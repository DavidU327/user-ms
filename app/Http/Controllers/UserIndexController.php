<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserNameResource;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserShowResource;
use Illuminate\Http\Request;

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

    public function show(User $user)
    {
        return new UserShowResource($user);
    }

    public function usersByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        $users = User::whereIn('id', $ids)
            ->select('id', 'name')
            ->get();
        return UserNameResource::collection($users);
    }
}
