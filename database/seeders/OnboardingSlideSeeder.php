<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Admin\Models\OnboardingSlide;
use Illuminate\Support\Facades\DB;

class OnboardingSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing slides to avoid duplicates during testing
        DB::table('onboarding_slides')->truncate();

        $slides = [
            // --- CUSTOMER SLIDES ---
            [
                'app_type'     => 'customer',
                'title'        => 'Welcome to WADEXPRO',
                'description'  => 'Your all-in-one logistics partner for fast, reliable, and secure transportation of goods across the globe.',
                'image_url'    => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'full_bleed',
                'sort_order'   => 1,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'customer',
                'title'        => 'Safe & Secure',
                'description'  => 'Our advanced encryption and verified driver network ensure your cargo is handled with the highest care.',
                'image_url'    => 'https://images.unsplash.com/photo-1554224155-1696413575b9?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'glassmorphic',
                'sort_order'   => 2,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'customer',
                'title'        => 'Real-time Tracking',
                'description'  => 'Know exactly where your delivery is at any moment with our precision GPS and instant notifications.',
                'image_url'    => 'https://images.unsplash.com/photo-1540339832862-eb6993275727?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'industrial',
                'sort_order'   => 3,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'customer',
                'title'        => 'Transparent Pricing',
                'description'  => 'No hidden fees. Get instant quotes based on weight, distance, and urgency with complete honesty.',
                'image_url'    => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?q=80&w=2071&auto=format&fit=crop',
                'layout_style' => 'top_image',
                'sort_order'   => 4,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'customer',
                'title'        => 'Loyalty Rewards',
                'description'  => 'Earn points for every shipment and redeem them for discounts and exclusive logistics perks.',
                'image_url'    => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'clean_vector',
                'sort_order'   => 5,
                'is_active'    => true,
            ],

            // --- DRIVER SLIDES ---
            [
                'app_type'     => 'driver',
                'title'        => 'Be Your Own Boss',
                'description'  => 'Take control of your time. Start earning today with flexible hours and competitive payout rates.',
                'image_url'    => 'https://images.unsplash.com/photo-1501700489910-fb240757d598?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'dark_premium',
                'sort_order'   => 1,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'driver',
                'title'        => 'Maximize Earnings',
                'description'  => 'Our smart dispatching engine routes you to the highest demand areas to keep your wheels turning.',
                'image_url'    => 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?q=80&w=2071&auto=format&fit=crop',
                'layout_style' => 'side_by_side',
                'sort_order'   => 2,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'driver',
                'title'        => 'Precision Navigation',
                'description'  => 'Integrated smart maps optimized for logistics and heavy vehicles to ensure the fastest delivery paths.',
                'image_url'    => 'https://images.unsplash.com/photo-1569003339405-ea396a5a8a90?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'floating_card',
                'sort_order'   => 3,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'driver',
                'title'        => '24/7 Driver Support',
                'description'  => 'You are never alone on the road. Our support team is available around the clock to assist you.',
                'image_url'    => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=2072&auto=format&fit=crop',
                'layout_style' => 'bottom_image',
                'sort_order'   => 4,
                'is_active'    => true,
            ],
            [
                'app_type'     => 'driver',
                'title'        => 'Flexible Scheduling',
                'description'  => 'Work when you want, where you want. No minimum hours, no stress. Just pick up and deliver.',
                'image_url'    => 'https://images.unsplash.com/photo-1491438590914-bc09fca97c21?q=80&w=2070&auto=format&fit=crop',
                'layout_style' => 'clean_vector',
                'sort_order'   => 5,
                'is_active'    => true,
            ],
        ];

        foreach ($slides as $slide) {
            OnboardingSlide::create($slide);
        }
    }
}
