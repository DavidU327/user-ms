<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;

class UserIndexController extends Controller
{
    public function index()
    {
        dd('hola');
        $users = User::whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return UserResource::collection($users);
    }
}
