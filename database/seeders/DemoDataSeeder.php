<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Hall;
use App\Models\Floor;
use App\Models\HallUnit;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuSet;
use App\Models\ExtraService;
use App\Models\ServiceListing;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Dispute;
use App\Models\Package;
use App\Models\CorporateLead;
use App\Models\CorporateQuotation;
use App\Models\ServiceCategory;
use App\Models\AvailabilitySlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'slug');

        // ====================================================================
        // 1. USERS — all data is Lahore based
        // ====================================================================
        // Customers (20)
        $customers = [];
        $customerNames = [
            ['Ahmed Ali', '03001234501'], ['Sana Khan', '03001234502'], ['Bilal Hussain', '03001234503'],
            ['Fatima Noor', '03001234504'], ['Hassan Raza', '03001234505'], ['Ayesha Malik', '03001234506'],
            ['Usman Cheema', '03001234507'], ['Zainab Iqbal', '03001234508'], ['Omar Farooq', '03001234509'],
            ['Hira Shah', '03001234510'], ['Talha Anwar', '03001234511'], ['Mahnoor Ali', '03001234512'],
            ['Rayan Sheikh', '03001234513'], ['Khadija Tariq', '03001234514'], ['Adnan Qureshi', '03001234515'],
            ['Mariam Zafar', '03001234516'], ['Hamza Bhatti', '03001234517'], ['Sidra Javed', '03001234518'],
            ['Danish Mehmood', '03001234519'], ['Iqra Batool', '03001234520'],
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

        // Vendors (75) — all Lahore
        $hallVendorBusiness = [
            'The Grand Marquee', 'Pearl Continental Marquee', 'Royal Palm Marquee', 'Crystal Marquee Events',
            'Emerald Garden Marquee', 'Golden Gate Marquee', 'Imperial Marquee', 'Signature Marquee Halls',
            'Classic Marquee', 'Elite Marquee', 'Prestige Marquee', 'Crown Marquee', 'Majestic Marquee',
            'Sapphire Marquee', 'Elegance Marquee', 'Silver Star Marquee', 'Victoria Marquee', 'Haveli Marquee Lahore',
            'Al-Karam Marquee', 'Shalimar Marquee', 'Gulberg Marquee', 'Model Town Marquee', 'DHA Marquee',
            'Bahria Marquee', 'Canal View Marquee',
        ];
        $decorBusiness = [
            'Floral Dreams Decor', 'Elegance Decorators', 'Bloom Decor Studio', 'Rose Petal Decor',
            'Luxury Decor House', 'Twinkle Lights Decor', 'Desi Decor Hub', 'Golden Drape Decor',
            'Fairy Land Decor', 'Urban Decor Co', 'Silk & Satin Decor', 'Mehndi Decor Specialists',
        ];
        $cateringBusiness = [
            'Royal Catering Services', 'Taste Buds Catering', 'Lahore Food Catering', 'Mughal Dastarkhwan Catering',
            'Spice Route Catering', 'Gourmet Catering Co', 'Desi Kitchen Catering', 'Fine Dining Catering',
            'BBQ Masters Catering', 'Continental Catering Lahore', 'Biryani House Catering', 'Events Catering Hub',
        ];
        $photoBusiness = [
            'Lens & Light Photography', 'Captured Moments', 'Pixel Perfect Studio', 'Wedding Frames',
            'Focus Point Photography', 'Memory Lane Studio', 'Royal Photo Studio', 'Studio Nine Lahore',
            'Candid Vision', 'Golden Hour Photography',
        ];
        $djBusiness = [
            'DJ Rythms Pakistan', 'Beats & Lights', 'Sound Wave Events', 'Crown DJ Service',
            'Mega Sound Lahore', 'Bassline Entertainment', 'Stage & Sound Co', 'Disco Lahore DJs',
        ];
        $carBusiness = [
            'Luxury Car Hire', 'Baraat Cars Lahore', 'Royal Rides', 'Limousine Services PK',
            'Classic Car Rental', 'SUV Baraat Fleet', 'Wedding Wheels', 'Elite Auto Hire',
        ];

        $vendorData = [];
        foreach ($hallVendorBusiness as $name) {
            $vendorData[] = [$name, 'hall'];
        }
        foreach ($decorBusiness as $name) {
            $vendorData[] = [$name, 'decor'];
        }
        foreach ($cateringBusiness as $name) {
            $vendorData[] = [$name, 'catering'];
        }
        foreach ($photoBusiness as $name) {
            $vendorData[] = [$name, 'photography'];
        }
        foreach ($djBusiness as $name) {
            $vendorData[] = [$name, 'dj'];
        }
        foreach ($carBusiness as $name) {
            $vendorData[] = [$name, 'car'];
        }

        $vendorProfiles = [];
        foreach ($vendorData as $i => [$business, $type]) {
            $isHall = $type === 'hall';
            $user = User::create([
                'name' => explode(' ', $business)[0] . ' Owner',
                'email' => 'vendor' . ($i + 1) . '@beegevents.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => '03001234' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);
            $vendorProfiles[] = VendorProfile::create([
                'user_id' => $user->id,
                'business_name' => $business,
                'vendor_type' => $type,
                'city' => 'Lahore',
                'status' => 'verified',
                'min_capacity' => $isHall ? rand(50, 100) : null,
                'max_capacity' => $isHall ? rand(300, 1500) : null,
                'starting_price' => $isHall ? rand(15, 300) * 10000 : rand(15, 150) * 1000,
                'years_experience' => rand(2, 20),
                'cancellation_policy' => 'Full refund if cancelled 7 days before the event. 50% refund if cancelled 3-7 days before. No refund within 3 days.',
            ]);
        }

        // ====================================================================
        // 2. HALLS (120) — all Lahore
        // ====================================================================
        $lahoreAreas = [
            'Gulberg', 'DHA Phase 1', 'DHA Phase 2', 'DHA Phase 3', 'DHA Phase 4', 'DHA Phase 5', 'DHA Phase 6',
            'Bahria Town', 'Model Town', 'Johar Town', 'Wapda Town', 'Cantt', 'Liberty Market', 'MM Alam Road',
            'Jail Road', 'Ferozepur Road', 'Raiwind Road', 'Canal Road', 'Multan Road', 'Main Boulevard',
            'Township', 'Allama Iqbal Town', 'Garden Town', 'Samanabad', 'Mozang', 'Ichhra', 'Shadman',
            'Faisal Town', 'Valencia Town', 'Lake City', 'Bedian Road', 'Barki Road', 'Thokar Niaz Baig',
            'Kacha Jail Road', 'Cavalry Ground', 'Peco Road', 'Muslim Town', 'Wahdat Road', 'Kareem Block',
            'Green Town', 'Mustafa Town', 'Green Valley', 'LDA Avenue', 'DHA Eme Zone', 'DHA Rahbar',
        ];
        $namePrefixes = [
            'Royal', 'Grand', 'Imperial', 'Crystal', 'Emerald', 'Golden', 'Signature', 'Classic',
            'Elite', 'Prestige', 'Pearl', 'Crown', 'Majestic', 'Sapphire', 'Plaza', 'Elegance',
        ];
        $hallSuffixes = ['Marquee', 'Banquet Hall', 'Lawn', 'Farm House', 'Community Center', 'Ballroom', 'Rooftop', 'Wedding Hall'];
        $venueTypes = ['marriage_hall', 'banquet_hall', 'farm_house', 'community_center', 'hotel_ballroom', 'rooftop', 'lawn', 'marquee'];

        $hallVendorIds = range(0, 24);
        $hallModels = [];
        for ($i = 0; $i < 120; $i++) {
            $area = $lahoreAreas[$i % count($lahoreAreas)];
            $prefix = $namePrefixes[intdiv($i, count($lahoreAreas)) % count($namePrefixes)];
            $suffix = $hallSuffixes[$i % count($hallSuffixes)];
            $vi = $hallVendorIds[$i % count($hallVendorIds)];
            $hallModels[] = Hall::create([
                'vendor_profile_id' => $vendorProfiles[$vi]->id,
                'name' => "$prefix $area $suffix",
                'venue_type' => $venueTypes[$i % count($venueTypes)],
                'address' => "$area, Lahore",
                'has_floors' => ($i % 3) !== 0,
            ]);
        }

        // ====================================================================
        // 3. FLOORS — for halls with has_floors = true
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
        // 4. HALL UNITS (~480)
        // ====================================================================
        $unitNames = [
            'Main Hall', 'Small Hall', 'Lawn', 'VIP Room', 'Garden', 'Party Hall', 'Banquet Room',
            'Conference Hall', 'Family Hall', 'Grand Lawn', 'Marquee Area', 'Ballroom', 'Rooftop Terrace',
            'VIP Lounge', 'Basement Hall',
        ];
        $decorTypes = ['fixed', 'outsourced', 'customizable'];
        $cateringModes = ['internal', 'external', 'both', 'none'];
        $foodServiceStyles = ['static_place', 'on_table', 'both'];
        $amenityPool = ['parking', 'wheelchair', 'ac', 'sound_system', 'generator', 'bridal_room', 'stage', 'washrooms', 'waiting_area', 'dining_tables'];
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
                    'catering_mode' => $cateringModes[$u % count($cateringModes)],
                    'food_service_style' => $foodServiceStyles[$u % count($foodServiceStyles)],
                    'staff_male' => rand(4, 12),
                    'staff_female' => rand(2, 10),
                    'amenities' => collect($amenityPool)->random(rand(3, 6))->values()->all(),
                    'base_price' => rand(15, 150) * 10000,
                ]);

                // Other charges (extra services) for some units
                if (rand(0, 1) === 0) {
                    foreach ([
                        ['Extra Person', 800, 'per_person'],
                        ['Generator Backup', 15000, 'flat'],
                        ['Additional Lighting', 10000, 'flat'],
                        ['Extra Table', 3000, 'per_table'],
                        ['Sound System Upgrade', 20000, 'flat'],
                    ] as $idx => [$name, $price, $priceUnit]) {
                        if (rand(0, 1) === 1) continue;
                        ExtraService::create([
                            'serviceable_type' => 'App\Models\HallUnit',
                            'serviceable_id' => $unit->id,
                            'name' => $name,
                            'price' => $price,
                            'price_unit' => $priceUnit,
                        ]);
                    }
                }

                $hallUnitIds[] = $unit->id;
            }
        }

        // ====================================================================
        // 5. MENU CATEGORIES, ITEMS & MENU SETS (for hall vendors 0-24)
        // ====================================================================
        $menuTemplates = [
            ['Continental', [
                ['Chicken Alfredo', 1200], ['Beef Steak', 1800], ['Grilled Fish', 1500], ['Pasta Carbonara', 1100],
            ]],
            ['BBQ', [
                ['Chicken Tikka', 900], ['Seekh Kebab', 850], ['Malai Boti', 950], ['BBQ Platter', 1600],
            ]],
            ['Desi Food', [
                ['Chicken Biryani', 800], ['Mutton Karahi', 1400], ['Chicken Karahi', 1100], ['Daal Makhni', 700],
            ]],
            ['Sweets & Desserts', [
                ['Gulab Jamun', 300], ['Kheer', 350], ['Ras Malai', 400], ['Ice Cream Bar', 250],
            ]],
            ['Cold Drinks', [
                ['Soft Drinks', 150], ['Mineral Water', 100], ['Fresh Juices', 250],
            ]],
        ];

        $menuItemsByVendor = [];
        foreach (range(0, 24) as $vi) {
            $profile = $vendorProfiles[$vi];
            $items = [];
            foreach ($menuTemplates as $catIdx => [$catName, $catItems]) {
                $category = MenuCategory::create([
                    'vendor_profile_id' => $profile->id,
                    'name' => $catName,
                    'sort_order' => $catIdx + 1,
                ]);
                foreach ($catItems as $itemIdx => [$itemName, $price]) {
                    $items[] = MenuItem::create([
                        'vendor_profile_id' => $profile->id,
                        'menu_category_id' => $category->id,
                        'name' => $itemName,
                        'price' => $price,
                        'description' => 'Freshly prepared ' . $itemName,
                        'is_available' => true,
                    ]);
                }
            }
            $menuItemsByVendor[$vi] = $items;

            // Menu sets (Menu 1, Menu 2, Menu 3) per Punjab multiple-option law
            $setDefs = [
                ['Menu 1 (Standard)', 'Economical 3-course desi + BBQ menu.', [0, 1, 2, 6, 7, 8, 12, 13, 14, 15]],
                ['Menu 2 (Premium)', 'Mix of continental, BBQ and desi favourites.', [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 13, 15]],
                ['Menu 3 (Royal)', 'Full luxury spread across all categories.', [0, 2, 3, 4, 5, 7, 8, 9, 11, 12, 14, 16]],
            ];
            foreach ($setDefs as $setIdx => [$setName, $setDesc, $itemIdxes]) {
                $set = MenuSet::create([
                    'vendor_profile_id' => $profile->id,
                    'name' => $setName,
                    'description' => $setDesc,
                    'is_active' => true,
                    'sort_order' => $setIdx + 1,
                ]);
                $setItems = collect($itemIdxes)->map(fn ($ix) => $items[$ix % count($items)])->pluck('id');
                $set->items()->sync($setItems);
            }
        }

        // ====================================================================
        // 6. SERVICE LISTINGS (150)
        // ====================================================================
        $listingPools = [
            'decor' => [
                'Floral Arrangement Premium', 'Stage Decoration Classic', 'Lighting Setup Deluxe', 'Wedding Stage Theme Setup',
                'Aisle & Entrance Decor', 'Ceiling Draping Service', 'Chair Covers & Sashes', 'Flower Wall Backdrop',
                'Entrance Arch Flowers', 'Chandelier Setup', 'Candle Centerpieces', 'Photo Booth Backdrop',
                'Marquee Interior Theming', 'Table Centre Pieces', 'Ladder & Drape Setup', 'Escort Cards & Signage',
            ],
            'catering' => [
                'Standard Menu per Person', 'Premium Menu per Person', 'BBQ Package per Person', 'Dessert Station Setup',
                'Continental Menu per Person', 'Chaat & Appetizer Station', 'Live Pasta Station', 'Chinese Menu per Person',
                'Italian Menu per Person', 'Desi Menu per Person', 'Fruit & Juice Bar', 'Beverage Service',
                'Snack Platters', 'Hi-Tea Setup', 'Late Night Dinner', 'Veg & Non-Veg Combo',
            ],
            'photography' => [
                'Wedding Day Coverage', 'Pre-Wedding Shoot', 'Cinematic Highlight Reel', 'Engagement Shoot',
                'Albums & Prints Deluxe', 'Drone Aerial Coverage', 'Traditional Mehndi Shoot', 'Family Group Shots',
                'Same-Day Edit Reel', 'Bridal Poses Session', 'Outdoor Theme Shoot', 'Studio Portrait Session',
                'Full-Day Documentary', 'Photo Booth Service',
            ],
            'dj' => [
                'Premium DJ Package', 'Basic Sound System', 'Live Band Performance', 'Dhol Players (2 pcs)',
                'Full Sound + Light Setup', 'Dance Floor Lighting', 'LED Wall Screen', 'MC / Anchor Services',
                'Sufi Night Singer', 'Disco Light Setup', 'Truss & Rigging', 'Extra Speaker Setup',
            ],
            'car' => [
                'Toyota Corolla Wedding Car', 'Mercedes S-Class Hire', 'BMW 7 Series Luxury', 'Wedding Coach (20 seater)',
                'Vintage Car (Beetle/Classic)', 'SUV Fleet (4x4) for Baraat', 'Land Cruiser Convoy', 'Range Rover Hire',
                'Honda City Wedding Car', 'Vitz Family Car', 'Hummer Limo', 'Horse Carriage (Baraat)',
            ],
        ];

        $vendorByCat = [
            'decor' => range(25, 36),
            'catering' => range(37, 48),
            'photography' => range(49, 58),
            'dj' => range(59, 66),
            'car' => range(67, 74),
        ];

        $listingIds = [];
        foreach ($listingPools as $catSlug => $titles) {
            $catId = $categories[$catSlug] ?? null;
            if (!$catId) continue;
            $vIds = $vendorByCat[$catSlug] ?? [];
            foreach ($vIds as $vi) {
                $profile = $vendorProfiles[$vi];
                for ($k = 0; $k < 3; $k++) {
                    $title = $titles[($vi + $k) % count($titles)];
                    $perPerson = str_contains($title, 'per Person');
                    $listing = ServiceListing::create([
                        'vendor_profile_id' => $profile->id,
                        'service_category_id' => $catId,
                        'title' => $title,
                        'description' => "Professional $title service for your event. Quality assured with verified reviews.",
                        'price' => $perPerson ? rand(800, 3500) : rand(10000, 90000),
                        'price_unit' => $perPerson ? 'per_person' : 'fixed',
                    ]);
                    $listingIds[] = $listing->id;
                }
            }
        }

        // Guarantee the exact titles referenced by PackageItemsSeeder exist
        $requiredListings = [
            'decor' => ['Floral Arrangement Premium', 'Stage Decoration Classic', 'Lighting Setup Deluxe', 'Wedding Stage Theme Setup'],
            'catering' => ['Standard Menu per Person', 'Premium Menu per Person', 'Dessert Station Setup'],
        ];
        foreach ($requiredListings as $catSlug => $titles) {
            $catId = $categories[$catSlug] ?? null;
            if (!$catId) continue;
            $vIds = $vendorByCat[$catSlug] ?? [];
            if (count($vIds) === 0) continue;
            $profile = $vendorProfiles[$vIds[0]];
            foreach ($titles as $title) {
                $exists = ServiceListing::where('service_category_id', $catId)->where('title', $title)->exists();
                if ($exists) continue;
                $perPerson = str_contains($title, 'per Person');
                $listing = ServiceListing::create([
                    'vendor_profile_id' => $profile->id,
                    'service_category_id' => $catId,
                    'title' => $title,
                    'description' => "Professional $title service for your event. Quality assured with verified reviews.",
                    'price' => $perPerson ? rand(800, 3500) : rand(10000, 90000),
                    'price_unit' => $perPerson ? 'per_person' : 'fixed',
                ]);
                $listingIds[] = $listing->id;
            }
        }

        // ====================================================================
        // 7. BOOKINGS (300)
        // ====================================================================
        $statuses = ['requested', 'discussing', 'verified', 'confirmed', 'completed', 'cancelled'];
        $eventTypes = ['wedding', 'engagement', 'corporate', 'birthday'];

        $timeSlots = ['noon', 'evening'];
        for ($b = 1; $b <= 300; $b++) {
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
                'time_slot' => $timeSlots[array_rand($timeSlots)],
                'event_type' => $eventType,
                'status' => $status,
                'budget_input' => $budget,
                'total_price' => $totalPrice,
                'notes' => $b % 5 === 0 ? 'Please arrange extra chairs and vegetarian options.' : null,
            ]);

            // Booking Items (1-4 per booking)
            $itemsCount = rand(1, min(4, 1 + ($b % 3)));
            for ($bi = 0; $bi < $itemsCount; $bi++) {
                if (rand(0, 1) === 0 && count($hallUnitIds) > 0) {
                    $unitId = $hallUnitIds[array_rand($hallUnitIds)];
                    $unit = \App\Models\HallUnit::with('hall')->find($unitId);
                    if ($unit) {
                        $vpId = $unit->hall->vendor_profile_id;
                        $menuSet = $unit->hall->vendorProfile->menuSets()->inRandomOrder()->first();
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'itemable_type' => 'App\Models\HallUnit',
                            'itemable_id' => $unitId,
                            'vendor_profile_id' => $vpId,
                            'price' => $unit->base_price,
                            'time_slot' => $timeSlots[array_rand($timeSlots)],
                            'menu_set_id' => $menuSet && rand(0, 1) ? $menuSet->id : null,
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
        // 8. PAYMENTS (for completed/confirmed bookings)
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
        // 9. REVIEWS (for completed bookings)
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
        // 10. DISPUTES (12)
        // ====================================================================
        $cancelledBookings = Booking::where('status', 'cancelled')->take(12)->get();
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
        // 11. VENDOR SUBSCRIPTION PACKAGES (plans)
        // ====================================================================
        $packagesData = [
            ['Starter Plan', 'Perfect for new vendors — 1 hall, 2 service listings, basic visibility.', 15000, 30, 'featured', 1, 2],
            ['Growth Plan', 'For growing vendors — 2 halls, 5 listings, featured boost.', 35000, 30, 'featured', 2, 5],
            ['Premium Plan', 'Best value — 3 halls, 10 listings, premium boost, top placement.', 65000, 30, 'premium', 3, 10],
            ['Pro Annual', 'Everything in Premium, billed yearly for serious businesses.', 650000, 365, 'premium', 3, 10],
        ];

        foreach ($packagesData as [$title, $desc, $price, $durationDays, $boostTier, $maxHalls, $maxListings]) {
            Package::create([
                'title' => $title,
                'description' => $desc,
                'total_price' => $price,
                'duration_days' => $durationDays,
                'boost_tier' => $boostTier,
                'max_halls' => $maxHalls,
                'max_listings' => $maxListings,
                'is_active' => true,
            ]);
        }

        // ====================================================================
        // 12. CORPORATE LEADS (12) — Lahore
        // ====================================================================
        $companies = [
            ['TechCorp Solutions', 'Usman Ahmed', 'usman@techcorp.com', '03002001001', 'Need venue for annual dinner — 500 people in Lahore.', 'new'],
            ['Al-Falah Group', 'Hassan Iqbal', 'hassan@alfalah.com', '03002001002', 'Quarterly business conference in Lahore, 200 attendees.', 'contacted'],
            ['Crescent Textiles', 'Fatima Aslam', 'fatima@crescent.com', '03002001003', 'Product launch event in Lahore, expecting 300 guests.', 'new'],
            ['Punjab Healthcare', 'Dr. Ahmed Khan', 'dr.ahmed@phc.com', '03002001004', 'Medical conference for 400 doctors in Lahore.', 'converted'],
            ['Digital Pakistan', 'Sara Zafar', 'sara@digitalpak.com', '03002001005', 'Tech summit for 1000 people in a Lahore convention hall.', 'new'],
            ['Bank Alfalah Ltd', 'Kamran Shah', 'kamran@bankalfalah.com', '03002001006', 'Annual customer appreciation dinner for 600 guests in Lahore.', 'contacted'],
            ['Shalimar Foods', 'Rizwan Ali', 'rizwan@shalimar.com', '03002001007', 'New product tasting event in Lahore, 150 guests.', 'new'],
            ['Pakistan Telecom', 'Nadia Khan', 'nadia@ptcl.com', '03002001008', 'Employee awards ceremony at a Lahore hotel.', 'closed'],
            ['Lahore Grammar School', 'Prof. Akram', 'akram@lgs.edu.pk', '03002001009', 'Annual sports gala and prize distribution in Lahore.', 'new'],
            ['Fashion Pakistan', 'Zara Tariq', 'zara@fp.com', '03002001010', 'Fashion show and dinner for 400 guests in Lahore.', 'new'],
            ['Khan Developers', 'Ali Khan', 'ali@khandevelopers.com', '03002001011', 'Property launch ceremony and dinner in Lahore.', 'contacted'],
            ['Sufi Traders', 'Imran Sufi', 'imran@sufitraders.com', '03002001012', 'Export conference with international delegates in Lahore.', 'new'],
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

        // ====================================================================
        // 13. CORPORATE QUOTATIONS (5) — one per representative lead
        // ====================================================================
        $adminId = User::where('role', 'admin')->value('id');
        $leadIds = CorporateLead::pluck('id', 'company_name');
        $quotationSeeds = [
            ['TechCorp Solutions', 'draft', now()->addMonths(1)->toDateString(), 'Pearl Continental Hotel, Lahore', 500, 1200000, 950000, 'Grand hall, buffet dinner, stage & sound, decorations, 40 staff.', 'Deposit of 50% required on acceptance.', now()->addDays(14)->toDateString()],
            ['Al-Falah Group', 'sent', now()->addMonths(2)->toDateString(), 'Lahore Expo Centre', 200, 800000, 650000, 'Conference hall, projector, AV setup, lunch buffet, tea breaks.', 'Includes 2 days of venue hire.', now()->addDays(10)->toDateString()],
            ['Crescent Textiles', 'sent', now()->addMonths(3)->toDateString(), 'Royal Palm Golf & Country Club, Lahore', 300, 1500000, 1250000, 'Banquet hall, runway setup for product launch, catering, valet parking.', 'Client to confirm number of guests by next week.', now()->addDays(20)->toDateString()],
            ['Punjab Healthcare', 'accepted', now()->addMonths(1)->toDateString(), 'Avari Xpress, Lahore', 400, 2000000, 1800000, 'Main hall, medical AV equipment, lunch & hi-tea, 60 staff.', 'Accepted — booking to be finalised in system.', now()->addDays(30)->toDateString()],
            ['Digital Pakistan', 'declined', now()->addMonths(4)->toDateString(), 'Arfa Software Technology Park, Lahore', 1000, 3500000, 3000000, 'Convention floor, LED screens, live streaming, catering for 1000.', 'Client found the budget too high — negotiating.', now()->addDays(7)->toDateString()],
        ];

        foreach ($quotationSeeds as [$company, $qStatus, $eventDate, $venue, $capacity, $budget, $amount, $inclusions, $qNotes, $validUntil]) {
            $leadId = $leadIds[$company] ?? null;
            if (! $leadId) {
                continue;
            }
            CorporateQuotation::create([
                'corporate_lead_id' => $leadId,
                'token' => Str::random(40),
                'event_date' => $eventDate,
                'venue' => $venue,
                'seating_capacity' => $capacity,
                'budget' => $budget,
                'amount' => $amount,
                'inclusions' => $inclusions,
                'notes' => $qNotes,
                'valid_until' => $validUntil,
                'status' => $qStatus,
                'created_by' => $adminId,
            ]);
        }

        // ====================================================================
        // 14. MANUAL BOOKINGS (3) — offline hall bookings that block slots
        // ====================================================================
        $manualClients = [
            ['Haji Saeed & Sons', '03010000001', 10, 'evening', 'corporate', 450000],
            ['Noor Catering', '03010000002', 20, 'noon', 'birthday', 300000],
            ['Punjab Dairies', '03010000003', 30, 'evening', 'wedding', 650000],
        ];

        foreach ($manualClients as $i => [$clientName, $clientPhone, $daysOffset, $timeSlot, $eventType, $amount]) {
            if (count($hallUnitIds) === 0) {
                break;
            }
            $unit = HallUnit::with('hall')->find($hallUnitIds[$i % count($hallUnitIds)]);
            if (! $unit) {
                continue;
            }
            $vpId = $unit->hall->vendor_profile_id;
            $eventDate = now()->addDays($daysOffset);

            $booking = Booking::create([
                'customer_id' => null,
                'booking_type' => 'manual',
                'event_date' => $eventDate,
                'time_slot' => $timeSlot,
                'event_type' => $eventType,
                'status' => 'confirmed',
                'budget_input' => $amount,
                'total_price' => $amount,
                'notes' => "Manual booking\nClient: {$clientName}\nPhone: {$clientPhone}",
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'itemable_type' => 'App\Models\HallUnit',
                'itemable_id' => $unit->id,
                'vendor_profile_id' => $vpId,
                'price' => $amount,
                'time_slot' => $timeSlot,
                'vendor_status' => 'accepted',
            ]);

            AvailabilitySlot::create([
                'resource_type' => 'App\Models\HallUnit',
                'resource_id' => $unit->id,
                'date' => $eventDate,
                'slot_type' => $timeSlot,
                'time_slot' => null,
                'status' => 'booked',
                'booking_id' => $booking->id,
            ]);
        }
    }
}
