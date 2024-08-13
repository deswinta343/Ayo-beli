<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|regex:/^[a-zA-Z0-9]+$/|unique:users',
        'password' => 'required'
    ],
    [
        'username.regex' => 'Username hanya boleh terdiri dari huruf dan angka tanpa spasi atau simbol.'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ], 400);
    }

    // Simpan pengguna tanpa meng-hash password
    $user = User::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => $request->password,
    ]);

    if ($user) {
        // Generate JWT token
        $token = JWTAuth::fromUser($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'username' => $user->username,
                'email' => $user->email,
                'token' => $token
            ]
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Internal server error'
        ], 500);
    }
}

public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => ['required', 'regex:/^[a-zA-Z0-9]+$/'],
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid username or password format'
        ], 422);
    }

    $user = User::whereRaw('BINARY username = ?', [$request->username])->first();
    
    if (!$user || $request->password !== $user->password) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid username or password'
        ], 400);
    }

    $payload = [
        'user_id' => $user->id
    ];


    $token = JWTAuth::claims($payload)->fromUser($user);

    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user_id' => $user->id,
            'token' => $token,
        ]
    ], 200);
    }
    

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    
}