<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        return response()->json($users, 201);
    }

    public function register(Request $request)
    {
        // 验证请求数据
        $validatedData = $request->validate([
            'name' => 'required|string|max:25',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);


        // 创建用户
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;
        $user->api_token = $token;
        $user->save();

        // 返回用户数据
        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function login(Request $request)
    {
        // 验证请求信息
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 尝试进行登录
        if (Auth::attempt($validatedData)) {
            $user = Auth::user();

            // 生成访问令牌
            $token = $user->createToken('api_token')->plainTextToken;
            $user->api_token = $token;
            $user->save();

            // 返回用户信息
            return response()->json([
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => '登录失败'
            ], 401);
        }
    }
}
