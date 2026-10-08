<?php

namespace Tests\Feature\User;

use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesUserMsSchema;
use Tests\TestCase;

class UserDeleteControllerTest extends TestCase
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

    public function test_delete_user_marks_state_as_deleted_and_removes_image(): void
    {
        Storage::disk('azure')->put('images/users/avatar.png', 'fake');

        $user = new User();
        $user->forceFill([
            'name' => 'Delete User',
            'phone' => '3001002000',
            'identification' => '12346',
            'type_identification_id' => 1,
            'rol_id' => 2,
            'email' => 'delete.user@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
            'image' => 'https://example.test/container/images/users/avatar.png',
        ]);
        $user->save();

        $response = $this->deleteJson("/api/delete_user/{$user->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Usuario eliminado');

        $user->refresh();
        $this->assertEquals(State::where('name', State::DELETE_USER)->value('id'), $user->state_id);
        $this->assertNotNull($user->deleted_at);
        $this->assertNull($user->image);
        $this->assertFalse(Storage::disk('azure')->exists('images/users/avatar.png'));
    }
}
