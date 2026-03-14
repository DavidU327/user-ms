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

        $collectors = User::with('user')
            ->whereHas('user', function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            })
            ->whereNull('deleted_at')
            ->paginate(10); // Paginar correctamente

        return UserResource::collection($collectors);
    }
}
