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
use Tymon\JWTAuth\Facades\JWTAuth;
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

    public function logout(Request $request) {
        auth()->invalidate(true);
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$accessToken = JWTAuth::claims(['refresh' => false])->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login. Wrong username or password',
            ], 400);
        }

        $user = auth()->user();

        $accessToken = JWTAuth::claims(['refresh' => false])->fromUser($user);
        $refreshToken = JWTAuth::claims([
            'refresh' => true,
            'user_id' => $user->id,
            'exp' => now()->addMinutes(10080)->timestamp
        ])->fromUser($user);

        config(['jwt.ttl' => env('JWT_TTL', 2880)]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully login.',
            'data' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ], 200);
    }

    public function refresh(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided'
                ], 401);
            }

            $payload = JWTAuth::setToken($token)->getPayload();

            if (!$payload->get('refresh')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only refresh token is allowed'
                ], 403);
            }

            $user = User::find($payload->get('user_id'));
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token'
                ], 401);
            }

            JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($token), false);
            $newAccessToken = JWTAuth::claims(['refresh' => false])->fromUser($user);


            config(['jwt.ttl' => config('jwt.refresh_ttl')]);
            $newRefreshToken = JWTAuth::claims(['refresh' => true, 'user_id' => $user->id])->fromUser($user);

            config(['jwt.ttl' => env('JWT_TTL', 2880)]);

            return response()->json([
                'success' => true,
                'message' => 'Access token successfully refreshed',
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token has expired'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
