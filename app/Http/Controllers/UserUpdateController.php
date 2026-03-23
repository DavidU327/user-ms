<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Support\Str;


class UserUpdateController extends Controller
{

    public function saveStorage($image, $route, $oldFileUrl): string
    {
        if ($oldFileUrl) {
            $parsedUrl = parse_url($oldFileUrl, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
        }

        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $safeName = Str::slug($originalName, '_');
        $imageName = $safeName . '.' . $extension;
        $nameRoute = 'images/' . $route . '/';
        Storage::disk('azure')->putFileAs($nameRoute, $image, $imageName);
        $url = rtrim(config('filesystems.disks.azure.url'), '/') . '/' .
            config('filesystems.disks.azure.container') . '/' .
            $nameRoute . $imageName;
        return $url;
    }

    public function update(UserUpdateRequest $userUpdateRequest, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user->name = $userUpdateRequest->name;
            $user->phone = $userUpdateRequest->phone;
            if ($userUpdateRequest->hasFile('images')) {
                $image = $this->saveStorage($userUpdateRequest->images, 'users', $user->image);
                $user->image = $image;
            }
            $user->save();
            DB::commit();
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
