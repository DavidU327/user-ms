<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email', // Asegúrate de tener un campo de email si usas autenticación
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'identification' => $this->identification,
            'phone' => $this->phone,
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function typeIdentification()
    {
        return $this->belongsTo(TypeIdentification::class);
    }
}
