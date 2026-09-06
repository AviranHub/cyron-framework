<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\AuditLog;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use Cyron\Authentication\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::query()->orderBy('sort_order')->orderBy('name')->get();
        $topics = ForumTopic::query()->orderBy('created_at', 'desc')->limit(10)->get();
        $posts = ForumPost::query()->orderBy('created_at', 'desc')->limit(10)->get();
        return view('admin.forum.index', compact('categories', 'topics', 'posts'));
    }

    public function topics()
    {
        $topics = ForumTopic::query()->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->paginate(30);
        $categories = $this->categoriesById($topics->items(), 'category_id');
        $users = $this->usersById($topics->items(), 'user_id');
        return view('admin.forum.topics', compact('topics', 'categories', 'users'));
    }

    public function posts()
    {
        $posts = ForumPost::query()->orderBy('created_at', 'desc')->paginate(30);
        $topics = $this->topicsById($posts->items());
        $users = $this->usersById($posts->items(), 'user_id');
        return view('admin.forum.posts', compact('posts', 'topics', 'users'));
    }

    public function toggleLock(int $id)
    {
        $topic = ForumTopic::find($id);
        if (!$topic) abort(404);
        $topic->update(['is_locked' => (int) !$topic->is_locked]);
        $this->audit('admin.forum.topic_lock_toggled', ['topic_id' => $id]);
        return redirect()->back();
    }

    public function togglePin(int $id)
    {
        $topic = ForumTopic::find($id);
        if (!$topic) abort(404);
        $topic->update(['is_pinned' => (int) !$topic->is_pinned]);
        $this->audit('admin.forum.topic_pin_toggled', ['topic_id' => $id]);
        return redirect()->back();
    }

    public function destroyTopic(int $id)
    {
        $topic = ForumTopic::find($id);
        if (!$topic) abort(404);
        $topic->delete();
        $this->audit('admin.forum.topic_deleted', ['topic_id' => $id]);
        return redirect()->back();
    }

    public function destroyPost(int $id)
    {
        $post = ForumPost::find($id);
        if (!$post) abort(404);
        $post->delete();
        $this->audit('admin.forum.post_deleted', ['post_id' => $id]);
        return redirect()->back();
    }

    private function categoriesById(iterable $items, string $field): array
    {
        $ids = [];
        foreach ($items as $item) $ids[(int) ($item->{$field} ?? 0)] = true;
        $result = [];
        foreach ($ids as $id => $_) if ($id && ($category = ForumCategory::find($id))) $result[$id] = $category;
        return $result;
    }

    private function topicsById(iterable $items): array
    {
        $result = [];
        foreach ($items as $item) if (($topic = ForumTopic::find((int) $item->topic_id))) $result[(int) $topic->id] = $topic;
        return $result;
    }

    private function usersById(iterable $items, string $field): array
    {
        $result = [];
        foreach ($items as $item) {
            $id = (int) ($item->{$field} ?? 0);
            if ($id && !isset($result[$id])) $result[$id] = User::find($id);
        }
        return $result;
    }

    private function audit(string $action, array $context): void
    {
        if (!Auth::id()) return;
        AuditLog::create([
            'actor_id' => (int) Auth::id(),
            'action' => $action,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE),
            'occurred_at' => date('Y-m-d H:i:s'),
        ]);
    }
}