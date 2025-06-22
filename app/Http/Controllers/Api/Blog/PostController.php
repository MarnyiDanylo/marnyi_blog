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
}
