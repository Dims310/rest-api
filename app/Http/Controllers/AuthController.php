<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{
    //

    public function login(Request $request) {
        $credentials = request(['email', 'password']);

        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => $validator->messages()->first(),
                ]
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $token = auth()->guard('api')->attempt($credentials);

        // authentication failed
        if (!$token) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => 'Incorrect email or password, try again.'
                ]
            ], 401);
        }

        // success authentication
        return response()->json([
            'error' => false,
            'data' => [
                'message' => "Token generated successfully.",
                'token' => $token
            ]
        ], 200);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => $validator->messages()->first(),
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id
        ]);

        // success condition
        if ($user) {
            return response()->json([
                'error' => false,
                'data' => [
                    'message' => "Success create user."
                ]
            ], 200);
        } 

        // false condition
        return response()->json([
            'error' => true,
            'data' => [
                'message' => "Failed create user, try again."
            ]
        ], 302);
    }

    public function me() {
        $user = auth('api')->user();
        return response()->json([
            'error' => false,
            'data' => [
                'user' => $user
            ]
        ], 201);
    }

    public function logout() {
        // auth('api')->invalidate();
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
        if ($removeToken) {
            return response()->json(['message' => 'Successfully logged out']);
        }
    }

    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
