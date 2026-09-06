<?php

namespace App\Http\Controllers\User;

use App\Http\Controller;
use App\Models\Book;
use App\Models\Library;
use App\Models\ReadingProgress;
use App\Models\Shelf;
use Cyron\Authentication\Auth;
use Cyron\Http\Request;
use Cyron\Http\Response;

class LibraryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $entries = Library::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $books = [];

        foreach ($entries as $entry) {
            $book = Book::find($entry->book_id);
            if ($book) $books[] = $book;
        }

        $shelves = Shelf::where('user_id', $user->id)->orderBy('name')->get();

        return view('user/library', compact('user', 'books', 'shelves'));
    }

    public function storeShelf(Request $request)
    {
        $errors = $request->validate(['name' => 'required|string|min:2|max:191']);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        Shelf::create([
            'user_id' => Auth::id(),
            'name' => trim((string) $request->input('name')),
        ]);

        return redirect()->route('user.library')->with('success', 'قفسه جدید ایجاد شد.');
    }

    public function showShelf($id)
    {
        $user = Auth::user();
        $shelf = Shelf::where('id', $id)->where('user_id', $user->id)->first();
        if (!$shelf) return redirect()->route('user.library')->with('error', 'قفسه پیدا نشد.');

        $entries = Library::where('user_id', $user->id)->where('shelf_id', $shelf->id)->get();
        $books = [];
        foreach ($entries as $entry) {
            $book = Book::find($entry->book_id);
            if ($book) $books[] = $book;
        }

        return view('user/library', compact('user', 'books', 'shelf'));
    }

    public function progress(Request $request, $bookId)
    {
        $user = $request->user ?: Auth::user();
        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();

        return Response::success([
            'book_id' => (int) $bookId,
            'last_page' => (int) ($progress->last_page ?? 1),
        ]);
    }

    public function saveProgress(Request $request)
    {
        $errors = $request->validate([
            'book_id' => 'required|integer',
            'page' => 'required|integer|min:1',
        ]);
        if ($errors && $errors->any()) {
            return Response::validationError($errors->fieldErrors());
        }

        $user = $request->user ?: Auth::user();
        $book = Book::find((int) $request->input('book_id'));
        if (!$book) return Response::notFound('کتاب پیدا نشد.');

        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($progress) {
            $progress->update(['last_page' => (int) $request->input('page')]);
        } else {
            $progress = ReadingProgress::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'last_page' => (int) $request->input('page'),
            ]);
        }

        return Response::success([
            'book_id' => $book->id,
            'last_page' => (int) $progress->last_page,
        ], 'پیشرفت مطالعه ذخیره شد.');
    }
}