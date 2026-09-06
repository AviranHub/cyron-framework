<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\Book;
use App\Models\BookPart;
use App\Models\Library;
use Cyron\Authentication\Auth;
use Cyron\Http\Request;
use Cyron\Http\Response;

class ReaderController extends Controller
{
    public function show(Request $request, $slug)
    {
        $book = Book::where('slug', $slug)->first();
        if (!$book) return redirect()->route('books')->with('error', 'کتاب پیدا نشد.');

        $userId = Auth::id();
        $hasAccess = (float) ($book->price ?? 0) <= 0
            || Library::where('user_id', $userId)->where('book_id', $book->id)->first();
        if (!$hasAccess) return redirect()->route('book.buy', ['slug' => $slug]);

        $page = max(1, (int) $request->query('page', 1));
        $progress = \App\Models\ReadingProgress::where('user_id', $userId)->where('book_id', $book->id)->first();
        return view('book/reader', ['book' => $book, 'initialPage' => (int) ($progress->last_page ?? $page)]);
    }

    public function pages($bookId)
    {
        $book = Book::find((int) $bookId);
        $userId = Auth::id();
        if (!$book) return Response::notFound('کتاب پیدا نشد.');

        $hasAccess = (float) ($book->price ?? 0) <= 0
            || Library::where('user_id', $userId)->where('book_id', $book->id)->first();
        if (!$hasAccess) return Response::forbidden('دسترسی به این کتاب مجاز نیست.');

        $pages = [];
        foreach (BookPart::where('book_id', $book->id)->orderBy('page_id')->get() as $part) {
            $pages[] = [
                'id' => (int) $part->id,
                'page_id' => (int) $part->page_id,
                'page_name' => $part->page_name,
                'text' => $part->text,
            ];
        }
        return Response::success($pages, 'Book pages retrieved successfully');
        }
    }
