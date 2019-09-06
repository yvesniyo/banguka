<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AdminController extends Controller
{
    public function index(){
        $user = Auth::user();
        return ["page"=>"Admin HomePage","user"=>$user];
    }
    public function getParkingAdmins($id){
        return "Getting Parking Admins";
    }
    public function getOneParkingAdmin($id,Request $request){
        return "Getting One Parking Admin".$id;
    }

    public function postParkingAdmins($request){
        return "Posting Parking Admins";
    }

    public function updateParkingAdmins($id, Request $request){
        return "Updating Parking Admins";
    }

    public function deleteParkingAdmins($id, Request $request){
        return "Deleting Parking Admins";
    }
}
