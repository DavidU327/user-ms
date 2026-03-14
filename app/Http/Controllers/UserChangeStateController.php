<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;

class UserChangeStateController extends Controller
{
    public function getState($stateId)
    {
        $state = State::find($stateId);
        return $state->name == State::DISABLED ?
            State::where('name', State::ENABLED)->value('id') :
            State::where('name', State::DISABLED)->value('id');
    }

    public function changeState (User $user) {
        $state = $this->getState($user->state_id);
        $user->state_id = $state;
        $user->save();
        $infoState = State::find($state);
        $data = [
            'message' => 'Cambio de estado correctamente',
            'data' => [
                'id' => $user->id,
                'state' => [
                    'id' => $infoState->id,
                    'name' => $infoState->name,
                    'color' => $infoState->color,
                ],
            ],
            'code' => 200,
        ];
        return response()->json($data);
    }
}
