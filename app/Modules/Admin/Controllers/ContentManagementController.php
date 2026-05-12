<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CMS\Models\BlogPost;
use App\Modules\CMS\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentManagementController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // BLOG POSTS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Blog posts listing.
     */
    public function blog(Request $request)
    {
        $query = BlogPost::with('author')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(20)->withQueryString();

        $stats = [
            'published' => BlogPost::where('status', 'published')->count(),
            'draft'     => BlogPost::where('status', 'draft')->count(),
            'total_views' => BlogPost::sum('view_count'),
        ];

        return view('admin.cms_blog', compact('posts', 'stats'));
    }

    /**
     * Store a new blog post.
     */
    public function storeBlog(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'category'         => 'nullable|string|max:60',
            'tags'             => 'nullable|string',
            'status'           => 'required|in:draft,published',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'cover_image'      => 'nullable|image|max:4096',
        ]);

        $data['slug']      = Str::slug($data['title']) . '-' . now()->timestamp;
        $data['author_id'] = auth('admin')->id();
        $data['tags']      = isset($data['tags']) ? explode(',', $data['tags']) : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image_url'] = $request->file('cover_image')->store('blog', 'public');
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        unset($data['cover_image']);
        BlogPost::create($data);

        return back()->with('success', "Blog post '{$data['title']}' saved.");
    }

    /**
     * Update a blog post.
     */
    public function updateBlog(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'category'         => 'nullable|string|max:60',
            'status'           => 'required|in:draft,published',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if ($data['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return back()->with('success', "Blog post updated.");
    }

    /**
     * Delete a blog post.
     */
    public function destroyBlog($id)
    {
        BlogPost::findOrFail($id)->delete();
        return back()->with('success', "Blog post deleted.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FAQ
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * FAQ listing.
     */
    public function faq(Request $request)
    {
        $faqs = Faq::orderBy('audience')->orderBy('sort_order')->paginate(50);

        return view('admin.cms_faq', compact('faqs'));
    }

    /**
     * Store a new FAQ.
     */
    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:50',
            'audience'   => 'required|in:all,customer,driver',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = true;
        Faq::create($data);

        return back()->with('success', "FAQ added successfully.");
    }

    /**
     * Update an FAQ entry.
     */
    public function updateFaq(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $data = $request->validate([
            'question'   => 'required|string',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:50',
            'audience'   => 'required|in:all,customer,driver',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $faq->update($data);

        return back()->with('success', "FAQ updated.");
    }

    /**
     * Delete an FAQ.
     */
    public function destroyFaq($id)
    {
        Faq::findOrFail($id)->delete();
        return back()->with('success', "FAQ deleted.");
    }
}
