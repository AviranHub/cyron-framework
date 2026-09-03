<?php

namespace App\Http\Controllers\Author;

use App\Http\Controller;
use App\Core\Authentication\Auth;
use App\Models\Book;
use App\Models\UserActivity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower((string)($user->role ?? '')), ['admin', 'superadmin'], true);
        $booksQuery = Book::query();
        if (!$isAdmin) $booksQuery->where('author_id', '=', $user->id);
        $books = $booksQuery->orderBy('created_at', 'desc')->limit(6)->get();
        $activities = UserActivity::query()->where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();
        return view('author.dashboard', compact('user', 'books', 'activities', 'isAdmin'));
    }

    public function books()
    {
        $user = Auth::user();
        $query = Book::query();
        if (!in_array(strtolower((string)($user->role ?? '')), ['admin', 'superadmin'], true)) {
            $query->where('author_id', '=', $user->id);
        }
        $books = $query->orderBy('created_at', 'desc')->paginate(12);
        return view('author.books.index', compact('user', 'books'));
    }
}
