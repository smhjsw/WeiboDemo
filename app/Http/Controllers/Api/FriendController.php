<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\Post;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function follow($friend_id)
    {
        $friend = new Friend();
        $friend->user_id = auth()->user()->id;
        $friend->friend_id = $friend_id;

        return response()->json(['message' => '关注成功']);
    }

    public function unfollow($friend_id)
    {
        Friend::where('user_id', auth()->user()->id)->where('friend_id', $friend_id)->delete();
        return response()->json(['message' => '取消关注成功']);
    }

    public function posts($friend_id)
    {
        $posts = Post::whereIn('user_id', [$friend_id])->orderBy('created_at', 'desc')->get();

        return response()->json($posts, 200);
    }
}
