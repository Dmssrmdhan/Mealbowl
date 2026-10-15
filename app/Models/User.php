<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_CEO = 'ceo';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = ['name', 'username', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    public function isCeo(): bool
    {
        return $this->role === self::ROLE_CEO;
    }
}
