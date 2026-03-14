<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;

class UserIndexController extends Controller
{
    public function index()
    {
        $users = User::where('rol_id', 2)
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return UserResource::collection($users);
    }
}
