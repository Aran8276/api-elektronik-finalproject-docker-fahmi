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

        return response()->json([
            'message' => 'Admin registered successfully',
            'admin' => $admin
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login. Please check your input data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('username', 'password');

        if (!$token = auth('admin')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login. Wrong username or password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in.',
            'user' => auth('admin')->user(),
            'access_token' => $token
        ], 200);
    }

    public function logout()
    {
        auth('admin')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function me()
    {
        return response()->json(auth('admin')->user());
    }
}
    