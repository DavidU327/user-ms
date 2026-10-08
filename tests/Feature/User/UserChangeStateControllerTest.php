<?php

namespace Tests\Feature\User;

use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Tests\Feature\Support\CreatesUserMsSchema;
use Tests\TestCase;

class UserChangeStateControllerTest extends TestCase
{
    use CreatesUserMsSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareUserMsSchema();
        $this->withoutMiddleware([AuthenticateJwt::class, RoleMiddleware::class]);
    }

    public function test_change_state_toggles_user_state(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'User Test',
            'phone' => '3001002000',
            'identification' => '12345',
            'type_identification_id' => 1,
            'rol_id' => 2,
            'email' => 'user.test@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
        $user->save();

        $response = $this->getJson("/api/change_state/{$user->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Cambio de estado correctamente');

        $user->refresh();
        $this->assertEquals(
            State::where('name', State::DISABLED)->value('id'),
            $user->state_id
        );
    }
}
