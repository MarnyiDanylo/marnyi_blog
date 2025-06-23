<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();

        return $posts;
    }

    public function show($id)
    {
        $post = BlogPost::with(['user', 'category'])
            ->find($id);

        return response()->json([
            'data' => $post,
            'success' => true
        ]);
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'category_id' => 'required|integer|exists:blog_categories,id',
        ]);

        $post = BlogPost::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'content_raw' => strip_tags($validated['content']),
            'category_id' => $validated['category_id'],
            'user_id' => auth()->id() ?? 1
        ]);

        return response()->json([
            'message' => 'Статтю створено',
            'post' => $post
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'category_id' => 'required|integer|exists:blog_categories,id',
        ]);

        $post = BlogPost::findOrFail($id);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'content_raw' => strip_tags($validated['content']),
            'content_html' => $validated['content'],
            'category_id' => $validated['category_id'],
        ]);

        return response()->json([
            'message' => 'Статтю оновлено',
            'post' => $post->fresh()
        ]);
    }
}
