<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\OnboardingSlide;
use App\Modules\Admin\Models\SplashScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    /**
     * Display onboarding manager for a specific app type.
     */
    public function index(string $appType)
    {
        if (! in_array($appType, ['customer', 'driver'])) {
            abort(404);
        }

        $slides = OnboardingSlide::forApp($appType)->ordered()->get();

        // Initialize or fetch splash screen config
        $splash = SplashScreen::firstOrCreate(
            ['app_type' => $appType],
            [
                'tagline' => 'Move. Deliver. Thrive.',
                'app_name' => 'WADEXPRO',
                'duration_ms' => 3000,
                'show_ripple' => true,
                'show_tagline' => true,
                'show_app_name' => true,
                'bg_color' => '#000B1E',
                'secondary_color' => '#FFB800',
            ]
        );

        $label = ucfirst($appType);

        return view('admin.settings.onboarding', compact('slides', 'splash', 'appType', 'label'));
    }

    /**
     * Update Splash Screen configuration.
     */
    public function updateSplash(Request $request)
    {
        $request->validate([
            'app_type' => 'required|in:customer,driver',
            'tagline' => 'required|string|max:255',
            'duration_ms' => 'required|integer|min:1000|max:10000',
            'bg_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,gif,mp4,mov,avi,webm|max:20480',
            'background' => 'nullable|file|mimes:png,jpg,jpeg,webp,gif,mp4,mov,avi,webm|max:51200',
        ]);

        $splash = SplashScreen::where('app_type', $request->app_type)->firstOrFail();

        $splash->tagline = $request->tagline;
        $splash->app_name = $request->app_name;
        $splash->duration_ms = $request->duration_ms;
        $splash->bg_color = $request->bg_color;
        $splash->secondary_color = $request->secondary_color;
        $splash->show_ripple = $request->boolean('show_ripple');
        $splash->show_logo = $request->boolean('show_logo');
        $splash->show_background = $request->boolean('show_background');
        $splash->show_tagline = $request->boolean('show_tagline');
        $splash->show_app_name = $request->boolean('show_app_name');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('splash', 'public');
            $mimeType = $file->getMimeType();
            $splash->logo_url = '/storage/'.$path;
            $splash->logo_media_type = str_contains($mimeType, 'video') ? 'video' : 'image';
        }

        if ($request->hasFile('background')) {
            $file = $request->file('background');
            $path = $file->store('splash', 'public');
            $mimeType = $file->getMimeType();
            $splash->background_url = '/storage/'.$path;
            $splash->background_media_type = str_contains($mimeType, 'video') ? 'video' : 'image';
        }

        $splash->save();

        return back()->with('success', 'Splash screen configuration updated.');
    }

    /**
     * Store a new onboarding slide.
     */
    public function store(Request $request)
    {
        $request->validate([
            'app_type' => 'required|in:customer,driver',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:30',
            'button_type' => 'required|string|in:'.implode(',', array_keys(OnboardingSlide::BUTTON_TYPES)),
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',
            'layout_style' => 'required|string|in:'.implode(',', array_keys(OnboardingSlide::LAYOUT_STYLES)),
            'bg_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'button_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $file = $request->file('image');
        $path = $file->store('onboarding', 'public');
        $mimeType = $file->getMimeType();
        $mediaType = str_contains($mimeType, 'video') ? 'video' : 'image';

        $maxOrder = OnboardingSlide::forApp($request->app_type)->max('sort_order') ?? 0;

        OnboardingSlide::create([
            'app_type' => $request->app_type,
            'title' => $request->title,
            'description' => $request->description,
            'button_text' => $request->button_text ?? 'Next',
            'button_type' => $request->button_type ?? 'action_below_text',
            'image_url' => '/storage/'.$path,
            'media_type' => $mediaType,
            'layout_style' => $request->layout_style,
            'bg_color' => $request->bg_color ?? '#FFFFFF',
            'text_color' => $request->text_color ?? '#000B1E',
            'button_color' => $request->button_color ?? '#FFB800',
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Onboarding slide created successfully.');
    }

    /**
     * Update an existing slide.
     */
    public function update(Request $request, string $id)
    {
        $slide = OnboardingSlide::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:30',
            'button_type' => 'required|string|in:'.implode(',', array_keys(OnboardingSlide::BUTTON_TYPES)),
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',
            'layout_style' => 'required|string|in:'.implode(',', array_keys(OnboardingSlide::LAYOUT_STYLES)),
            'bg_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'button_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $slide->title = $request->title;
        $slide->description = $request->description;
        $slide->button_text = $request->button_text ?? 'Next';
        $slide->button_type = $request->button_type ?? 'action_below_text';
        $slide->layout_style = $request->layout_style;
        $slide->bg_color = $request->bg_color ?? '#FFFFFF';
        $slide->text_color = $request->text_color ?? '#000B1E';
        $slide->button_color = $request->button_color ?? '#FFB800';

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slide->image_url) {
                $oldPath = str_replace('/storage/', '', $slide->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $file = $request->file('image');
            $path = $file->store('onboarding', 'public');
            $slide->image_url = '/storage/'.$path;
            $slide->media_type = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
        }

        $slide->save();

        return back()->with('success', 'Slide updated successfully.');
    }

    /**
     * Delete a slide.
     */
    public function destroy(string $id)
    {
        $slide = OnboardingSlide::findOrFail($id);

        if ($slide->image_url) {
            $oldPath = str_replace('/storage/', '', $slide->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $slide->delete();

        return back()->with('success', 'Slide removed successfully.');
    }

    /**
     * Toggle a slide's active state.
     */
    public function toggle(string $id)
    {
        $slide = OnboardingSlide::findOrFail($id);
        $slide->is_active = ! $slide->is_active;
        $slide->save();

        return back()->with('success', 'Slide '.($slide->is_active ? 'activated' : 'deactivated').'.');
    }

    /**
     * Reorder slides via AJAX.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|string',
        ]);

        foreach ($request->order as $index => $id) {
            OnboardingSlide::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Public API: Return active slides for a given app type.
     */
    public function apiIndex(string $appType)
    {
        if (! in_array($appType, ['customer', 'driver'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid app type'], 400);
        }

        try {
            $slides = OnboardingSlide::forApp($appType)
                ->active()
                ->ordered()
                ->get(['id', 'title', 'description', 'button_text', 'button_type', 'image_url', 'layout_style', 'sort_order', 'bg_color', 'text_color', 'button_color', 'media_type']);
        } catch (\Exception $e) {
            // WADEX-Guard: Fallback to basic columns if extended schema is not yet migrated
            $slides = OnboardingSlide::forApp($appType)
                ->active()
                ->ordered()
                ->get(['id', 'title', 'description', 'image_url', 'sort_order']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $slides,
        ]);
    }

    /**
     * Public API: Return splash screen config for a given app type.
     */
    public function apiSplash(string $appType)
    {
        if (! in_array($appType, ['customer', 'driver'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid app type'], 400);
        }

        $splash = SplashScreen::where('app_type', $appType)->first();

        return response()->json([
            'status' => 'success',
            'data' => $splash,
        ]);
    }
}
