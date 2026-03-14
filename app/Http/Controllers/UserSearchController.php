<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserSearchRequest;

class UserSearchController extends Controller
{
    public function search(UserSearchRequest $userSearchRequest)
    {

        $searchTerm = $userSearchRequest->search;

        $user = User::where('rol_id', 2)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('identification', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
            })
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return UserResource::collection($user);
    }
}
