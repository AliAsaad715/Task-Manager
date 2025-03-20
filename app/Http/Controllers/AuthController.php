<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isTrue;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        return response([
            'user' => $user,
            'token' => $user->createToken('MyappToken')->plainTextToken,
            'message' => 'User created successfully.'],
            201);
    }

    public function login(LoginRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if (!Auth::attempt($request->only('email', 'password')))//request(['email', 'password'])
            return response(['message' => 'Email or password is incorrect.'], 404);

        $user = Auth::user();
        return response([
            'user' => $user,
            'token' => $user->createToken('MyappToken')->plainTextToken,
            'message' => 'User logged in successfully.'
        ], 200);
    }

    public function logout(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if ($request->has('all_devices') && $request['all_devices']) {
            Auth::user()->tokens()->delete();
            return response(['message' => 'The user logged out successfully from all devices.'], 200);
        }
        $request->user()->currentAccessToken()->delete();
        return response(['message' => 'The user logged out successfully from this device only.'], 200);
    }
}
