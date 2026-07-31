<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'How to Choose the Perfect Wedding Hall in Pakistan',
                'excerpt' => 'From capacity and location to hidden costs, here is what you must check before booking a wedding venue.',
                'content' => '<p>Choosing the right wedding hall is one of the biggest decisions in event planning. Start by listing your guest count, then shortlist venues that comfortably seat everyone without feeling overcrowded.</p><p>Location matters more than people think. A venue that is far from most guests means extra transport cost and late arrivals. Check parking availability, nearby hotels, and access for female guests.</p><p>Before you pay any advance, ask about hidden costs: decoration restrictions, generator charges, service taxes, and overtime fees. Always get the full package in writing.</p><p>Use a platform like BeeG Events to compare real prices, see genuine photos, and read verified reviews from customers who have actually booked the venue.</p>',
                'author' => 'BeeG Events Team',
                'is_published' => true,
            ],
            [
                'title' => 'Wedding Budget Breakdown: Where Your Money Actually Goes',
                'excerpt' => 'A realistic percentage guide to planning a wedding budget that does not fall apart halfway through.',
                'content' => '<p>Most couples underestimate venue and catering, the two largest wedding expenses. As a rule of thumb, plan 40% of the budget for venue, food, and drinks, 25% for decor, 15% for photography and videography, 10% for attire and beauty, and 10% as a buffer for unexpected costs.</p><p>The buffer is not optional. Flowers, last-minute guests, and vendor overtime always add up. Never commit 100% of your budget before the event date.</p><p>Compare at least three vendors for every category. BeeG Events lets you compare verified service listings side by side so you can negotiate from a position of knowledge.</p>',
                'author' => 'BeeG Events Team',
                'is_published' => true,
            ],
            [
                'title' => 'Corporate Event Planning: A Complete Checklist for 2026',
                'excerpt' => 'From budgeting to post-event follow-up, a practical checklist for company events that run smoothly.',
                'content' => '<p>Corporate events have a completely different rhythm than private functions. Start with a clear objective: is this a product launch, a team-building retreat, or an awards night? The objective decides the venue, the seating, and the agenda.</p><p>Set the budget first, then book the venue. A common mistake is picking a venue and then discovering it eats 70% of the budget. Aim for the venue, food, and basic AV to stay under 60%.</p><p>Assign one person as the single point of contact for every vendor. Corporate events fail on communication, not on budget.</p><p>BeeG Events handles corporate inquiries with a dedicated lead flow, so a real person responds to your requirements with a manual quote.</p>',
                'author' => 'BeeG Events Team',
                'is_published' => true,
            ],
            [
                'title' => 'Marquee vs Hall vs Open Air: Which Venue Is Right for You?',
                'excerpt' => 'A straight comparison of the three most common event venue types in Pakistan.',
                'content' => '<p>Halls are the most predictable: fixed capacity, built-in kitchens, and established staff. They are ideal for weddings in the winter months when outdoor options are risky.</p><p>Marquees give you a blank canvas. You control the layout, the lighting, and the atmosphere, but you also inherit the risk: weather, generators, and portable bathrooms all become your responsibility.</p><p>Open-air venues are spectacular for daytime events but depend completely on weather and season. Always confirm the backup plan before booking.</p><p>Whatever you choose, compare real photos and verified reviews on BeeG Events before paying an advance.</p>',
                'author' => 'BeeG Events Team',
                'is_published' => true,
            ],
            [
                'title' => 'Top 10 Questions to Ask a Caterer Before Booking',
                'excerpt' => 'The exact questions that separate a reliable caterer from a last-minute disaster.',
                'content' => '<p>1. What is the per-head cost and what exactly is included? 2. Can we attend a tasting before booking? 3. How many staff do you bring per 100 guests? 4. What is your backup plan if a cook falls sick?</p><p>5. Do you provide crockery, cutlery, and furniture? 6. What are the service taxes and delivery charges? 7. How do you handle guests with allergies? 8. When is the final headcount deadline? 9. What is the payment schedule? 10. What happens if the event is cancelled?</p><p>Every answer should come in writing. Verbal promises disappear the night before the event.</p>',
                'author' => 'BeeG Events Team',
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'author' => $post['author'],
                    'is_published' => $post['is_published'],
                    'published_at' => now(),
                ]
            );
        }

        $this->command?->info('Blog posts seeded.');
    }
}
