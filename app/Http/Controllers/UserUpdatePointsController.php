<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdatePointsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Http;

class UserUpdatePointsController extends Controller
{

    public function updatePoints(UserUpdatePointsRequest $userUpdatePointsRequest, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user->points += $userUpdatePointsRequest->points;
            $user->save();
            DB::commit();
            Http::baseUrl(config('services.level_service.url'))
                ->patch('/update-user-level', [
                    'user_id' => $user->id,
                    'points' => $user->points,
            ]);
            $userResource = UserResource::make($user);
            $data = [
                'message' => 'Usuario actualizado correctamente',
                'user' => $userResource,
                'code' => 200,
            ];
            return response()->json($data);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException  $exception) {
            DB::rollBack();
            $data = [
                'message' => $exception->getMessage(),
                'code' => 400,
            ];

            return response()->json($data);
        }
    }
}
