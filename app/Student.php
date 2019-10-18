<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    public $table = "users";

    public $guarded = [];

    public function package(){
        return $this->hasOne("\App\StudentPackage","id","package_id");
    }

}
