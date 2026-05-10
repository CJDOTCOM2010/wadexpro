<?php

namespace App\Modules\CMS\Services;

use App\Modules\CMS\Models\CmsBlock;
use App\Modules\CMS\Models\CmsPage;
use App\Modules\CMS\Models\CmsSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CmsService
{
    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Get a published page by slug with all visible sections and blocks.
     * Results are cached for performance.
     */
    public function getPageBySlug(string $slug, ?string $region = null): ?CmsPage
    {
        $cacheKey = "cms_page:{$slug}:{$region}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $region) {
            return CmsPage::query()
                ->published()
                ->forRegion($region)
                ->where('slug', $slug)
                ->with([
                    'sections' => function ($query) {
                        $query->visible()->orderBy('sort_order');
                    },
                    'sections.blocks' => function ($query) {
                        $query->orderBy('sort_order');
                    },
                ])
                ->first();
        });
    }

    /**
     * Get all published pages (for menu/navigation building).
     */
    public function getPublishedPages(?string $region = null): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "cms_pages_list:{$region}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($region) {
            return CmsPage::query()
                ->published()
                ->forRegion($region)
                ->orderBy('sort_order')
                ->get(['id', 'title', 'slug', 'template', 'region']);
        });
    }

    /**
     * Create a new CMS page.
     */
    public function createPage(array $data): CmsPage
    {
        if (isset($data['title']) && is_array($data['title'])) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']['en'] ?? reset($data['title']));
        } elseif (isset($data['title'])) {
             $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        }

        $page = CmsPage::create($data);
        $this->clearPageCache($page->slug);

        return $page;
    }

    /**
     * Update an existing CMS page.
     */
    public function updatePage(CmsPage $page, array $data): CmsPage
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            if (is_array($data['title'])) {
                $data['slug'] = Str::slug($data['title']['en'] ?? reset($data['title']));
            } else {
                $data['slug'] = Str::slug($data['title']);
            }
        }

        $page->update($data);
        $this->clearPageCache($page->slug);

        return $page->fresh();
    }

    /**
     * Delete a CMS page and all its sections/blocks.
     */
    public function deletePage(CmsPage $page): void
    {
        $slug = $page->slug;
        $page->delete();
        $this->clearPageCache($slug);
    }

    /**
     * Create a section within a page.
     */
    public function createSection(CmsPage $page, array $data): CmsSection
    {
        $data['page_id'] = $page->id;

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $page->sections()->max('sort_order') + 1;
        }

        $section = CmsSection::create($data);
        $this->clearPageCache($page->slug);

        return $section;
    }

    /**
     * Update a section.
     */
    public function updateSection(CmsSection $section, array $data): CmsSection
    {
        $section->update($data);
        $this->clearPageCache($section->page->slug);

        return $section->fresh();
    }

    /**
     * Delete a section and reorder remaining sections.
     */
    public function deleteSection(CmsSection $section): void
    {
        $page = $section->page;
        $section->delete();

        // Re-sequence remaining sections
        $page->sections()->orderBy('sort_order')->get()
            ->each(function (CmsSection $s, int $index) {
                $s->update(['sort_order' => $index]);
            });

        $this->clearPageCache($page->slug);
    }

    /**
     * Reorder sections within a page.
     *
     * @param array<int, string> $sectionIds Ordered array of section UUIDs
     */
    public function reorderSections(CmsPage $page, array $sectionIds): void
    {
        foreach ($sectionIds as $index => $sectionId) {
            CmsSection::where('id', $sectionId)
                ->where('page_id', $page->id)
                ->update(['sort_order' => $index]);
        }

        $this->clearPageCache($page->slug);
    }

    /**
     * Create a block within a section.
     */
    public function createBlock(CmsSection $section, array $data): CmsBlock
    {
        $data['section_id'] = $section->id;

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $section->blocks()->max('sort_order') + 1;
        }

        $block = CmsBlock::create($data);
        $this->clearPageCache($section->page->slug);

        return $block;
    }

    /**
     * Update a block.
     */
    public function updateBlock(CmsBlock $block, array $data): CmsBlock
    {
        $block->update($data);
        $this->clearPageCache($block->section->page->slug);

        return $block->fresh();
    }

    /**
     * Delete a block.
     */
    public function deleteBlock(CmsBlock $block): void
    {
        $section = $block->section;
        $block->delete();
        $this->clearPageCache($section->page->slug);
    }

    /**
     * Seed the default landing page with all required sections.
     */
    public function seedDefaultLandingPage(): CmsPage
    {
        $page = $this->createPage([
            'title' => ['en' => 'Home'],
            'slug' => 'home',
            'meta_description' => ['en' => 'WadExp - Your reliable ride-hailing, courier, and mobility platform in Ghana.'],
            'meta_keywords' => 'ride hailing, courier, delivery, Ghana, Accra, mobility',
            'status' => 'published',
            'template' => 'landing',
        ]);

        $sections = [
            [
                'type' => 'hero',
                'title' => 'Hero Section',
                'blocks' => [
                    ['type' => 'heading', 'key' => 'title', 'content' => ['en' => 'Go anywhere with WadExp']],
                    ['type' => 'paragraph', 'key' => 'subtitle', 'content' => ['en' => 'Request a ride, get picked up by a nearby driver, and enjoy a comfortable journey to your destination.']],
                    ['type' => 'button', 'key' => 'cta_ride', 'content' => ['en' => 'Request a Ride'], 'link_url' => '/ride'],
                    ['type' => 'button', 'key' => 'cta_driver', 'content' => ['en' => 'Become a Driver'], 'link_url' => '/driver/register'],
                ],
            ],
            [
                'type' => 'services',
                'title' => 'Our Services',
                'blocks' => [
                    ['type' => 'icon_card', 'key' => 'ride', 'content' => ['en' => 'Ride'], 'properties' => ['icon' => 'car', 'description' => 'Get a reliable ride in minutes. Choose from economy, comfort, or XL.']],
                    ['type' => 'icon_card', 'key' => 'delivery', 'content' => ['en' => 'Delivery'], 'properties' => ['icon' => 'package', 'description' => 'Send packages across the city with real-time tracking.']],
                    ['type' => 'icon_card', 'key' => 'courier', 'content' => ['en' => 'Courier'], 'properties' => ['icon' => 'bike', 'description' => 'Fast motorcycle courier service for urgent deliveries.']],
                ],
            ],
            [
                'type' => 'how_it_works',
                'title' => 'How It Works',
                'blocks' => [
                    ['type' => 'step', 'key' => 'step_1', 'content' => ['en' => 'Set your pickup location'], 'properties' => ['step_number' => 1, 'description' => 'Open the app and enter your destination. We will show you the estimated fare upfront.']],
                    ['type' => 'step', 'key' => 'step_2', 'content' => ['en' => 'Get matched with a driver'], 'properties' => ['step_number' => 2, 'description' => 'A nearby driver will accept your request. Track their arrival in real-time.']],
                    ['type' => 'step', 'key' => 'step_3', 'content' => ['en' => 'Arrive safely'], 'properties' => ['step_number' => 3, 'description' => 'Enjoy your ride and pay seamlessly through the app. Rate your experience.']],
                ],
            ],
            [
                'type' => 'benefits',
                'title' => 'Why Choose WadExp',
                'blocks' => [
                    ['type' => 'icon_card', 'key' => 'safety', 'content' => ['en' => 'Safety First'], 'properties' => ['icon' => 'shield', 'description' => 'All drivers are verified. SOS button and ride sharing for your safety.']],
                    ['type' => 'icon_card', 'key' => 'affordable', 'content' => ['en' => 'Affordable Pricing'], 'properties' => ['icon' => 'wallet', 'description' => 'Transparent pricing with no hidden fees. Pay with mobile money or card.']],
                    ['type' => 'icon_card', 'key' => 'reliable', 'content' => ['en' => 'Always Reliable'], 'properties' => ['icon' => 'clock', 'description' => 'Available 24/7 with drivers across all major cities in Ghana.']],
                    ['type' => 'icon_card', 'key' => 'tracking', 'content' => ['en' => 'Real-Time Tracking'], 'properties' => ['icon' => 'map-pin', 'description' => 'Track your ride or delivery in real-time from pickup to destination.']],
                ],
            ],
            [
                'type' => 'driver_onboarding',
                'title' => 'Drive with WadExp',
                'blocks' => [
                    ['type' => 'heading', 'key' => 'title', 'content' => ['en' => 'Earn on your own schedule']],
                    ['type' => 'paragraph', 'key' => 'subtitle', 'content' => ['en' => 'Join thousands of drivers earning a reliable income. Set your own hours, be your own boss.']],
                    ['type' => 'stat', 'key' => 'avg_earnings', 'content' => ['en' => 'GHS 800+'], 'properties' => ['label' => 'Average weekly earnings']],
                    ['type' => 'stat', 'key' => 'drivers', 'content' => ['en' => '5000+'], 'properties' => ['label' => 'Active drivers']],
                    ['type' => 'button', 'key' => 'cta', 'content' => ['en' => 'Start Driving'], 'link_url' => '/driver/register'],
                ],
            ],
            [
                'type' => 'business',
                'title' => 'WadExp for Business',
                'blocks' => [
                    ['type' => 'heading', 'key' => 'title', 'content' => ['en' => 'Enterprise mobility solutions']],
                    ['type' => 'paragraph', 'key' => 'description', 'content' => ['en' => 'Streamline transportation for your organization. Dedicated account management, priority support, and detailed reporting.']],
                    ['type' => 'button', 'key' => 'cta', 'content' => ['en' => 'Get Started'], 'link_url' => '/business'],
                ],
            ],
            [
                'type' => 'app_download',
                'title' => 'Download the App',
                'blocks' => [
                    ['type' => 'heading', 'key' => 'title', 'content' => ['en' => 'Get moving with WadExp']],
                    ['type' => 'paragraph', 'key' => 'subtitle', 'content' => ['en' => 'Download the WadExp app and start riding today.']],
                    ['type' => 'button', 'key' => 'android', 'content' => ['en' => 'Google Play'], 'link_url' => '#', 'properties' => ['store' => 'google_play']],
                    ['type' => 'button', 'key' => 'ios', 'content' => ['en' => 'App Store'], 'link_url' => '#', 'properties' => ['store' => 'app_store']],
                ],
            ],
            [
                'type' => 'region_content',
                'title' => 'Available in Ghana',
                'settings' => ['region' => 'GH'],
                'blocks' => [
                    ['type' => 'heading', 'key' => 'title', 'content' => ['en' => 'Serving cities across Ghana']],
                    ['type' => 'paragraph', 'key' => 'cities', 'content' => ['en' => 'Accra, Kumasi, Takoradi, Tamale, Cape Coast, and expanding.']],
                    ['type' => 'paragraph', 'key' => 'payment_info', 'content' => ['en' => 'Pay with MTN Mobile Money, Vodafone Cash, AirtelTigo Money, or cards via Paystack.']],
                ],
            ],
        ];

        foreach ($sections as $index => $sectionData) {
            $blocks = $sectionData['blocks'] ?? [];
            unset($sectionData['blocks']);
            $sectionData['sort_order'] = $index;

            $section = $this->createSection($page, $sectionData);

            foreach ($blocks as $blockIndex => $blockData) {
                $blockData['sort_order'] = $blockIndex;
                $this->createBlock($section, $blockData);
            }
        }

        return $page;
    }

    /**
     * Clear all caches related to a page slug.
     */
    private function clearPageCache(string $slug): void
    {
        Cache::forget("cms_page:{$slug}:");
        Cache::forget("cms_page:{$slug}:GH");
        Cache::forget('cms_pages_list:');
        Cache::forget('cms_pages_list:GH');
    }
}
