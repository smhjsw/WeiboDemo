<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:500',
        ]);

        $post = new Post();
        $post->content = $request->input('content');
        $post->user_id = auth()->user()->id;
        $post->save();

        return response()->json([
            'message' => '发布成功',
        ], 200);
    }

    public function following(Request $request)
    {
        $followingIds = Friend::where('user_id', auth()->user()->id)->pluck('friend_id');

        $posts = Post::whereIn('user_id', $followingIds)->orderBy('created_at', 'desc')->get();

        return response()->json($posts, 200);
    }
}
