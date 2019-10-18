<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class exam extends Model
{
    //
    public $guarded = [];

    public function user(){
        return $this->hasOne('App\User','id','added_by');
    }
}
