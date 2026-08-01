<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vendor type specifications
    |--------------------------------------------------------------------------
    | Single source of truth for the type-specific field sets shown during
    | vendor registration/onboarding and on the vendor profile page.
    |
    | Field shape:
    |   key       - field / column key
    |   label     - display label
    |   type      - text | number | textarea | select | multi_select | checkbox | file
    |   options   - options for select / multi_select
    |   required  - bool
    |   flat      - bool, store directly as a vendor_profiles column
    |   suffix    - suffix shown next to the input (e.g. PKR)
    |   min / max - numeric constraints
    |   help      - helper text
    |
    | `starting_price_from` / `years_experience_from` tell VendorSpecService
    | which submitted field feeds the mirrored searchable flat columns.
    */
    'types' => [
        'hall' => [
            'label' => 'Marriage Hall',
            'icon' => 'ti ti-building-arch',
            'starting_price_from' => 'hall_cost',
            'fields' => [
                ['key' => 'floors_count', 'label' => 'No. of Floors', 'type' => 'number', 'min' => 1],
                ['key' => 'min_capacity', 'label' => 'Capacity (min guests)', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'max_capacity', 'label' => 'Capacity (max guests)', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'sitting_per_person_cost', 'label' => 'Sitting per person cost', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'hall_cost', 'label' => 'Hall cost', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'min_person_booking', 'label' => 'Min person booking', 'type' => 'number', 'min' => 0],
                ['key' => 'max_person_booking', 'label' => 'Max person booking', 'type' => 'number', 'min' => 0],
                ['key' => 'per_person_without_food', 'label' => 'Per person (without food)', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'hall_cost_without_food', 'label' => 'Hall cost without food', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'allows_catering', 'label' => 'Catering allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_photography', 'label' => 'Photography allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_dj_light', 'label' => 'DJ + Lighting allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_decor', 'label' => 'Decor allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'amenities', 'label' => 'Services / Amenities', 'type' => 'multi_select', 'options' => ['Parking', 'Wheelchair', 'Isolated bridal room', 'Rest area', 'Pray area', 'Ramp', 'Lift']],
            ],
        ],
        'farmhouse' => [
            'label' => 'Farmhouse',
            'icon' => 'ti ti-home-2',
            'starting_price_from' => 'venue_cost',
            'fields' => [
                ['key' => 'min_capacity', 'label' => 'Capacity (min guests)', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'max_capacity', 'label' => 'Capacity (max guests)', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'land_area_kanal', 'label' => 'Land area', 'type' => 'number', 'suffix' => 'Kanal', 'min' => 0],
                ['key' => 'sitting_per_person_cost', 'label' => 'Sitting per person cost', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'venue_cost', 'label' => 'Venue cost', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'per_person_without_food', 'label' => 'Per person (without food)', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'venue_cost_without_food', 'label' => 'Venue cost without food', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'allows_catering', 'label' => 'Catering allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_photography', 'label' => 'Photography allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_dj_light', 'label' => 'DJ + Lighting allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'allows_decor', 'label' => 'Decor allowed', 'type' => 'checkbox', 'group' => 'options'],
                ['key' => 'amenities', 'label' => 'Services / Amenities', 'type' => 'multi_select', 'options' => ['Lawn', 'Pool', 'Indoor Hall', 'Rooftop', 'BBQ Area', 'Kids Play Area', 'Parking', 'Generator']],
            ],
        ],
        'decor' => [
            'label' => 'Decor',
            'icon' => 'ti ti-flower',
            'starting_price_from' => 'base_price',
            'years_experience_from' => 'years_experience',
            'fields' => [
                ['key' => 'years_experience', 'label' => 'Years of experience', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'styles', 'label' => 'Styles', 'type' => 'multi_select', 'options' => ['Floral', 'Mandap', 'Traditional', 'Modern', 'Theme']],
                ['key' => 'includes_lighting', 'label' => 'Includes lighting', 'type' => 'checkbox'],
                ['key' => 'setup_time', 'label' => 'Setup time', 'type' => 'select', 'options' => ['3–6 hours', '6–12 hours', '12+ hours']],
                ['key' => 'service_area', 'label' => 'Service area', 'type' => 'text'],
                ['key' => 'base_price', 'label' => 'Base price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
            ],
        ],
        'catering' => [
            'label' => 'Catering',
            'icon' => 'ti ti-chef-hat',
            'starting_price_from' => 'price_per_person_min',
            'years_experience_from' => 'years_experience',
            'fields' => [
                ['key' => 'years_experience', 'label' => 'Years of experience', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'cuisine_types', 'label' => 'Cuisine types', 'type' => 'multi_select', 'options' => ['Desi', 'Chinese', 'Continental', 'Thai', 'BBQ', 'Salad', 'Sweets', 'Seafood']],
                ['key' => 'price_per_person_min', 'label' => 'Price per person (min)', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'price_per_person_max', 'label' => 'Price per person (max)', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'max_capacity_per_event', 'label' => 'Max capacity per event', 'type' => 'number', 'min' => 0],
                ['key' => 'halal_certified', 'label' => 'Halal certified', 'type' => 'checkbox'],
                ['key' => 'service_area', 'label' => 'Service area', 'type' => 'text'],
            ],
        ],
        'photography' => [
            'label' => 'Photography & Videography',
            'icon' => 'ti ti-camera',
            'starting_price_from' => 'base_price',
            'years_experience_from' => 'years_experience',
            'fields' => [
                ['key' => 'years_experience', 'label' => 'Years of experience', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'coverage_types', 'label' => 'Coverage types', 'type' => 'multi_select', 'options' => ['Candid', 'Traditional', 'Cinematic', 'Pre-wedding', 'Drone', 'Album']],
                ['key' => 'team_size', 'label' => 'Team size', 'type' => 'number', 'min' => 1],
                ['key' => 'includes_videography', 'label' => 'Includes videography', 'type' => 'checkbox'],
                ['key' => 'includes_drone', 'label' => 'Includes drone coverage', 'type' => 'checkbox'],
                ['key' => 'base_price', 'label' => 'Base price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
            ],
        ],
        'dj' => [
            'label' => 'DJ / Sound',
            'icon' => 'ti ti-music',
            'starting_price_from' => 'base_price',
            'years_experience_from' => 'years_experience',
            'fields' => [
                ['key' => 'setup_type', 'label' => 'Setup type', 'type' => 'select', 'options' => ['DJ Only', 'DJ + Lighting', 'Full Production']],
                ['key' => 'equipment', 'label' => 'Equipment', 'type' => 'multi_select', 'options' => ['Speakers', 'Mixer', 'Lighting', 'Truss', 'Smoke Machine', 'LED Wall']],
                ['key' => 'years_experience', 'label' => 'Years of experience', 'type' => 'number', 'min' => 0, 'flat' => true],
                ['key' => 'base_price', 'label' => 'Base price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
            ],
        ],
        'car' => [
            'label' => 'Car Rental',
            'icon' => 'ti ti-car',
            'starting_price_from' => 'per_event_price',
            'fields' => [
                ['key' => 'fleet_size', 'label' => 'Fleet size', 'type' => 'number', 'min' => 1],
                ['key' => 'vehicle_types', 'label' => 'Vehicle types', 'type' => 'multi_select', 'options' => ['Sedan', 'SUV', 'Sports', 'Limo', 'Vintage', 'Hatchback']],
                ['key' => 'chauffeur_included', 'label' => 'Chauffeur included', 'type' => 'checkbox'],
                ['key' => 'per_event_price', 'label' => 'Per event price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'per_hour_price', 'label' => 'Per hour price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
                ['key' => 'service_area', 'label' => 'Service area', 'type' => 'text'],
            ],
        ],
        'other' => [
            'label' => 'Other',
            'icon' => 'ti ti-package',
            'starting_price_from' => 'base_price',
            'fields' => [
                ['key' => 'details', 'label' => 'Describe your service', 'type' => 'textarea'],
                ['key' => 'base_price', 'label' => 'Base price', 'type' => 'number', 'suffix' => 'PKR', 'min' => 0],
            ],
        ],
    ],

    /*
    | Contact / legal fields shown for every vendor type (flat columns).
    */
    'common_fields' => [
        ['key' => 'contact_person_name', 'label' => 'Contact Person Name', 'type' => 'text', 'flat' => true, 'required' => true],
        ['key' => 'contact_person_phone', 'label' => 'Contact Person Number', 'type' => 'text', 'flat' => true, 'required' => true],
        ['key' => 'legal_doc', 'label' => 'Legal Document', 'type' => 'file', 'flat' => true, 'help' => 'PDF, JPG or PNG (max 10MB)'],
    ],
];
