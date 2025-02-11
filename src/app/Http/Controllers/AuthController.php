<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Http\Request;
use Mail;
use Str;
use Validator;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $response = array(
                'success' => false,
                'message' => 'Failed to register. Please check your input data',
                'data' => null,
                'errors' => $validator->errors()
            );

            return response()->json($response, 400);
        }

        $user = User::create($validator->validated());
        $response = array(
            'success' => true,
            'message' => 'Successfully register.',
            'data' => $user
        );

        return response()->json($response, 201);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $response = array(
                'success' => false,
                'message' => 'Failed to login. Please check your input data',
                'data' => null,
                'errors' => $validator->errors()
            );

            return response()->json($response, 400);
        }

        $credentials = $request->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {
            $response = array(
                'success' => false,
                'message' => 'Failed to login. Wrong username or password',
                'data' => null,
            );

            return response()->json($response, 400);
        }

        $response = array(
            'success' => true,
            'message' => 'Successfully login.',
            'data' => auth()->guard('api')->user(),
            'accesstoken' => $token
        );

        return response()->json($response, 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );
        Mail::to($request->email)->send(new ResetPasswordMail($token, $user->name));

        return response()->json([
            'success' => true,
            'message' => 'Reset password email has been sent.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);


        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['success' => false, 'message' => 'Invalid token.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();


        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password has been reset.']);
    }

}
