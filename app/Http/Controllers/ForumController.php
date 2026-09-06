<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use Cyron\Authentication\Auth;
use Cyron\Analytics\ActivityTracker;
use Cyron\Http\Request;
use Cyron\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::query()->orderBy('sort_order')->orderBy('name')->get();
        $topics = ForumTopic::query()->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        return view('forum/index', compact('categories', 'topics'));
    }

    public function category($slug)
    {
        $category = ForumCategory::where('slug', $slug)->first();
        if (!$category) abort(404);
        $topics = ForumTopic::where('category_id', $category->id)
            ->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        return view('forum/category', compact('category', 'topics'));
    }

    public function show($slug)
    {
        $topic = ForumTopic::where('slug', $slug)->first();
        if (!$topic) abort(404);
        $topic->update(['views' => (int) $topic->views + 1]);
        ActivityTracker::record('forum.topic_viewed', ['subject_type' => ForumTopic::class, 'subject_id' => (int) $topic->id]);
        $posts = ForumPost::where('topic_id', $topic->id)->orderBy('created_at')->paginate(20);
        return view('forum/topic', compact('topic', 'posts'));
    }

    public function create()
    {
        return view('forum/create', ['categories' => ForumCategory::query()->orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        $errors = $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
        ]);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();
        $category = ForumCategory::find((int) $request->input('category_id'));
        if (!$category) return redirect()->back()->with('error', 'دسته‌بندی پیدا نشد.')->withInput();

        $title = trim((string) $request->input('title'));
        $slug = Str::slug($title) . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        $topic = ForumTopic::create([
            'category_id' => $category->id,
            'user_id' => Auth::id(),
            'title' => $title,
            'slug' => $slug,
            'content' => trim((string) $request->input('content')),
            'views' => 0,
            'is_pinned' => 0,
            'is_locked' => 0,
        ]);
        return redirect()->route('forum.topic', ['slug' => $topic->slug]);
    }

    public function reply(Request $request, $slug)
    {
        $topic = ForumTopic::where('slug', $slug)->first();
        if (!$topic) abort(404);
        if ((int) $topic->is_locked === 1) return redirect()->back()->with('error', 'این موضوع بسته شده است.');
        $errors = $request->validate(['content' => 'required|string|min:2|max:10000']);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();
        ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => trim((string) $request->input('content')),
            'likes' => 0,
            'dislikes' => 0,
        ]);
        return redirect()->route('forum.topic', ['slug' => $slug]);
    }
}
