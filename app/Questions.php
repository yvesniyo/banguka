<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    //
    protected $fillable  = [
        'title','choices','answer','added_by','rates','questionImage','image_downloaded'
    ];


    
}
