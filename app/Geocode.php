<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Geocode extends Model
{
    //
    protected $fillable = ['address','latitude','longitude'];

}
