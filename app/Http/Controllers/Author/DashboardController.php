<?php

namespace App\Http\Controllers\Author;

use App\Http\Controller;
use Cyron\Authentication\Auth;
use App\Models\Book;
use App\Models\UserActivity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $isAdmin = in_array(strtolower((string)($user->role ?? '')), ['admin', 'superadmin'], true);
        $booksQuery = Book::query();
        if (!$isAdmin) $booksQuery->where('author_id', '=', $user->id);
        $books = $booksQuery->orderBy('created_at', 'desc')->limit(6)->get();
        $activities = UserActivity::query()->where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();
        $catalogQuery = Book::query();
        if (!$isAdmin) $catalogQuery->where('author_id', '=', $user->id);
        $catalog = $catalogQuery->get();
        $stats = ['books' => 0, 'views' => 0, 'audio' => 0, 'published' => 0];
        foreach ($catalog as $book) {
            $stats['books']++;
            $stats['views'] += (int) ($book->views ?? 0);
            if ((int) ($book->is_audio ?? 0) === 1) $stats['audio']++;
            if (($book->status ?? null) === 'published') $stats['published']++;
        }
        return view('author.dashboard', compact('user', 'books', 'activities', 'isAdmin', 'stats'));
    }

    public function books()
    {
        $user = Auth::user();
        $query = Book::query();
        if (!in_array(strtolower((string)($user->role ?? '')), ['admin', 'superadmin'], true)) {
            $query->where('author_id', '=', $user->id);
        }
        $search = trim((string) request()->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'LIKE', '%' . $search . '%')->orWhere('author_name', 'LIKE', '%' . $search . '%');
            });
        }
        $status = trim((string) request()->input('status', ''));
        if (in_array($status, ['draft', 'published'], true)) $query->where('status', '=', $status);
        $books = $query->orderBy('created_at', 'desc')->paginate(12);
        return view('author.books.index', compact('user', 'books', 'search', 'status'));
    }
}
