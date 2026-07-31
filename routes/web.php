<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CorporateLeadController;
use App\Http\Controllers\Vendor\OnboardingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\DisputeController as CustomerDisputeController;
use App\Http\Controllers\Customer\BudgetMatchController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\MessageController as CustomerMessageController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboard;
use App\Http\Controllers\Vendor\ProfileController as VendorProfile;
use App\Http\Controllers\Vendor\HallController;
use App\Http\Controllers\Vendor\FloorController;
use App\Http\Controllers\Vendor\HallUnitController;
use App\Http\Controllers\Vendor\ServiceListingController;
use App\Http\Controllers\Vendor\BookingResponseController;
use App\Http\Controllers\Vendor\CalendarController;
use App\Http\Controllers\Vendor\InquiryController as VendorInquiryController;
use App\Http\Controllers\Vendor\MessageController as VendorMessageController;
use App\Http\Controllers\Vendor\ExtraServiceController as VendorExtraServiceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Admin\BookingVerificationController;
use App\Http\Controllers\Admin\DisputeController as AdminDisputeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\CorporateLeadController as AdminCorporateLeadController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\ExtraServiceController as AdminExtraServiceController;

Route::get('/', function () {
    $stats = [
        'vendors'  => \App\Models\VendorProfile::count(),
        'listings' => \App\Models\ServiceListing::count(),
        'bookings' => \App\Models\Booking::count(),
        'reviews'  => \App\Models\Review::count(),
    ];
    $featuredHalls = \App\Models\Hall::with('vendorProfile', 'hallUnits')
        ->take(4)->get();
    $featuredListings = \App\Models\ServiceListing::with('vendorProfile', 'serviceCategory')
        ->take(4)->get();
    $testimonials = \App\Models\Review::with('customer', 'vendorProfile')
        ->latest()->take(4)->get();
    return view('welcome', compact('stats', 'featuredHalls', 'featuredListings', 'testimonials'));
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->name('verification.send');
});

Route::controller(BrowseController::class)->group(function () {
    Route::get('/browse', 'index')->name('browse.index');
    Route::get('/browse/category/{slug}', 'category')->name('browse.category');
    Route::get('/browse/hall/{hall}', 'hallDetail')->name('browse.hall');
    Route::get('/browse/listing/{listing}', 'listingDetail')->name('browse.listing');
    Route::get('/search', 'search')->name('browse.search');
    Route::get('/packages', 'packages')->name('browse.packages');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::prefix('blog')->name('blog.')->controller(BlogController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{blogPost}', 'show')->name('show');
});

Route::post('/inquiry', [\App\Http\Controllers\InquiryController::class, 'store'])->name('inquiry.store')->middleware('throttle:5,60');

Route::controller(CorporateLeadController::class)->group(function () {
    Route::get('/corporate-inquiry', 'create')->name('corporate.leads.create');
    Route::post('/corporate-inquiry', 'store')->name('corporate.leads.store');
})->middleware('throttle:3,60');

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::post('/cart/remove/{key}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [CustomerBookingController::class, 'checkout'])->name('checkout');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/bookings/{booking}/pay', [CustomerPaymentController::class, 'showPayment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/pay/intent', [CustomerPaymentController::class, 'createIntent'])->name('payments.intent');
    Route::post('/bookings/{booking}/pay/confirm', [CustomerPaymentController::class, 'confirmPayment'])->name('payments.confirm');
    Route::post('/bookings/{booking}/pay/manual', [CustomerPaymentController::class, 'manualPayment'])->name('payments.manual');

    Route::get('/bookings/{booking}/messages', [CustomerMessageController::class, 'index'])->name('messages.index');
    Route::post('/bookings/{booking}/messages', [CustomerMessageController::class, 'store'])->name('messages.store');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/disputes', [CustomerDisputeController::class, 'store'])->name('disputes.store');

    Route::get('/budget-match', [BudgetMatchController::class, 'index'])->name('budget.match.index');
    Route::post('/budget-match', [BudgetMatchController::class, 'match'])->name('budget.match');
});

Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboard::class, 'index'])->name('dashboard');

    Route::get('/profile', [VendorProfile::class, 'index'])->name('profile.create');
    Route::post('/profile', [VendorProfile::class, 'store'])->name('profile.store');
    Route::put('/profile/{vendorProfile}', [VendorProfile::class, 'update'])->name('profile.update');

    Route::get('/halls', [HallController::class, 'index'])->name('halls.index');
    Route::post('/halls', [HallController::class, 'store'])->name('halls.store');
    Route::put('/halls/{hall}', [HallController::class, 'update'])->name('halls.update');
    Route::delete('/halls/{hall}', [HallController::class, 'destroy'])->name('halls.destroy');
    Route::post('/halls/{hall}/images', [HallController::class, 'uploadImage'])->name('halls.images.upload');
    Route::delete('/halls/images/{hallImage}', [HallController::class, 'deleteImage'])->name('halls.images.delete');

    Route::get('/halls/{hall}/floors', [FloorController::class, 'index'])->name('halls.floors.index');
    Route::post('/halls/{hall}/floors', [FloorController::class, 'store'])->name('halls.floors.store');
    Route::put('/floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
    Route::delete('/floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');

    Route::get('/halls/{hall}/units', [HallUnitController::class, 'index'])->name('halls.units.index');
    Route::post('/halls/{hall}/units', [HallUnitController::class, 'store'])->name('halls.units.store');
    Route::put('/hall-units/{hallUnit}', [HallUnitController::class, 'update'])->name('hall-units.update');
    Route::delete('/hall-units/{hallUnit}', [HallUnitController::class, 'destroy'])->name('hall-units.destroy');

    Route::get('/halls/{hall}/units/{unit}/extras', [VendorExtraServiceController::class, 'index'])->name('halls.units.extras.index');
    Route::post('/halls/{hall}/units/{unit}/extras', [VendorExtraServiceController::class, 'store'])->name('halls.units.extras.store');
    Route::delete('/extra-services/{extraService}', [VendorExtraServiceController::class, 'destroy'])->name('extra-services.destroy');

    Route::get('/listings', [ServiceListingController::class, 'index'])->name('listings.index');
    Route::post('/listings', [ServiceListingController::class, 'store'])->name('listings.store');
    Route::put('/listings/{serviceListing}', [ServiceListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{serviceListing}', [ServiceListingController::class, 'destroy'])->name('listings.destroy');

    Route::get('/bookings', [BookingResponseController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{bookingItem}/respond', [BookingResponseController::class, 'respond'])->name('bookings.respond');

    Route::get('/bookings/{booking}/messages', [VendorMessageController::class, 'index'])->name('messages.index');
    Route::post('/bookings/{booking}/messages', [VendorMessageController::class, 'store'])->name('messages.store');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/slots', [CalendarController::class, 'getSlots'])->name('calendar.slots');
    Route::post('/calendar/block', [CalendarController::class, 'blockSlot'])->name('calendar.block');
    Route::post('/calendar/unblock', [CalendarController::class, 'unblockSlot'])->name('calendar.unblock');

    Route::get('/inquiries', [VendorInquiryController::class, 'index'])->name('inquiries.index');
    Route::put('/inquiries/{inquiry}', [VendorInquiryController::class, 'updateStatus'])->name('inquiries.update');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/vendors', [VendorVerificationController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/pending', [VendorVerificationController::class, 'pending'])->name('vendors.pending');
    Route::get('/vendors/{vendorProfile}', [VendorVerificationController::class, 'show'])->name('vendors.show');
    Route::get('/vendors/{vendorProfile}/edit', [VendorVerificationController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{vendorProfile}', [VendorVerificationController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{vendorProfile}', [VendorVerificationController::class, 'destroy'])->name('vendors.destroy');
    Route::post('/vendors/{vendorProfile}/verify', [VendorVerificationController::class, 'verify'])->name('vendors.verify');
    Route::post('/vendors/{vendorProfile}/suspend', [VendorVerificationController::class, 'suspend'])->name('vendors.suspend');
    Route::post('/vendors/halls/{hall}/images', [VendorVerificationController::class, 'uploadHallImage'])->name('vendors.halls.images.upload');
    Route::delete('/vendors/halls/images/{hallImage}', [VendorVerificationController::class, 'deleteHallImage'])->name('vendors.halls.images.delete');
    Route::post('/halls/{hall}/floors', [VendorVerificationController::class, 'storeFloor'])->name('halls.floors.store');
    Route::delete('/floors/{floor}', [VendorVerificationController::class, 'deleteFloor'])->name('floors.destroy');
    Route::post('/halls/{hall}/units', [VendorVerificationController::class, 'storeUnit'])->name('halls.units.store');
    Route::delete('/hall-units/{hallUnit}', [VendorVerificationController::class, 'deleteUnit'])->name('hall-units.destroy');

    Route::get('/bookings', [BookingVerificationController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingVerificationController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/verify', [BookingVerificationController::class, 'verify'])->name('bookings.verify');
    Route::post('/bookings/{booking}/confirm', [BookingVerificationController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [BookingVerificationController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [BookingVerificationController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/payment', [BookingVerificationController::class, 'recordPayment'])->name('bookings.payment');
    Route::get('/bookings/{booking}/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::post('/bookings/{booking}/messages', [AdminMessageController::class, 'store'])->name('messages.store');

    Route::get('/halls/{hall}/units/{unit}/extras', [AdminExtraServiceController::class, 'index'])->name('halls.units.extras.index');
    Route::post('/halls/{hall}/units/{unit}/extras', [AdminExtraServiceController::class, 'store'])->name('halls.units.extras.store');
    Route::delete('/extra-services/{extraService}', [AdminExtraServiceController::class, 'destroy'])->name('extra-services.destroy');

    Route::get('/disputes', [AdminDisputeController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{dispute}', [AdminDisputeController::class, 'show'])->name('disputes.show');
    Route::post('/disputes/{dispute}/resolve', [AdminDisputeController::class, 'resolve'])->name('disputes.resolve');
    Route::post('/disputes/{dispute}/reject', [AdminDisputeController::class, 'reject'])->name('disputes.reject');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{serviceCategory}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{serviceCategory}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/leads', [AdminCorporateLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{corporateLead}', [AdminCorporateLeadController::class, 'show'])->name('leads.show');
    Route::post('/leads/{corporateLead}/status', [AdminCorporateLeadController::class, 'updateStatus'])->name('leads.status');
    Route::delete('/leads/{corporateLead}', [AdminCorporateLeadController::class, 'destroy'])->name('leads.destroy');

    Route::resource('/blog', \App\Http\Controllers\Admin\BlogController::class)->except('show')->names('blog');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
