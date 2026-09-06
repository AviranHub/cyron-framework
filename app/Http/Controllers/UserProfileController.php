<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\Book;
use App\Models\User;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', urldecode($username))->where('status', 'active')->first();
        if (!$user) abort(404);

        $books = Book::where('author_id', $user->id)
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->get();

        if (count($books) === 0) {
            $books = Book::where('author_name', $user->name)
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->get();
        }

        return view('user/profile', compact('user', 'books'));
    }
}
