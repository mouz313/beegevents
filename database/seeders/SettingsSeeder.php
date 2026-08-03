<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['site_name', 'BeeG Events', 'text', 'Site Name', 'general', 'Company / platform name shown across the site and documents.'],
            ['site_tagline', 'Weddings & Events Platform', 'text', 'Site Tagline', 'general', 'Short tagline shown with the brand.'],
            ['site_description', 'BeeG Events helps you plan weddings and events with trusted halls, decor, catering, photography and more.', 'textarea', 'Site Description', 'general', 'Meta description used by the site.'],
            ['site_logo', null, 'image', 'Site Logo', 'general', 'Logo used in documents and public branding.'],
            ['contact_email', 'info@beegevents.com', 'email', 'Contact Email', 'general', 'Public contact email.'],
            ['contact_phone', '+92 300 1234567', 'text', 'Contact Phone', 'general', 'Public contact phone number.'],
            ['contact_address', 'Lahore, Pakistan', 'textarea', 'Contact Address', 'general', 'Business address shown in documents and contact pages.'],
            ['support_email', 'support@beegevents.com', 'email', 'Support Email', 'general', 'Email for support enquiries.'],
            ['footer_about', 'BeeG Events — your trusted partner for weddings and events.', 'textarea', 'Footer About Text', 'general', 'Short about text shown in the site footer.'],

            // Landing slider
            ['hero_slide_1_image', null, 'image', 'Hero Slide 1 Image', 'slider', 'Background image for the first landing page slider slide.'],
            ['hero_slide_2_image', null, 'image', 'Hero Slide 2 Image', 'slider', 'Background image for the second landing page slider slide.'],
            ['hero_slide_3_image', null, 'image', 'Hero Slide 3 Image', 'slider', 'Background image for the third landing page slider slide.'],
            ['hero_slide_1_heading', 'Plan Your Perfect Event in Minutes', 'text', 'Hero Slide 1 Heading', 'slider', 'Headline shown on the first slider slide.'],
            ['hero_slide_2_heading', 'Find Trusted Vendors for Your Event', 'text', 'Hero Slide 2 Heading', 'slider', 'Headline shown on the second slider slide.'],
            ['hero_slide_3_heading', 'Book With Confidence, Every Time', 'text', 'Hero Slide 3 Heading', 'slider', 'Headline shown on the third slider slide.'],

            // Social
            ['facebook', 'https://facebook.com/beegevents', 'url', 'Facebook', 'social', 'Facebook page URL.'],
            ['instagram', 'https://instagram.com/beegevents', 'url', 'Instagram', 'social', 'Instagram page URL.'],
            ['twitter', 'https://twitter.com/beegevents', 'url', 'Twitter / X', 'social', 'Twitter or X profile URL.'],
            ['tiktok', 'https://tiktok.com/@beegevents', 'url', 'TikTok', 'social', 'TikTok profile URL.'],
            ['youtube', 'https://youtube.com/@beegevents', 'url', 'YouTube', 'social', 'YouTube channel URL.'],

            // Business rules
            ['currency_symbol', 'PKR', 'text', 'Currency Symbol', 'business', 'Currency shown on prices.'],
            ['advance_deposit_percent', '30', 'number', 'Advance Deposit %', 'business', 'Advance deposit percentage expected on booking confirmation.'],
            ['booking_hold_hours', '24', 'number', 'Booking Hold Hours', 'business', 'How long a hall slot stays held before being released.'],
            ['refund_cutoff_hours', '72', 'number', 'Refund Cutoff Hours', 'business', 'Hours before the event after which cancellations do not refund the advance.'],

            // Status
            ['maintenance_mode', '0', 'boolean', 'Maintenance Mode', 'status', 'Put the public site into maintenance mode.'],
            ['allow_new_bookings', '1', 'boolean', 'Allow New Bookings', 'status', 'Whether customers can place new bookings.'],
        ];

        foreach ($settings as [$key, $value, $type, $label, $group, $description]) {
            Setting::updateOrCreate(
                ['key' => $key],
                compact('value', 'type', 'label', 'group', 'description')
            );
        }

        Setting::forgetCache();
    }
}
