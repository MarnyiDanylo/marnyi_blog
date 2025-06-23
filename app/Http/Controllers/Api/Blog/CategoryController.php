<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::all();

        return $categories;
    }

    public function show($id)
    {
        $category = BlogCategory::with('posts')
            ->find($id);

        return response()->json([
            'data' => $category,
            'success' => true
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255|unique:blog_categories,title',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:blog_categories,slug'
        ]);

        try {
            $category = BlogCategory::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'slug' => $validated['slug'] ?? \Str::slug($validated['title'])
            ]);

            return response()->json([
                'message' => 'Категорію створено',
                'data' => $category,
                'success' => true
            ], 201);

        } catch (\Exception $e) {
            Log::error('Category store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не вдалося створити категорію',
                'success' => false
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|min:3|max:255|unique:blog_categories,title,'.$id,
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:blog_categories,slug,'.$id
        ]);

        try {
            $category = BlogCategory::findOrFail($id);

            $category->update([
                'title' => $validated['title'] ?? $category->title,
                'description' => $validated['description'] ?? $category->description,
                'slug' => $validated['slug'] ?? $category->slug
            ]);

            return response()->json([
                'message' => 'Категорію оновлено',
                'data' => $category,
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не вдалося оновити категорію',
                'success' => false
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);

            $category->delete();

            return response()->json([
                'message' => 'Категорію видалено',
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Category destroy error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не вдалося видалити категорію',
                'success' => false
            ], 500);
        }
    }
}
