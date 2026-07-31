<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Hall;
use App\Models\Floor;
use App\Models\HallUnit;
use App\Models\ServiceListing;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Dispute;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\CorporateLead;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'slug');

        // ====================================================================
        // 1. USERS (25)
        // ====================================================================
        // Customers (10)
        $customers = [];
        $customerNames = [
            ['Ahmed Ali', '03001234501'], ['Sana Khan', '03001234502'], ['Bilal Hussain', '03001234503'],
            ['Fatima Noor', '03001234504'], ['Hassan Raza', '03001234505'], ['Ayesha Malik', '03001234506'],
            ['Usman Cheema', '03001234507'], ['Zainab Iqbal', '03001234508'], ['Omar Farooq', '03001234509'],
            ['Hira Shah', '03001234510'],
        ];
        foreach ($customerNames as $i => [$name, $phone]) {
            $customers[] = User::create([
                'name' => $name,
                'email' => 'customer' . ($i + 1) . '@beegevents.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => $phone,
                'email_verified_at' => now(),
            ]);
        }

        // Vendors (12)
        $vendorData = [
            ['The Grand Marquee', 'hall', 'Lahore', '03001234001', 'vendor1'],
            ['Pearl Continental Venues', 'hall', 'Lahore', '03001234002', 'vendor2'],
            ['Islamabad Marquee Services', 'hall', 'Islamabad', '03001234003', 'vendor3'],
            ['Karachi Banquet Halls', 'hall', 'Karachi', '03001234004', 'vendor4'],
            ['Floral Dreams Decor', 'decor', 'Lahore', '03001234005', 'vendor5'],
            ['Elegance Decorators', 'decor', 'Islamabad', '03001234006', 'vendor6'],
            ['Royal Catering Services', 'catering', 'Lahore', '03001234007', 'vendor7'],
            ['Taste Buds Catering', 'catering', 'Karachi', '03001234008', 'vendor8'],
            ['Lens & Light Photography', 'photography', 'Lahore', '03001234009', 'vendor9'],
            ['Captured Moments', 'photography', 'Islamabad', '03001234010', 'vendor10'],
            ['DJ Rythms Pakistan', 'dj', 'Lahore', '03001234011', 'vendor11'],
            ['Luxury Car Hire', 'car', 'Lahore', '03001234012', 'vendor12'],
        ];

        $vendorProfiles = [];
        foreach ($vendorData as $i => [$business, $type, $city, $phone, $email]) {
            $user = User::create([
                'name' => explode(' ', $business)[0] . ' Owner',
                'email' => $email . '@beegevents.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => $phone,
                'email_verified_at' => now(),
            ]);
            $vendorProfiles[] = VendorProfile::create([
                'user_id' => $user->id,
                'business_name' => $business,
                'vendor_type' => $type,
                'city' => $city,
                'status' => 'verified',
                'cancellation_policy' => 'Full refund if cancelled 7 days before the event. 50% refund if cancelled 3-7 days before. No refund within 3 days.',
            ]);
        }

        // ====================================================================
        // 2. HALLS (15) — vendors 0-3 are hall vendors
        // ====================================================================
        $hallVendorIds = [0, 1, 2, 3];
        $hallNames = [
            ['Shalimar Garden Banquet', '42 Main Boulevard, Gulberg, Lahore', true],
            ['Pearl Continental Lawn', 'Shahrah-e-Quaid-e-Azam, Gulberg, Lahore', true],
            ['Islamabad Serena Pavilion', 'Khayaban-e-Suharwardy, F-7, Islamabad', true],
            ['DHA Phase 2 Banquet Hall', 'DHA Phase 2, Khayaban-e-Badar, Karachi', false],
            ['The Royal Palm Lahore', '78-A Main Boulevard, Gulberg, Lahore', true],
            ['Marquee Garden Lahore', '23 Canal Road, Lahore', false],
            ['Margalla Hills Banquet', 'G-11 Markaz, Islamabad', true],
            ['Clifton Community Hall', 'Khayaban-e-Ittehad, Clifton, Karachi', false],
            ['Grand Jamia Complex', 'Shahdara, Lahore', true],
            ['Lake View Pavilion', 'Murree Road, Bhara Kahu, Islamabad', false],
            ['Bahria Town Phase 7 Hall', 'Jinnah Avenue, Bahria Town, Lahore', true],
            ['Gulshan-e-Maymar Banquet', 'Main University Road, Gulshan, Karachi', false],
            ['Haveli Heritage Lahore', 'Food Street, Gawalmandi, Lahore', true],
            ['Saidpur Village Pavilion', 'Saidpur Village, Islamabad', false],
            ['Khayaban Hall, DHA', 'Khayaban-e-Tufail, DHA, Karachi', false],
        ];

        $hallModels = [];
        foreach ($hallNames as $i => [$name, $address, $hasFloors]) {
            $vi = $hallVendorIds[$i % count($hallVendorIds)];
            $hallModels[] = Hall::create([
                'vendor_profile_id' => $vendorProfiles[$vi]->id,
                'name' => $name,
                'address' => $address,
                'has_floors' => $hasFloors,
            ]);
        }

        // ====================================================================
        // 3. FLOORS (20+) — for halls with has_floors = true
        // ====================================================================
        $floorLabels = ['Ground Floor', 'First Floor', 'Basement Hall', 'Terrace Garden'];
        $floorModels = [];
        foreach ($hallModels as $hall) {
            if ($hall->has_floors) {
                $count = rand(2, 3);
                for ($f = 0; $f < $count; $f++) {
                    $floorModels[$hall->id][] = Floor::create([
                        'hall_id' => $hall->id,
                        'floor_label' => $floorLabels[$f] ?? 'Floor ' . ($f + 1),
                    ]);
                }
            }
        }

        // ====================================================================
        // 4. HALL UNITS (55+)
        // ====================================================================
        $unitNames = ['Main Hall', 'Small Hall', 'Lawn', 'VIP Room', 'Garden', 'Party Hall', 'Banquet Room', 'Conference Hall', 'Family Hall', 'Grand Lawn'];
        $decorTypes = ['fixed', 'outsourced', 'customizable'];
        $hallUnitIds = [];

        foreach ($hallModels as $hall) {
            $unitsCount = rand(3, 5);
            $floorsForHall = $floorModels[$hall->id] ?? [];
            for ($u = 0; $u < $unitsCount; $u++) {
                $floorId = count($floorsForHall) > 0 ? $floorsForHall[$u % count($floorsForHall)]->id : null;
                $unit = HallUnit::create([
                    'hall_id' => $hall->id,
                    'floor_id' => $floorId,
                    'unit_name' => $unitNames[$u % count($unitNames)],
                    'min_capacity' => rand(50, 200),
                    'max_capacity' => rand(200, 1500),
                    'menu_summary' => 'Pakistani, Chinese, and Continental menu options available.',
                    'decor_type' => $decorTypes[$u % 3],
                    'base_price' => rand(15, 150) * 10000,
                ]);
                $hallUnitIds[] = $unit->id;
            }
        }

        // ====================================================================
        // 5. SERVICE LISTINGS (35+)
        // ====================================================================
        $serviceTemplates = [
            ['decor', 'Floral Arrangement Premium', 50000, 'fixed'],
            ['decor', 'Stage Decoration Classic', 35000, 'fixed'],
            ['decor', 'Lighting Setup Deluxe', 45000, 'fixed'],
            ['decor', 'Aisle & Entrance Decor', 25000, 'fixed'],
            ['decor', 'Ceiling Draping Service', 30000, 'fixed'],
            ['decor', 'Wedding Stage Theme Setup', 85000, 'fixed'],
            ['catering', 'Standard Menu per Person', 1500, 'per_person'],
            ['catering', 'Premium Menu per Person', 2800, 'per_person'],
            ['catering', 'BBQ Package per Person', 2000, 'per_person'],
            ['catering', 'Dessert Station Setup', 25000, 'fixed'],
            ['catering', 'Continental Menu per Person', 3200, 'per_person'],
            ['catering', 'Chaat & Appetizer Station', 18000, 'fixed'],
            ['photography', 'Pre-Wedding Shoot', 30000, 'fixed'],
            ['photography', 'Wedding Day Coverage', 65000, 'fixed'],
            ['photography', 'Cinematic Highlight Reel', 50000, 'fixed'],
            ['photography', 'Engagement Shoot', 25000, 'fixed'],
            ['photography', 'Albums & Prints Deluxe', 18000, 'fixed'],
            ['photography', 'Drone Aerial Coverage', 35000, 'fixed'],
            ['dj', 'Basic Sound System', 25000, 'fixed'],
            ['dj', 'Premium DJ Package', 50000, 'fixed'],
            ['dj', 'Live Band Performance', 75000, 'fixed'],
            ['dj', 'Dhol Players (2 pcs)', 15000, 'fixed'],
            ['dj', 'Full Sound + Light Setup', 80000, 'fixed'],
            ['car', 'Toyota Corolla Wedding Car', 15000, 'fixed'],
            ['car', 'Mercedes S-Class Hire', 50000, 'fixed'],
            ['car', 'BMW 7 Series Luxury', 45000, 'fixed'],
            ['car', 'Wedding Coach (20 seater)', 35000, 'fixed'],
            ['car', 'Vintage Car (Beetle/Classic)', 25000, 'fixed'],
            ['car', 'SUV Fleet (4x4) for Baraat', 80000, 'fixed'],
        ];

        $listingIds = [];
        $decorVendorIds = [4, 5];   // Floral Dreams, Elegance
        $cateringVendorIds = [6, 7]; // Royal Catering, Taste Buds
        $photoVendorIds = [8, 9];    // Lens & Light, Captured Moments
        $djVendorIds = [10];
        $carVendorIds = [11];

        $vendorByCat = [
            'decor' => $decorVendorIds,
            'catering' => $cateringVendorIds,
            'photography' => $photoVendorIds,
            'dj' => $djVendorIds,
            'car' => $carVendorIds,
        ];

        foreach ($serviceTemplates as [$catSlug, $title, $price, $unit]) {
            $catId = $categories[$catSlug] ?? null;
            if (!$catId) continue;
            $vIds = $vendorByCat[$catSlug] ?? [];
            if (count($vIds) === 0) continue;
            $vId = $vendorProfiles[$vIds[array_rand($vIds)]]->id;
            $listing = ServiceListing::create([
                'vendor_profile_id' => $vId,
                'service_category_id' => $catId,
                'title' => $title,
                'description' => "Professional $title service for your event. Quality assured with verified reviews.",
                'price' => $price,
                'price_unit' => $unit,
            ]);
            $listingIds[] = $listing->id;
        }

        // ====================================================================
        // 6. BOOKINGS (75)
        // ====================================================================
        $statuses = ['requested', 'discussing', 'verified', 'confirmed', 'completed', 'cancelled'];
        $eventTypes = ['wedding', 'engagement', 'corporate', 'birthday'];

        $bookingIds = [];
        for ($b = 1; $b <= 75; $b++) {
            $customer = $customers[array_rand($customers)];
            $eventType = $eventTypes[array_rand($eventTypes)];
            $status = $statuses[array_rand($statuses)];

            // Earlier dates for completed/confirmed, future for requested
            $daysOffset = $status === 'completed' ? rand(-90, -10) : ($status === 'confirmed' ? rand(-5, 15) : rand(-30, 45));
            $eventDate = now()->addDays($daysOffset);

            $budget = rand(2, 50) * 100000;
            $totalPrice = $budget - rand(0, 2) * 50000;

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'booking_type' => 'multi',
                'event_date' => $eventDate,
                'event_type' => $eventType,
                'status' => $status,
                'budget_input' => $budget,
                'total_price' => $totalPrice,
                'commission_amount' => round($totalPrice * 0.05),
                'notes' => $b % 5 === 0 ? 'Please arrange extra chairs and vegetarian options.' : null,
            ]);
            $bookingIds[] = $booking->id;

            // Booking Items (1-4 per booking)
            $itemsCount = rand(1, min(4, 1 + ($b % 3)));
            for ($bi = 0; $bi < $itemsCount; $bi++) {
                if (rand(0, 1) === 0 && count($hallUnitIds) > 0) {
                    $unitId = $hallUnitIds[array_rand($hallUnitIds)];
                    $unit = \App\Models\HallUnit::with('hall')->find($unitId);
                    if ($unit) {
                        $vpId = $unit->hall->vendor_profile_id;
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'itemable_type' => 'App\Models\HallUnit',
                            'itemable_id' => $unitId,
                            'vendor_profile_id' => $vpId,
                            'price' => $unit->base_price,
                            'vendor_status' => $status === 'cancelled' ? 'declined' : ($status === 'completed' || $status === 'confirmed' ? 'accepted' : (rand(0, 1) ? 'accepted' : 'pending')),
                        ]);
                    }
                } elseif (count($listingIds) > 0) {
                    $lid = $listingIds[array_rand($listingIds)];
                    $listing = \App\Models\ServiceListing::find($lid);
                    if ($listing) {
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'itemable_type' => 'App\Models\ServiceListing',
                            'itemable_id' => $lid,
                            'vendor_profile_id' => $listing->vendor_profile_id,
                            'price' => $listing->price,
                            'vendor_status' => $status === 'cancelled' ? 'declined' : ($status === 'completed' || $status === 'confirmed' ? 'accepted' : (rand(0, 1) ? 'accepted' : 'pending')),
                        ]);
                    }
                }
            }
        }

        // ====================================================================
        // 7. PAYMENTS (for completed/confirmed bookings)
        // ====================================================================
        $completedBookings = Booking::whereIn('status', ['completed', 'confirmed'])->get();
        foreach ($completedBookings as $booking) {
            Payment::create([
                'booking_id' => $booking->id,
                'type' => 'advance',
                'amount' => round($booking->total_price * (rand(30, 50) / 100)),
                'method' => 'manual',
                'status' => 'received',
                'received_by' => 1,
            ]);
            if ($booking->status === 'completed') {
                Payment::create([
                    'booking_id' => $booking->id,
                    'type' => 'balance',
                    'amount' => $booking->total_price - round($booking->total_price * 0.4),
                    'method' => 'manual',
                    'status' => 'received',
                    'received_by' => 1,
                ]);
            }
        }

        // ====================================================================
        // 8. REVIEWS (for completed bookings)
        // ====================================================================
        $completedOnly = Booking::where('status', 'completed')->get();
        $comments = [
            'Amazing service! Everything was perfect on the day.',
            'Very professional team. Highly recommended!',
            'Good experience overall. There was a slight delay in setup.',
            'Excellent venue and wonderful coordination. Will book again.',
            'The decoration was stunning. Exactly what we wanted.',
            'Food was delicious and the staff was very courteous.',
            'Great value for money. The hall looked beautiful.',
            'Smooth planning process and excellent execution.',
            'Very happy with the service. Five stars!',
            'Could have been better with communication, but end result was good.',
        ];
        foreach ($completedOnly as $booking) {
            $items = $booking->bookingItems;
            $vpsSeen = [];
            foreach ($items as $item) {
                if (in_array($item->vendor_profile_id, $vpsSeen)) continue;
                $vpsSeen[] = $item->vendor_profile_id;
                Review::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'vendor_profile_id' => $item->vendor_profile_id,
                    'rating' => rand(3, 5),
                    'comment' => $comments[array_rand($comments)],
                ]);
            }
        }

        // ====================================================================
        // 9. DISPUTES (8)
        // ====================================================================
        $cancelledBookings = Booking::where('status', 'cancelled')->take(8)->get();
        foreach ($cancelledBookings as $booking) {
            Dispute::create([
                'booking_id' => $booking->id,
                'raised_by' => $booking->customer_id,
                'reason' => 'The vendor did not deliver the agreed services. We had to cancel last minute and are requesting a refund.',
                'status' => rand(0, 2) === 0 ? 'resolved' : 'open',
                'resolution_notes' => rand(0, 1) ? 'Refund processed after reviewing the case.' : null,
            ]);
        }

        // ====================================================================
        // 10. PACKAGES (6)
        // ====================================================================
        $packagesData = [
            ['Silver Wedding Package', 'Complete wedding package with hall, decor, and basic catering.', 450000, 'wedding'],
            ['Gold Wedding Package', 'Premium wedding with deluxe decor, premium catering, and photography.', 850000, 'wedding'],
            ['Engagement Special', 'Perfect engagement package with small hall, decor, and DJ.', 200000, 'engagement'],
            ['Corporate Event Package', 'Full corporate event solution with conference setup and catering.', 350000, 'corporate'],
            ['Birthday Bash', 'Complete birthday party package with decor and catering.', 150000, 'birthday'],
            ['Platinum Wedding', 'Ultimate luxury wedding — all services included.', 1500000, 'wedding'],
        ];

        foreach ($packagesData as [$title, $desc, $price, $type]) {
            Package::create([
                'title' => $title,
                'description' => $desc,
                'total_price' => $price,
                'event_type' => $type,
            ]);
        }

        // ====================================================================
        // 11. CORPORATE LEADS (12)
        // ====================================================================
        $companies = [
            ['TechCorp Solutions', 'Usman Ahmed', 'usman@techcorp.com', '03002001001', 'Need venue for annual dinner — 500 people in Lahore.', 'new'],
            ['Al-Falah Group', 'Hassan Iqbal', 'hassan@alfalah.com', '03002001002', 'Quarterly business conference in Islamabad, 200 attendees.', 'contacted'],
            ['Crescent Textiles', 'Fatima Aslam', 'fatima@crescent.com', '03002001003', 'Product launch event in Karachi, expecting 300 guests.', 'new'],
            ['Punjab Healthcare', 'Dr. Ahmed Khan', 'dr.ahmed@phc.com', '03002001004', 'Medical conference for 400 doctors in Lahore.', 'converted'],
            ['Digital Pakistan', 'Sara Zafar', 'sara@digitalpak.com', '03002001005', 'Tech summit for 1000 people in convention hall.', 'new'],
            ['Bank Alfalah Ltd', 'Kamran Shah', 'kamran@bankalfalah.com', '03002001006', 'Annual customer appreciation dinner for 600 guests.', 'contacted'],
            ['Shalimar Foods', 'Rizwan Ali', 'rizwan@shalimar.com', '03002001007', 'New product tasting event, 150 guests.', 'new'],
            ['Pakistan Telecom', 'Nadia Khan', 'nadia@ptcl.com', '03002001008', 'Employee awards ceremony in Islamabad.', 'closed'],
            ['Lahore Grammar School', 'Prof. Akram', 'akram@lgs.edu.pk', '03002001009', 'Annual sports gala and prize distribution.', 'new'],
            ['Fashion Pakistan', 'Zara Tariq', 'zara@fp.com', '03002001010', 'Fashion show and dinner for 400 guests.', 'new'],
            ['Khan Developers', 'Ali Khan', 'ali@khandevelopers.com', '03002001011', 'Property launch ceremony and dinner.', 'contacted'],
            ['Sufi Traders', 'Imran Sufi', 'imran@sufitraders.com', '03002001012', 'Export conference with international delegates.', 'new'],
        ];

        foreach ($companies as [$company, $person, $email, $phone, $notes, $status]) {
            CorporateLead::create([
                'company_name' => $company,
                'contact_person' => $person,
                'email' => $email,
                'phone' => $phone,
                'requirement_notes' => $notes,
                'status' => $status,
            ]);
        }
    }
}
