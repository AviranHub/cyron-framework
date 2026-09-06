<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\Article;

class BlogController extends Controller
{
    public function index()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('blog/index', compact('articles'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->first();
        if (!$article) abort(404);

        return view('blog/post', compact('article'));
    }
}
