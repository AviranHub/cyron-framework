<?php

namespace App\Http\Controllers\Author;

use App\Http\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookPart;
use Cyron\Authentication\Auth;
use Cyron\Http\Request;
use Cyron\Support\Str;

class ContentController extends Controller
{
    public function createBook()
    {
        return view('author.books.create', [
            'user' => Auth::user(),
            'categories' => BookCategory::orderBy('name')->get(),
        ]);
    }

    public function storeBook(Request $request)
    {
        $errors = $request->validate([
            'title' => 'required|string|min:2|max:191',
            'price' => 'required|integer|min:0',
            'category_id' => 'nullable|integer',
        ]);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();
        if (!$request->hasFile('cover')) return redirect()->back()->with('error', 'جلد کتاب الزامی است.')->withInput();

        $user = Auth::user();
        $title = trim((string) $request->input('title'));
        $slug = Str::slug($title);
        if (Book::where('slug', $slug)->first()) $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        $coverFile = $request->fileData('cover');
        if (!is_array($coverFile)) return redirect()->back()->with('error', 'فایل جلد معتبر نیست.')->withInput();
        $cover = storage()->upload($coverFile, 'book');

        $book = Book::create([
            'title' => $title,
            'slug' => $slug,
            'author_id' => $user->id,
            'author_name' => $user->name,
            'publisher_id' => $user->id,
            'category_id' => $request->input('category_id') ?: null,
            'cover' => $cover,
            'pdf' => $request->input('pdf') ?: null,
            'introduction' => $request->input('introduction') ?: null,
            'description' => $request->input('description') ?: null,
            'price' => (int) $request->input('price'),
            'status' => 'draft',
            'access_type' => ((int) $request->input('price') > 0) ? 'purchase' : 'free',
            'is_audio' => 0, 'is_buy' => 1, 'is_show' => 0, 'is_download' => 0,
            'is_subscribe' => 0, 'is_read' => 1, 'is_public' => 0, 'is_bestseller' => 0,
            'pages' => 0, 'pages_count' => 0, 'likes' => 0, 'views' => 0, 'copen' => 0,
        ]);

        return redirect()->route('author.books.parts.create', ['id' => $book->id])
            ->with('success', 'کتاب به‌صورت پیش‌نویس ساخته شد؛ حالا صفحات آن را اضافه کنید.');
    }

    public function editBook($id)
    {
        if (!Auth::user()) return redirect()->route('login');
        return view('author.books.edit', [
            'user' => Auth::user(),
            'book' => $this->ownedBook($id),
            'categories' => BookCategory::orderBy('name')->get(),
        ]);
    }

    public function updateBook(Request $request, $id)
    {
        if (!Auth::user()) return redirect()->route('login');
        $book = $this->ownedBook($id);
        $errors = $request->validate([
            'title' => 'required|string|min:2|max:191',
            'price' => 'required|integer|min:0',
            'category_id' => 'nullable|integer',
            'pdf' => 'nullable|url',
            'introduction' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();

        $data = [
            'title' => trim((string) $request->input('title')),
            'category_id' => $request->input('category_id') ?: null,
            'pdf' => $request->input('pdf') ?: null,
            'introduction' => $request->input('introduction') ?: null,
            'description' => $request->input('description') ?: null,
            'price' => (int) $request->input('price'),
            'access_type' => ((int) $request->input('price') > 0) ? 'purchase' : 'free',
        ];
        if ($request->hasFile('cover')) {
            $coverFile = $request->fileData('cover');
            if (is_array($coverFile)) $data['cover'] = storage()->upload($coverFile, 'book');
        }
        $book->update($data);

        return redirect()->route('author.books')->with('success', 'اطلاعات اثر به‌روز شد.');
    }

    public function createPart($id)
    {
        if (!Auth::user()) return redirect()->route('login');
        $book = $this->ownedBook($id);
        $parts = BookPart::where('book_id', $book->id)->orderBy('page_id')->get();
        $selectedPage = (int) request()->input('page_id', 1);
        $selectedPart = null;
        foreach ($parts as $part) {
            if ((int) $part->page_id === $selectedPage) {
                $selectedPart = $part;
                break;
            }
        }
        return view('author.books.part', compact('book', 'parts', 'selectedPage', 'selectedPart') + ['user' => Auth::user()]);
    }

    public function publishBook($id)
    {
        if (!Auth::user()) return redirect()->route('login');
        $book = $this->ownedBook($id);
        $pages = BookPart::where('book_id', $book->id)->count();
        if (!$book->title || !$book->cover || $pages < 1) {
            return redirect()->back()->with('error', 'برای انتشار، جلد و حداقل یک صفحه برای کتاب لازم است.');
        }
        $book->update([
            'status' => 'published',
            'is_public' => 1,
            'is_show' => 1,
            'published_date' => date('Y-m-d'),
        ]);
        return redirect()->back()->with('success', 'کتاب منتشر شد.');
    }

    public function unpublishBook($id)
    {
        if (!Auth::user()) return redirect()->route('login');
        $book = $this->ownedBook($id);
        $book->update(['status' => 'draft', 'is_public' => 0, 'is_show' => 0]);
        return redirect()->back()->with('success', 'کتاب به پیش‌نویس بازگردانده شد.');
    }

    public function storePart(Request $request, $id)
    {
        if (!Auth::user()) return redirect()->route('login');
        $book = $this->ownedBook($id);
        $errors = $request->validate([
            'page_id' => 'required|integer|min:1',
            'text' => 'required|string',
        ]);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();

        $pageId = (int) $request->input('page_id');
        $existing = BookPart::where('book_id', $book->id)->where('page_id', $pageId)->first();
        $data = ['text' => $request->input('text'), 'page_name' => 'صفحه ' . $pageId, 'publisher_id' => Auth::id()];
        if ($existing) $existing->update($data);
        else BookPart::create($data + ['page_id' => $pageId, 'book_id' => $book->id]);

        $pages = BookPart::where('book_id', $book->id)->count();
        $book->update(['pages' => $pages, 'pages_count' => $pages]);
        return redirect()->back()->with('success', 'صفحه ذخیره شد.');
    }

    private function ownedBook($id)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower((string) ($user->role ?? '')), ['admin', 'superadmin'], true);
        $query = Book::where('id', $id);
        if (!$isAdmin) $query->where('author_id', Auth::id());
        $book = $query->first();
        if (!$book) abort(404);
        return $book;
    }
}