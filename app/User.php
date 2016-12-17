<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','admin','client_id','admin','editor','last_login','login_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function client(){
        return $this->belongsTo('\App\Client','client_id');
    }


    // other methods

    public function isEditor(){
        return (!empty($this->editor) || !empty($this->admin));
    }

    public function isAdmin(){
        return !empty($this->admin);
    }

}
