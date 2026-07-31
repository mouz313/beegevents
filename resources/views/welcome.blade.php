@extends('layouts.app')

@section('title', config('app.name', 'BeeG Events') . ' — Plan Your Perfect Event')

@section('meta_description', 'Pakistan\'s trusted event planning platform. Find verified halls, farmhouses, decor, catering, photography, and more for your perfect event.')
@section('meta_keywords', 'event planning, wedding halls, farmhouses, decor, catering, photography, Pakistan events, BeeG Events')
@section('og_title', config('app.name', 'BeeG Events') . ' — Plan Your Perfect Event')
@section('og_description', 'Pakistan\'s trusted event planning platform connecting customers with verified venues and services.')

@section('hero')
<section class="hero-section" data-animate>
    <div class="hero-carousel">
        <div class="hero-slide active">
            <div class="slide-bg"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="slide-inner">
                <div class="eyebrow">
                    <i class="ti ti-shield-check-filled"></i> Trusted vendors, verified bookings
                </div>
                <h1>Plan Your Perfect<br><span>Event</span> in Minutes</h1>
                <p class="subtext">
                    Pakistan's most trusted platform for weddings, engagements, and corporate events.
                    Browse verified venues and services, match your budget, and book with confidence.
                </p>
                <div class="cta-group">
                    @auth
                        <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn-primary">
                            <i class="ti ti-dashboard"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">
                            <i class="ti ti-sparkles"></i> Plan My Event
                        </a>
                        <a href="{{ route('register') }}?role=vendor" class="btn-secondary">
                            <i class="ti ti-building-store"></i> List Your Business
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="hero-slide">
            <div class="slide-bg"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="slide-inner">
                <div class="eyebrow">
                    <i class="ti ti-building-store"></i> 12+ verified vendors across Pakistan
                </div>
                <h1>Find Trusted<br><span>Vendors</span> for Your Event</h1>
                <p class="subtext">
                    From banquet halls to caterers, photographers to decorators — discover top-rated
                    vendors with real reviews and transparent pricing.
                </p>
                <div class="cta-group">
                    <a href="{{ route('browse.index') }}" class="btn-primary">
                        <i class="ti ti-building-arch"></i> Browse Vendors
                    </a>
                    <a href="{{ route('register') }}?role=vendor" class="btn-secondary">
                        <i class="ti ti-building-store"></i> Join as Vendor
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-slide">
            <div class="slide-bg"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="slide-inner">
                <div class="eyebrow">
                    <i class="ti ti-shield-check-filled"></i> Every booking admin-verified
                </div>
                <h1>Book With<br><span>Confidence</span>, Every Time</h1>
                <p class="subtext">
                    Our team reviews and confirms every booking. Real-time availability, secure payments,
                    and dedicated support — so you can focus on celebrating.
                </p>
                <div class="cta-group">
                    @auth
                        <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn-primary">
                            <i class="ti ti-dashboard"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">
                            <i class="ti ti-sparkles"></i> Get Started Free
                        </a>
                        <a href="{{ route('login') }}" class="btn-secondary">
                            <i class="ti ti-login"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <div class="hero-dots">
        <button class="active" data-slide="0"></button>
        <button data-slide="1"></button>
        <button data-slide="2"></button>
    </div>
</section>
@endsection

@section('content')
@if($stats['vendors'] > 0 || $stats['listings'] > 0)
<section class="stats-bar" data-animate>
    <div class="stats-grid">
        <div class="stat-item" data-animate data-delay="100">
            <i class="ti ti-building-store"></i>
            <span class="stat-number">{{ $stats['vendors'] }}</span>
            <span class="stat-label">Vendors</span>
        </div>
        <div class="stat-item" data-animate data-delay="200">
            <i class="ti ti-list-check"></i>
            <span class="stat-number">{{ $stats['listings'] }}</span>
            <span class="stat-label">Listings</span>
        </div>
        <div class="stat-item" data-animate data-delay="300">
            <i class="ti ti-calendar-event"></i>
            <span class="stat-number">{{ $stats['bookings'] }}</span>
            <span class="stat-label">Bookings</span>
        </div>
        <div class="stat-item" data-animate data-delay="400">
            <i class="ti ti-star-filled"></i>
            <span class="stat-number">{{ $stats['reviews'] }}</span>
            <span class="stat-label">Reviews</span>
        </div>
    </div>
</section>
@endif

<section class="search-section" data-animate>
    <div class="search-section-inner">
        <div class="search-header">
            <i class="ti ti-calendar-search"></i>
            <h3>Check Availability — Find Your Perfect Venue</h3>
            <p>Pick a date, type what you're looking for, and see what's available.</p>
        </div>
        <form class="search-row" action="{{ route('browse.search') }}" method="GET">
            <div class="sr-field">
                <label>Event Date</label>
                <div class="sr-input-wrap">
                    <i class="ti ti-calendar-event"></i>
                    <input type="date" name="date" required>
                </div>
            </div>
            <div class="sr-field sr-field-grow">
                <label>Search</label>
                <div class="sr-input-wrap">
                    <i class="ti ti-search"></i>
                    <input type="text" name="q" placeholder="Hall name, vendor, or service...">
                </div>
            </div>
            <div class="sr-field">
                <label>Event Type</label>
                <div class="sr-input-wrap">
                    <i class="ti ti-category"></i>
                    <select name="event_type">
                        <option value="">All Events</option>
                        <option value="wedding">Wedding</option>
                        <option value="engagement">Engagement</option>
                        <option value="corporate">Corporate</option>
                        <option value="birthday">Birthday</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="sr-btn">
                <i class="ti ti-search"></i> Search
            </button>
        </form>
    </div>
</section>

@php
    $catIcons = ['hall' => 'ti-building-arch', 'decor' => 'ti-flower', 'catering' => 'ti-kitchen', 'photography' => 'ti-camera', 'dj' => 'ti-music', 'car' => 'ti-car'];
    $catDesc = ['hall' => 'Banquet halls, farmhouses, and marquees', 'decor' => 'Floral, lighting, and stage decoration', 'catering' => 'Food, beverages, and dessert services', 'photography' => 'Photo, video, and cinematic coverage', 'dj' => 'Sound systems, DJs, and live music', 'car' => 'Wedding cars, limos, and transport'];
    $catCounts = [
        'hall' => \App\Models\Hall::count(),
        'decor' => \App\Models\ServiceListing::whereIn('service_category_id', \App\Models\ServiceCategory::where('slug','decor')->pluck('id'))->count(),
        'catering' => \App\Models\ServiceListing::whereIn('service_category_id', \App\Models\ServiceCategory::where('slug','catering')->pluck('id'))->count(),
        'photography' => \App\Models\ServiceListing::whereIn('service_category_id', \App\Models\ServiceCategory::where('slug','photography')->pluck('id'))->count(),
        'dj' => \App\Models\ServiceListing::whereIn('service_category_id', \App\Models\ServiceCategory::where('slug','dj')->pluck('id'))->count(),
        'car' => \App\Models\ServiceListing::whereIn('service_category_id', \App\Models\ServiceCategory::where('slug','car')->pluck('id'))->count(),
    ];
@endphp
<section class="categories-section" data-animate>
    <div class="section-header">
        <h2>Everything You Need</h2>
        <p>From venues to decor, catering to photography — we've got you covered.</p>
    </div>
    <div class="category-grid">
        @foreach(['hall', 'decor', 'catering', 'photography', 'dj', 'car'] as $i => $slug)
            <a href="{{ route('browse.category', $slug) }}" class="category-card" data-animate data-delay="{{ $i * 100 }}">
                <div class="cat-icon"><i class="ti {{ $catIcons[$slug] }}"></i></div>
                <h4>{{ $slug == 'dj' ? 'DJ / Sound' : ucfirst(str_replace('_', ' ', $slug)) }}</h4>
                <p>{{ $catDesc[$slug] }}</p>
                <span class="cat-listing-count">{{ $catCounts[$slug] }} listed</span>
            </a>
        @endforeach
    </div>
</section>

@if($featuredHalls->count() > 0)
<section class="featured-section" data-animate>
    <div class="section-header">
        <h2><i class="ti ti-building-arch"></i> Featured Venues</h2>
        <a href="{{ route('browse.category', 'hall') }}">View All Halls <i class="ti ti-chevron-right"></i></a>
    </div>
    <div class="featured-grid">
        @foreach($featuredHalls as $i => $hall)
            <a href="{{ route('browse.hall', $hall) }}" class="featured-card" data-animate data-delay="{{ $i * 100 }}">
                <div class="fc-visual"><i class="ti ti-building-arch"></i></div>
                <div class="fc-body">
                    <span class="fc-badge hall">Venue</span>
                    <div class="fc-title">{{ $hall->name }}</div>
                    <div class="fc-vendor">
                        <i class="ti ti-building-store"></i> {{ $hall->vendorProfile->business_name ?? 'Vendor' }}
                    </div>
                    @if($hall->hallUnits->count() > 0)
                        <div class="fc-price">PKR {{ number_format($hall->hallUnits->min('base_price')) }}+</div>
                        <div class="fc-meta">
                            <i class="ti ti-users"></i> {{ $hall->hallUnits->min('min_capacity') }}-{{ $hall->hallUnits->max('max_capacity') }} guests
                            &middot; {{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() > 1 ? 's' : '' }}
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

@if($featuredListings->count() > 0)
<section class="featured-section" style="padding-top:0;" data-animate>
    <div class="section-header">
        <h2><i class="ti ti-list-check"></i> Top Services</h2>
        <a href="{{ route('browse.index') }}">View All Services <i class="ti ti-chevron-right"></i></a>
    </div>
    <div class="featured-grid">
        @foreach($featuredListings as $i => $listing)
            <a href="{{ route('browse.listing', $listing) }}" class="featured-card" data-animate data-delay="{{ $i * 100 }}">
                <div class="fc-visual"><i class="ti ti-list-check"></i></div>
                <div class="fc-body">
                    <span class="fc-badge service">{{ $listing->serviceCategory->name ?? 'Service' }}</span>
                    <div class="fc-title">{{ $listing->title }}</div>
                    <div class="fc-vendor">
                        <i class="ti ti-building-store"></i> {{ $listing->vendorProfile->business_name ?? 'Vendor' }}
                    </div>
                    <div class="fc-price">PKR {{ number_format($listing->price) }}</div>
                    <div class="fc-meta">
                        <i class="ti ti-clock"></i> per {{ str_replace('_', ' ', $listing->price_unit) }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="partner-strip" data-animate>
    <p>Trusted by leading event planners across Pakistan</p>
    <div class="partner-grid">
        <div class="partner-badge"><div class="pb-shape gold" style="background:var(--gold);"></div><span class="pb-label">HG</span></div>
        <div class="partner-badge"><div class="pb-shape amber" style="background:var(--amber);"></div><span class="pb-label">EL</span></div>
        <div class="partner-badge"><div class="pb-shape teal" style="background:var(--green);"></div><span class="pb-label">MC</span></div>
        <div class="partner-badge"><div class="pb-shape plum" style="background:#6B4C7A;"></div><span class="pb-label">WS</span></div>
        <div class="partner-badge"><div class="pb-shape coral" style="background:var(--red);"></div><span class="pb-label">GL</span></div>
    </div>
</section>

@if($testimonials->count() > 0)
<section class="testimonials-section" data-animate>
    <div class="section-header">
        <h2>What Our Customers Say</h2>
        <p>Real reviews from real events — see why Pakistan trusts BeeG.</p>
    </div>
    <div class="testimonials-grid">
        @foreach($testimonials as $i => $review)
            <div class="testimonial-card" data-animate data-delay="{{ $i * 100 }}">
                <div class="stars">
                    @for($s = 1; $s <= 5; $s++)
                        <i class="ti ti-star{{ $s <= $review->rating ? '-filled' : ' empty' }}"></i>
                    @endfor
                </div>
                <div class="comment">"{{ $review->comment ?? 'No comment' }}"</div>
                <div class="author">
                    <div class="avatar">{{ substr($review->customer->name ?? '?', 0, 1) }}</div>
                    <div>
                        <div class="name">{{ $review->customer->name ?? 'Anonymous' }}</div>
                        <div class="vendor-name">{{ $review->vendorProfile->business_name ?? '' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<section class="trust-strip" data-animate>
    <div class="trust-grid">
        <div class="trust-item" data-animate data-delay="100">
            <i class="ti ti-shield-check"></i>
            <h4>Admin-Verified Bookings</h4>
            <p>Every booking is reviewed and confirmed by our team to ensure quality and reliability.</p>
        </div>
        <div class="trust-item" data-animate data-delay="200">
            <i class="ti ti-clock"></i>
            <h4>Real-Time Availability</h4>
            <p>Check vendor calendars and book available slots instantly — no back-and-forth calls.</p>
        </div>
        <div class="trust-item" data-animate data-delay="300">
            <i class="ti ti-star"></i>
            <h4>Verified Reviews</h4>
            <p>Honest reviews from real customers help you choose the right vendor every time.</p>
        </div>
    </div>
</section>

<section class="how-it-works" id="how-it-works" data-animate>
    <div class="section-header">
        <h2>How It Works</h2>
        <p>Two simple flows — one for customers, one for vendors.</p>
    </div>
    <div class="how-grid">
        <div class="flow-card" data-animate data-delay="100">
            <div class="flow-label"><i class="ti ti-users"></i> For Customers</div>
            <h4>Plan Your Event</h4>
            <ul class="steps">
                <li>
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <strong>Request</strong>
                        <span>Browse venues and services, add items to your cart, and submit a booking request.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <strong>Discuss</strong>
                        <span>Vendors review and respond to your request. Discuss details and confirm availability.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <strong>Verify</strong>
                        <span>Admin verifies the booking and records the payment. Your booking is confirmed.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">4</div>
                    <div class="step-text">
                        <strong>Celebrate</strong>
                        <span>Everything is set. Enjoy your event and leave a review afterward!</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="flow-card" data-animate data-delay="200">
            <div class="flow-label"><i class="ti ti-building-store"></i> For Vendors</div>
            <h4>Grow Your Business</h4>
            <ul class="steps">
                <li>
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <strong>Register Free</strong>
                        <span>Create your vendor profile at no cost. No upfront fees or hidden charges.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <strong>Get Verified</strong>
                        <span>Admin reviews and verifies your profile. Your listings go live for customers.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <strong>Receive Bookings</strong>
                        <span>Customers find you, add your services to their cart, and send booking requests.</span>
                    </div>
                </li>
                <li>
                    <div class="step-num">4</div>
                    <div class="step-text">
                        <strong>Get Paid</strong>
                        <span>Respond to requests, confirm bookings, and receive payments securely.</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="final-cta" data-animate>
    <div class="cta-card">
        <h2>Ready to Plan Your Event?</h2>
        <p>Join thousands of happy customers who planned their perfect event with BeeG. It's free to get started.</p>
        <div class="cta-actions">
            @auth
                <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn-cta-primary">
                    <i class="ti ti-dashboard"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-cta-primary">
                    <i class="ti ti-sparkles"></i> Start Planning
                </a>
                <a href="{{ route('register') }}?role=vendor" class="btn-cta-secondary">
                    <i class="ti ti-building-store"></i> Become a Vendor
                </a>
            @endauth
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function() {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dots button');
    var current = 0;
    var interval;

    function goToSlide(index) {
        slides.forEach(function(s, i) { s.classList.toggle('active', i === index); });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === index); });
        current = index;
    }

    function nextSlide() { goToSlide((current + 1) % slides.length); }

    function startCarousel() { interval = setInterval(nextSlide, 5000); }

    if (slides.length > 1) {
        startCarousel();
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                clearInterval(interval);
                goToSlide(parseInt(this.getAttribute('data-slide')));
                startCarousel();
            });
        });
    }
})();
</script>
@endpush
