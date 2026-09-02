<?php

namespace App\Actions\Book;

use App\Actions\BaseAction;
use App\Models\Book;

class LikeBook extends BaseAction
{
    public function execute(array $data = [])
    {
        $user = $this->user();
        if (!$user) {
            return $this->unauthorized();
        }

        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            return $this->error('شناسه نامعتبر');
        }

        $book = Book::find($id);
        if (!$book) {
            return $this->error('کتاب یافت نشد', 404);
        }

        $book->update([
            'likes' => (int) ($book->likes ?? 0) + 1,
        ]);

        return $this->success([
            'book_id' => $book->id,
            'likes' => (int) ($book->likes ?? 0),
        ], 'کتاب لایک شد');
    }
}
