<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

//class User extends Authenticatable
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'role',
        'is_banned',
        'banned_at',
        'force_password_change'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
         //   'password' => 'hashed',
            'force_password_change' => 'boolean',
        ];
    }



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->isAdmin()) {
                $model->force_password_change = true;
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    public function shouldChangePassword(): bool
    {
        return $this->force_password_change;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Разрешаем доступ только админам
        return $this->role === 'admin';
    }



}
