<?php

namespace Database\Seeders;

use App\Modules\CMS\Models\Menu;
use App\Modules\CMS\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        /* ════════════════════════════════════════════════
         * MAIN NAVIGATION (Header)
         * ════════════════════════════════════════════════ */
        $mainNav = Menu::create([
            'id'       => (string) \Illuminate\Support\Str::uuid(),
            'name'     => 'Main Navigation',
            'slug'     => 'main-nav',
            'location' => 'header',
        ]);

        // ── Ride (Mega Menu Group) ──
        $ride = MenuItem::create([
            'id'         => (string) \Illuminate\Support\Str::uuid(),
            'menu_id'    => $mainNav->id,
            'label'      => 'Ride',
            'url'        => '#ride',
            'type'       => 'group',
            'icon'       => 'car',
            'sort_order' => 1,
        ]);

        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $ride->id,
            'label'       => 'Request a Ride',
            'url'         => '#ride',
            'description' => 'Go anywhere in Ghana with a tap',
            'icon'        => 'location',
            'type'        => 'link',
            'sort_order'  => 1,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $ride->id,
            'label'       => 'Reserve',
            'url'         => '#ride',
            'description' => 'Schedule rides up to 30 days ahead',
            'icon'        => 'clock',
            'type'        => 'link',
            'sort_order'  => 2,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $ride->id,
            'label'       => 'Airport Transfer',
            'url'         => '#ride',
            'description' => 'Premium airport pickups and drop-offs',
            'icon'        => 'plane',
            'type'        => 'link',
            'sort_order'  => 3,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $ride->id,
            'label'       => 'See prices',
            'url'         => '#ride',
            'type'        => 'cta_button',
            'css_class'   => 'bg-brand text-white',
            'sort_order'  => 4,
        ]);

        // ── Drive (Mega Menu Group) ──
        $drive = MenuItem::create([
            'menu_id'    => $mainNav->id,
            'label'      => 'Drive',
            'url'        => '#drive',
            'type'       => 'group',
            'icon'       => 'steering-wheel',
            'sort_order' => 2,
        ]);

        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $drive->id,
            'label'       => 'Drive with WADEXPRO',
            'url'         => '#drive',
            'description' => 'Earn money on your own schedule',
            'icon'        => 'money',
            'type'        => 'link',
            'sort_order'  => 1,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $drive->id,
            'label'       => 'Vehicle Requirements',
            'url'         => '#',
            'description' => 'See what you need to get started',
            'icon'        => 'checklist',
            'type'        => 'link',
            'sort_order'  => 2,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $drive->id,
            'label'       => 'Driver Safety',
            'url'         => '#safety',
            'description' => 'How we protect our driver partners',
            'icon'        => 'shield',
            'type'        => 'link',
            'sort_order'  => 3,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $drive->id,
            'label'       => 'Start earning',
            'url'         => '#drive',
            'type'        => 'cta_button',
            'css_class'   => 'bg-brand text-white',
            'sort_order'  => 4,
        ]);

        // ── Deliver (Mega Menu Group) ──
        $deliver = MenuItem::create([
            'menu_id'    => $mainNav->id,
            'label'      => 'Deliver',
            'url'        => '#deliver',
            'type'       => 'group',
            'icon'       => 'package',
            'sort_order' => 3,
        ]);

        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $deliver->id,
            'label'       => 'Send a Package',
            'url'         => '#deliver',
            'description' => 'Fast, affordable same-day delivery',
            'icon'        => 'package',
            'type'        => 'link',
            'sort_order'  => 1,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $deliver->id,
            'label'       => 'Bulk Delivery',
            'url'         => '#deliver',
            'description' => 'Multi-stop orchestrated delivery runs',
            'icon'        => 'truck',
            'type'        => 'link',
            'sort_order'  => 2,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $deliver->id,
            'label'       => 'Business Logistics',
            'url'         => '#business',
            'description' => 'Enterprise-grade fleet management',
            'icon'        => 'building',
            'type'        => 'link',
            'sort_order'  => 3,
        ]);

        // ── Business (Mega Menu Group) ──
        $business = MenuItem::create([
            'menu_id'    => $mainNav->id,
            'label'      => 'Business',
            'url'        => '#business',
            'type'       => 'group',
            'icon'       => 'briefcase',
            'sort_order' => 4,
        ]);

        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $business->id,
            'label'       => 'WADEXPRO for Business',
            'url'         => '#business',
            'description' => 'Corporate transportation management',
            'icon'        => 'building',
            'type'        => 'link',
            'sort_order'  => 1,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $business->id,
            'label'       => 'API Integration',
            'url'         => '#',
            'description' => 'Build logistics into your workflow',
            'icon'        => 'code',
            'type'        => 'link',
            'sort_order'  => 2,
        ]);

        // ── About (Mega Menu Group) ──
        $about = MenuItem::create([
            'menu_id'    => $mainNav->id,
            'label'      => 'About',
            'url'        => '#',
            'type'       => 'group',
            'icon'       => 'info',
            'sort_order' => 5,
        ]);

        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $about->id,
            'label'       => 'Safety',
            'url'         => '#safety',
            'description' => 'Your security is our top priority',
            'icon'        => 'shield',
            'type'        => 'link',
            'sort_order'  => 1,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $about->id,
            'label'       => 'Our Story',
            'url'         => '#',
            'description' => 'The mission behind WADEXPRO',
            'icon'        => 'heart',
            'type'        => 'link',
            'sort_order'  => 2,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $about->id,
            'label'       => 'Careers',
            'url'         => '#',
            'description' => 'Join the team redefining logistics',
            'icon'        => 'users',
            'type'        => 'link',
            'sort_order'  => 3,
        ]);
        MenuItem::create([
            'menu_id'     => $mainNav->id,
            'parent_id'   => $about->id,
            'label'       => 'Blog',
            'url'         => '#',
            'description' => 'News, insights, and updates',
            'icon'        => 'document',
            'type'        => 'link',
            'sort_order'  => 4,
        ]);


        /* ════════════════════════════════════════════════
         * FOOTER NAVIGATION
         * ════════════════════════════════════════════════ */
        $footer = Menu::create([
            'name'     => 'Footer Navigation',
            'slug'     => 'footer-nav',
            'location' => 'footer',
        ]);

        // Company Group
        $co = MenuItem::create(['menu_id' => $footer->id, 'label' => 'Company', 'type' => 'group', 'group_label' => 'Company', 'sort_order' => 1]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $co->id, 'label' => 'About us', 'url' => '#', 'type' => 'link', 'sort_order' => 1]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $co->id, 'label' => 'Our offerings', 'url' => '#', 'type' => 'link', 'sort_order' => 2]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $co->id, 'label' => 'How it works', 'url' => '#', 'type' => 'link', 'sort_order' => 3]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $co->id, 'label' => 'Careers', 'url' => '#', 'type' => 'link', 'sort_order' => 4]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $co->id, 'label' => 'Blog', 'url' => '#', 'type' => 'link', 'sort_order' => 5]);

        // Products Group
        $products = MenuItem::create(['menu_id' => $footer->id, 'label' => 'Products', 'type' => 'group', 'group_label' => 'Products', 'sort_order' => 2]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $products->id, 'label' => 'Ride', 'url' => '#ride', 'type' => 'link', 'sort_order' => 1]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $products->id, 'label' => 'Deliver', 'url' => '#deliver', 'type' => 'link', 'sort_order' => 2]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $products->id, 'label' => 'Drive', 'url' => '#drive', 'type' => 'link', 'sort_order' => 3]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $products->id, 'label' => 'Business', 'url' => '#business', 'type' => 'link', 'sort_order' => 4]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $products->id, 'label' => 'Reserve', 'url' => '#', 'type' => 'link', 'sort_order' => 5]);

        // Safety Group
        $safety = MenuItem::create(['menu_id' => $footer->id, 'label' => 'Safety', 'type' => 'group', 'group_label' => 'Safety', 'sort_order' => 3]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $safety->id, 'label' => 'Safety Center', 'url' => '#safety', 'type' => 'link', 'sort_order' => 1]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $safety->id, 'label' => 'Community Guidelines', 'url' => '#', 'type' => 'link', 'sort_order' => 2]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $safety->id, 'label' => 'Insurance', 'url' => '#', 'type' => 'link', 'sort_order' => 3]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $safety->id, 'label' => 'Help Center', 'url' => '#', 'type' => 'link', 'sort_order' => 4]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $safety->id, 'label' => 'Contact us', 'url' => '#', 'type' => 'link', 'sort_order' => 5]);

        // Legal Group
        $legal = MenuItem::create(['menu_id' => $footer->id, 'label' => 'Legal', 'type' => 'group', 'group_label' => 'Legal', 'sort_order' => 4]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $legal->id, 'label' => 'Terms of Service', 'url' => '#', 'type' => 'link', 'sort_order' => 1]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $legal->id, 'label' => 'Privacy Policy', 'url' => '#', 'type' => 'link', 'sort_order' => 2]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $legal->id, 'label' => 'Cookie Settings', 'url' => '#', 'type' => 'link', 'sort_order' => 3]);
        MenuItem::create(['menu_id' => $footer->id, 'parent_id' => $legal->id, 'label' => 'Accessibility', 'url' => '#', 'type' => 'link', 'sort_order' => 4]);
    }
}
