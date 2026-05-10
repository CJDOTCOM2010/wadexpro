<?php

namespace Database\Seeders;

use App\Modules\CMS\Models\CmsPage;
use App\Modules\CMS\Models\CmsSection;
use App\Modules\CMS\Models\CmsBlock;
use Illuminate\Database\Seeder;

class CmsLandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create the Home Page ──────────────────────────────
        $page = CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'id'               => (string) \Illuminate\Support\Str::uuid(),
                'title'            => ['en' => 'Home', 'fr' => 'Accueil'],
                'meta_description' => ['en' => 'WADEXP – Enterprise mobility & logistics for Africa.'],
                'status'           => 'published',
                'template'         => 'landing',
                'sort_order'       => 1,
            ]
        );

        // Clear old sections for idempotent re-seeding
        $page->sections()->each(function ($section) {
            $section->blocks()->delete();
            $section->delete();
        });

        // ── 2. Hero Section ──────────────────────────────────────
        $hero = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'hero',
            'title'      => 'Hero',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        CmsBlock::create(['section_id' => $hero->id, 'type' => 'heading', 'key' => 'title', 'content' => 'Move People. Deliver Everything.', 'sort_order' => 1]);
        CmsBlock::create(['section_id' => $hero->id, 'type' => 'paragraph', 'key' => 'subtitle', 'content' => 'Ghana\'s #1 enterprise-grade ride-hailing and courier platform. Fast pickups, safe rides, and seamless deliveries — all in one app.', 'sort_order' => 2]);
        CmsBlock::create(['section_id' => $hero->id, 'type' => 'button', 'key' => 'cta_ride', 'content' => 'Request a Ride', 'link_url' => '/ride', 'sort_order' => 3]);
        CmsBlock::create(['section_id' => $hero->id, 'type' => 'button', 'key' => 'cta_driver', 'content' => 'Become a Driver', 'link_url' => '/drive', 'sort_order' => 4]);

        // ── 3. Services Section ──────────────────────────────────
        $services = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'services',
            'title'      => 'What We Offer',
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        $serviceItems = [
            ['Ride', 'Request a ride in minutes. Safe, reliable, and affordable.', 'car'],
            ['Delivery', 'Send packages across the city with real-time tracking.', 'package'],
            ['Courier', 'Enterprise courier services for businesses of all sizes.', 'truck'],
        ];

        foreach ($serviceItems as $i => $item) {
            CmsBlock::create([
                'section_id' => $services->id,
                'type'       => 'icon_card',
                'key'        => 'service_' . ($i + 1),
                'content'    => $item[0],
                'properties' => ['description' => $item[1], 'icon' => $item[2]],
                'sort_order' => $i + 1,
            ]);
        }

        // ── 4. How It Works Section ──────────────────────────────
        $howItWorks = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'how_it_works',
            'title'      => 'How It Works',
            'sort_order' => 3,
            'is_visible' => true,
        ]);

        $steps = [
            ['Request a Ride', 'Open the app, enter your destination, and tap request.'],
            ['Get Matched', 'We instantly find the nearest available driver for you.'],
            ['Arrive Safely', 'Track your ride in real-time and arrive at your destination.'],
        ];

        foreach ($steps as $i => $step) {
            CmsBlock::create([
                'section_id' => $howItWorks->id,
                'type'       => 'step',
                'key'        => 'step_' . ($i + 1),
                'content'    => $step[0],
                'properties' => ['description' => $step[1], 'step_number' => $i + 1],
                'sort_order' => $i + 1,
            ]);
        }

        // ── 5. Benefits Section ──────────────────────────────────
        $benefits = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'benefits',
            'title'      => 'Why Choose WADEXP',
            'sort_order' => 4,
            'is_visible' => true,
        ]);

        $benefitItems = [
            ['Lightning Fast', 'Average pickup time under 4 minutes in Accra.', 'zap'],
            ['Affordable Pricing', 'Transparent fares with no hidden charges.', 'wallet'],
            ['Safety First', 'Real-time SOS, driver verification, and trip sharing.', 'shield'],
            ['24/7 Support', 'Dedicated customer support around the clock.', 'headphones'],
        ];

        foreach ($benefitItems as $i => $item) {
            CmsBlock::create([
                'section_id' => $benefits->id,
                'type'       => 'icon_card',
                'key'        => 'benefit_' . ($i + 1),
                'content'    => $item[0],
                'properties' => ['description' => $item[1], 'icon' => $item[2]],
                'sort_order' => $i + 1,
            ]);
        }

        // ── 6. Driver Onboarding Section ─────────────────────────
        $driverSection = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'driver_onboarding',
            'title'      => 'Drive With WADEXP',
            'sort_order' => 5,
            'is_visible' => true,
        ]);

        CmsBlock::create(['section_id' => $driverSection->id, 'type' => 'heading', 'key' => 'title', 'content' => 'Earn on Your Own Schedule', 'sort_order' => 1]);
        CmsBlock::create(['section_id' => $driverSection->id, 'type' => 'paragraph', 'key' => 'subtitle', 'content' => 'Join thousands of driver-partners across Ghana. Set your own hours, keep most of your earnings, and grow with us.', 'sort_order' => 2]);
        CmsBlock::create(['section_id' => $driverSection->id, 'type' => 'button', 'key' => 'cta', 'content' => 'Start Driving Today', 'link_url' => '/drive', 'sort_order' => 3]);

        $driverStats = [
            ['GH₵ 2,500+', 'Avg. Monthly Earnings'],
            ['10,000+', 'Active Drivers'],
            ['50+', 'Cities Covered'],
        ];

        foreach ($driverStats as $i => $stat) {
            CmsBlock::create([
                'section_id' => $driverSection->id,
                'type'       => 'stat',
                'key'        => 'stat_' . ($i + 1),
                'content'    => $stat[0],
                'properties' => ['label' => $stat[1]],
                'sort_order' => 10 + $i,
            ]);
        }

        // ── 7. Business / Enterprise Section ─────────────────────
        $business = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'business',
            'title'      => 'WADEXP for Business',
            'sort_order' => 6,
            'is_visible' => true,
        ]);

        CmsBlock::create(['section_id' => $business->id, 'type' => 'heading', 'key' => 'title', 'content' => 'Enterprise Mobility Solutions', 'sort_order' => 1]);
        CmsBlock::create(['section_id' => $business->id, 'type' => 'paragraph', 'key' => 'description', 'content' => 'Streamline employee transportation, manage fleet operations, and optimize logistics costs with our enterprise dashboard. Trusted by leading organizations across West Africa.', 'sort_order' => 2]);
        CmsBlock::create(['section_id' => $business->id, 'type' => 'button', 'key' => 'cta', 'content' => 'Contact Sales', 'link_url' => '/business', 'sort_order' => 3]);

        // ── 8. Testimonials Section ──────────────────────────────
        $testimonials = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'testimonials',
            'title'      => 'What Our Users Say',
            'sort_order' => 7,
            'is_visible' => true,
        ]);

        $reviews = [
            ['Kwame Asante', 'Daily Commuter, Accra', 'WADEXP has completely changed how I get around. The drivers are always professional, and the prices are unbeatable.'],
            ['Ama Mensah', 'Business Owner, Kumasi', 'I use WADEXP for all my courier deliveries. Fast, reliable, and the tracking feature gives my customers peace of mind.'],
            ['Daniel Osei', 'Driver Partner', 'Driving for WADEXP lets me earn well while maintaining my own schedule. The app is easy to use and payouts are always on time.'],
        ];

        foreach ($reviews as $i => $review) {
            CmsBlock::create([
                'section_id' => $testimonials->id,
                'type'       => 'testimonial',
                'key'        => 'testimonial_' . ($i + 1),
                'content'    => $review[0],
                'properties' => ['role' => $review[1], 'content' => $review[2]],
                'sort_order' => $i + 1,
            ]);
        }

        // ── 9. FAQ Section ───────────────────────────────────────
        $faq = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'faq',
            'title'      => 'Frequently Asked Questions',
            'sort_order' => 8,
            'is_visible' => true,
        ]);

        $faqs = [
            ['How do I request a ride?', 'Download the WADEXP app, create an account, enter your pickup and destination, and tap "Request Ride". A driver will be matched to you within seconds.'],
            ['How are fares calculated?', 'Fares are based on distance, time, and current demand. You will always see the estimated fare before confirming your ride.'],
            ['Is WADEXP safe?', 'Yes! All drivers go through KYC verification, background checks, and vehicle inspections. We also offer real-time SOS alerts and trip sharing.'],
            ['How do I become a driver?', 'Download the WADEXP Driver app, submit your documents for verification, complete a brief onboarding, and start earning.'],
        ];

        foreach ($faqs as $i => $item) {
            CmsBlock::create([
                'section_id' => $faq->id,
                'type'       => 'faq_item',
                'key'        => 'faq_' . ($i + 1),
                'content'    => $item[0],
                'properties' => ['answer' => $item[1]],
                'sort_order' => $i + 1,
            ]);
        }

        // ── 10. App Download Section ─────────────────────────────
        $appDownload = CmsSection::create([
            'page_id'    => $page->id,
            'type'       => 'app_download',
            'title'      => 'Get the App',
            'sort_order' => 9,
            'is_visible' => true,
        ]);

        CmsBlock::create(['section_id' => $appDownload->id, 'type' => 'heading', 'key' => 'title', 'content' => 'Download WADEXP Today', 'sort_order' => 1]);
        CmsBlock::create(['section_id' => $appDownload->id, 'type' => 'paragraph', 'key' => 'subtitle', 'content' => 'Available on iOS and Android. Start riding or delivering in minutes.', 'sort_order' => 2]);

        $this->command->info('✅ CMS Landing Page seeded with 10 sections and full content blocks.');
    }
}
