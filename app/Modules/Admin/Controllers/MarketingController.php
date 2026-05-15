<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // PROMO CODES
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List all promo codes / campaigns.
     */
    public function promos(Request $request)
    {
        try {
            $query = PromoCode::withCount('uses')->latest();

            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            if ($request->filled('search')) {
                $query->where('code', 'like', '%' . $request->search . '%');
            }

            $promos = $query->paginate(20)->withQueryString();

            $stats = [
                'active'  => PromoCode::where('is_active', true)->count(),
                'expired' => PromoCode::where('expires_at', '<', now())->count(),
                'total_uses' => PromoCode::sum('times_used') ?? 0,
            ];

            return view('admin.marketing_promos', compact('promos', 'stats'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Marketing Promos Error: ' . $e->getMessage());
            return view('admin.marketing_promos', [
                'promos' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'stats' => ['active' => 0, 'expired' => 0, 'total_uses' => 0],
            ])->with('error', 'Unable to load promotions.');
        }
    }

    /**
     * Store a new promo code.
     */
    public function storePromo(Request $request)
    {
        $data = $request->validate([
            'code'               => 'required|string|max:30|unique:promo_codes,code',
            'description'        => 'nullable|string|max:255',
            'type'               => 'required|in:percentage,fixed',
            'value'              => 'required|numeric|min:0',
            'max_discount'       => 'nullable|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after:starts_at',
        ]);

        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = true;

        PromoCode::create($data);

        return back()->with('success', "Promo code '{$data['code']}' created successfully.");
    }

    /**
     * Update an existing promo code.
     */
    public function updatePromo(Request $request, $id)
    {
        $promo = PromoCode::findOrFail($id);

        $data = $request->validate([
            'description'       => 'nullable|string|max:255',
            'type'              => 'required|in:percentage,fixed',
            'value'             => 'required|numeric|min:0',
            'max_discount'      => 'nullable|numeric|min:0',
            'min_order_amount'  => 'nullable|numeric|min:0',
            'max_uses'          => 'nullable|integer|min:1',
            'expires_at'        => 'nullable|date',
        ]);

        $promo->update($data);

        return back()->with('success', "Promo code updated.");
    }

    /**
     * Toggle active status of a promo code.
     */
    public function togglePromo($id)
    {
        $promo = PromoCode::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);

        $state = $promo->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Promo code {$state}.");
    }

    /**
     * Delete a promo code.
     */
    public function destroyPromo($id)
    {
        PromoCode::findOrFail($id)->delete();
        return back()->with('success', "Promo code deleted.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BANNERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List all banners.
     */
    public function banners(Request $request)
    {
        try {
            $query = \App\Models\Banner::latest();

            if ($request->filled('placement')) {
                $query->where('placement', $request->placement);
            }

            $banners = $query->paginate(20)->withQueryString();

            return view('admin.marketing_banners', compact('banners'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Marketing Banners Error: ' . $e->getMessage());
            return view('admin.marketing_banners', [
                'banners' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
            ])->with('error', 'Unable to load banners.');
        }
    }

    /**
     * Store a new banner.
     */
    public function storeBanner(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'image'       => 'required|image|max:4096',
            'link_url'    => 'nullable|url',
            'link_target' => 'in:_self,_blank',
            'placement'   => 'required|in:home,promotions,dashboard',
            'audience'    => 'required|in:all,customer,driver',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after:starts_at',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        // Store image
        $data['image_url'] = $request->file('image')->store('banners', 'public');

        unset($data['image']);
        $data['is_active'] = true;

        \App\Models\Banner::create($data);

        return back()->with('success', "Banner '{$data['title']}' uploaded successfully.");
    }

    /**
     * Toggle banner active status.
     */
    public function toggleBanner($id)
    {
        $banner = \App\Models\Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        $state = $banner->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Banner {$state}.");
    }

    /**
     * Delete a banner.
     */
    public function destroyBanner($id)
    {
        \App\Models\Banner::findOrFail($id)->delete();
        return back()->with('success', "Banner deleted.");
    }
}
