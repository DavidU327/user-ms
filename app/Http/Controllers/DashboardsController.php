<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardsController extends Controller
{
    public function allUsers()
    {
        $users = User::where('rol_id', 2)
            ->whereNull('deleted_at')
            ->count();
        return response()->json([
            'totalUsers' => $users,
        ]);
    }

}
