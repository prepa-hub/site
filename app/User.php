<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserActivate;

class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use Notifiable;
    const ACTIVE = 1;
    const INACTIVE = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'matiere_id', 'avatar', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function subject()
    {
        return $this->BelongsTo('App\Subject');
    }


    public function files()
    {
        return $this->hasMany('File', 'user_id');
    }
    public function verified()
    {
        return $this->hasVerifiedEmail();
    }
}
