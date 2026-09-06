<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\Like;
use Cyron\Authentication\Auth;
use Cyron\Http\Request;
use Cyron\Http\Response;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $userId = Auth::id();
        $type = (string) $request->input('likeable_type', '');
        $likeableId = (int) $request->input('likeable_id', 0);
        $action = (string) $request->input('action', 'like');

        if (!$userId || !$likeableId || $type === '') {
            return Response::badRequest('اطلاعات لایک کامل نیست.');
        }

        $existing = Like::where('user_id', $userId)
            ->where('likeable_type', $type)
            ->where('likeable_id', $likeableId)
            ->first();

        $isLike = $action !== 'dislike';
        if ($existing && (bool) $existing->is_like === $isLike) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['is_like' => $isLike ? 1 : 0]);
        } else {
            Like::create([
                'user_id' => $userId,
                'is_like' => $isLike ? 1 : 0,
                'likeable_type' => $type,
                'likeable_id' => $likeableId,
            ]);
        }

        return Response::success([
            'likes_count' => Like::where('likeable_type', $type)->where('likeable_id', $likeableId)->where('is_like', 1)->count(),
            'dislikes_count' => Like::where('likeable_type', $type)->where('likeable_id', $likeableId)->where('is_like', 0)->count(),
        ]);
    }
}