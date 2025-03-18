<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8']
        ]);

        if($validator->fails())
            return response([
                'data' => null,
                'massage' => $validator->errors()->first()],
                400);

        $user = User::create([
           'name' => $request['name'],
           'email' => $request['email'],
           'password' => bcrypt($request['password']),
        ]);
        $response = [
            'user' => $user,
            'token' => $user->createToken('Myapp')->plainTextToken,
        ];
        return response([
            'data' => $response,
            'message' => 'User created successfully.'],
            201);
    }

    public function login(Request $request)
    {
        $validator  = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails())
            return response([
                'data' => null,
                'message' => $validator->errors()->first()],
            400);

        $user = User::where('email', $request['email'])->first();
        if(!$user || !Hash::check($request['password'], $user->password))
            return response([
                'data' => null,
                'message' => 'Email or password is incorrect.'],
                404);

        $response = [
            'user' => $user,
            'token' => $user->createToken('Myapp')->plainTextToken
        ];
        return response([
            'data' => $response,
            'message' => 'User login successfully.'
        ],200);
    }
}
