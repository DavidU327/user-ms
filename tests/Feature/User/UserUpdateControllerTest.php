<?php

namespace Tests\Feature\User;

use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesUserMsSchema;
use Tests\TestCase;

class UserUpdateControllerTest extends TestCase
{
    use CreatesUserMsSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareUserMsSchema();
        $this->withoutMiddleware([AuthenticateJwt::class, RoleMiddleware::class]);

        config()->set('filesystems.disks.azure.url', 'https://example.test');
        config()->set('filesystems.disks.azure.container', 'container');
        Storage::fake('azure');
    }

    public function test_update_user_changes_name_and_phone(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Old Name',
            'phone' => '3000000000',
            'identification' => '12347',
            'type_identification_id' => 1,
            'rol_id' => 2,
            'email' => 'update.user@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
        $user->save();

        $response = $this->patchJson("/api/user/{$user->id}", [
            'name' => 'New Name',
            'phone' => '3111234567',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Usuario actualizado correctamente');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('3111234567', $user->phone);
    }

    public function test_update_user_with_image_replaces_old_image(): void
    {
        Storage::disk('azure')->put('images/users/old_avatar.png', 'old');

        $user = new User();
        $user->forceFill([
            'name' => 'Photo User',
            'phone' => '3002220000',
            'identification' => '12348',
            'type_identification_id' => 1,
            'rol_id' => 2,
            'email' => 'photo.user@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
            'image' => 'https://example.test/container/images/users/old_avatar.png',
        ]);
        $user->save();

        $response = $this->patch("/api/user/{$user->id}", [
            'name' => 'Photo User',
            'phone' => '3002220000',
            'images' => UploadedFile::fake()->image('new-avatar.png'),
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertNotNull($user->image);
        $this->assertStringContainsString('/images/users/new_avatar.png', $user->image);
        $this->assertFalse(Storage::disk('azure')->exists('images/users/old_avatar.png'));
    }
}
