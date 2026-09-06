<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Cyron\Http\Request;

class CatalogController extends Controller
{
    public function books(Request $request)
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 14)));
        $query = trim((string) $request->query('q', ''));
        $builder = Book::query();

        if ($query !== '') {
            $pattern = '%' . $query . '%';
            $builder->where('title', 'LIKE', $pattern);
        }

        if ($request->query('free') === '1') {
            $builder->where('price', '=', 0);
        }

        $paginator = $builder->orderBy('created_at', 'DESC')->paginate($perPage);
        $items = [];
        foreach ($paginator->items() as $book) {
            $items[] = $this->bookResource($book);
        }
        return response()->success([
            'items' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage,
                'per_page' => $paginator->perPage,
                'total' => $paginator->total,
                'last_page' => $paginator->lastPage,
            ],
        ], 'Books retrieved successfully');
    }

    public function book($slug)
    {
        error_log("[CatalogController::book] Received slug: $slug");
        $book = Book::where('slug', '=', $slug)->first();
        error_log("[CatalogController::book] Query result: " . ($book ? "Found - {$book->slug}" : "Not found"));
        if (!$book) {
            error_log("[CatalogController::book] Returning 404 for slug: $slug");
            return response()->json(['success' => false, 'message' => 'Book not found', 'debug' => ['received_slug' => $slug, 'query_result' => null]], 200);
        }

        return response()->success($this->bookResource($book), 'Book retrieved successfully');
    }

    public function categories()
    {
        $categories = BookCategory::all();
        return response()->success(array_map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug ?? null,
                'icon' => $category->icon ?? null,
            ];
        }, $categories->toArray()), 'Categories retrieved successfully');
    }

    protected function bookResource($book)
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'slug' => $book->slug,
            'subject' => $book->subject ?? null,
            'author' => $book->author ?? null,
            'author_name' => $book->author_name ?? null,
            'cover' => $book->cover ?? null,
            'pages' => $book->pages ?? null,
            'total_pages' => $book->total_pages ?? null,
            'likes' => (int) ($book->likes ?? 0),
            'views' => (int) ($book->views ?? 0),
            'status' => $book->status ?? null,
        ];
    }
}
