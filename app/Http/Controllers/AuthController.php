<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Str;
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
            'phone' => 'required|numeric|unique:users',
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

        $uuid = Str::uuid();
        $user = User::create([
            'uuid' => $request->$uuid,
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
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

    public function myprofile() {
        $user = auth('api')->user();
        return response()->json([
            'error' => false,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nim' => $user->nim,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role->name
                ]
            ]
        ], 200);
    }

    public function logout() {
        // auth('api')->invalidate();
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
        if ($removeToken) {
            return response()->json([
                'error' => false,
                'data' => [
                    'message' => "Success logout, invalidate the token."
                ]
            ]);
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
