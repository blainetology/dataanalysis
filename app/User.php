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
        'first_name', 'last_name', 'email', 'password', 'title', 'client_id', 'admin', 'editor', 'last_login', 'login_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    // relationships

    public function client(){
        return $this->belongsTo('\App\Client','client_id');
    }


    // other methods

    public function displayname(){
      $name = trim($this->first_name);
      if(!empty($this->last_name))
        $name .= ' '.$this->last_name;
      return trim($name);
    }

    public function isEditor(){
        return (!empty($this->editor) || !empty($this->admin));
    }

    public function isAdmin(){
        return !empty($this->admin);
    }

}
