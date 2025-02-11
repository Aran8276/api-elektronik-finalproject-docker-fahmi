<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:admins',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $admin = Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Admin registered successfully', 'admin' => $admin], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function logout()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        return response()->json(auth('admin')->user());
    }
}
