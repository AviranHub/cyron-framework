<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\AuthorFollow;
use App\Models\AuthorSupport;
use App\Models\Book;
use Cyron\Authentication\Auth;
use Cyron\Http\Request;

class AuthorController extends Controller
{
    public function show($authorName)
    {
        $authorName = urldecode($authorName);
        $publishedOnly = Book::where('status', 'published')->count() > 0;
        $books = ($publishedOnly ? Book::where('status', 'published') : Book::query())
            ->where('author_name', $authorName)
            ->orderBy('views', 'desc')
            ->get();

        if (count($books) === 0) {
            abort(404);
        }

        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = (bool) AuthorFollow::where('user_id', Auth::id())
                ->where('author_name', $authorName)
                ->first();
        }

        return view('author/profile', [
            'authorName' => $authorName,
            'books' => $books,
            'isFollowing' => $isFollowing,
        ]);
    }

    public function toggleFollow(Request $request, $authorName)
    {
        $userId = Auth::id();
        $authorName = urldecode($authorName);
        $follow = AuthorFollow::where('user_id', $userId)
            ->where('author_name', $authorName)
            ->first();

        if ($follow) {
            $follow->delete();
            $message = 'دنبال‌کردن نویسنده متوقف شد.';
        } else {
            AuthorFollow::create(['user_id' => $userId, 'author_name' => $authorName]);
            $message = 'نویسنده به فهرست دنبال‌شده‌ها اضافه شد.';
        }

        return redirect()->route('author.profile', ['authorName' => $authorName])->with('success', $message);
    }

    public function support(Request $request, $authorName)
    {
        $amount = (int) $request->input('amount', 0);
        $authorName = urldecode($authorName);

        if ($amount < 1000) {
            return redirect()->back()->with('error', 'مبلغ حمایت باید حداقل ۱۰۰۰ تومان باشد.');
        }

        AuthorSupport::create([
            'user_id' => Auth::id(),
            'author_name' => $authorName,
            'amount' => $amount,
            'status' => 'pending',
            'message' => trim((string) $request->input('message', '')),
        ]);

        return redirect()->route('author.profile', ['authorName' => $authorName])->with(
            'notice',
            'درخواست حمایت ثبت شد و پس از اتصال به درگاه پرداخت قابل تکمیل است.'
        );
    }
}
