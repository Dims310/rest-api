<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers() {
        $users = UserResource::collection(User::get());

        return response()->json([
            'error' => false,
            'data' => $users
        ], 200);
    }
}
