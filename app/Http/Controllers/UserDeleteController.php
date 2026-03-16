<?php

namespace App\Http\Controllers;

use App\Models\State;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class UserDeleteController extends Controller
{
    public function delete(User $user)
    {
        $user->deleted_at = Carbon::now()->format('Y-m-d');
        if ($user->image !== null) {
            $parsedUrl = parse_url($user->image, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
        }
        $user->image = null;
        $state = State::where('name', State::DELETE_USER)->first();
        $user->state_id = $state->id;
        $user->deleted_at = Carbon::now()->format('Y-m-d');
        $user->save();
        $data = [
            'message' => 'Usuario eliminado',
            'id' => $user->id,
            'code' => 200,
        ];
        return response()->json($data);
    }
}
