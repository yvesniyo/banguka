<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //
    public $guarded= [];


    public static function id($name){
        return self::where("name", $name)->first()->id;
    }
}
