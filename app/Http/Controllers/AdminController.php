<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Users;
use Validator;
class AdminController extends Controller
{
    public function index(){
        $user = Auth::user();
        return ["page"=>"Admin HomePage","LoggedIn user"=>$user];
    }
    public function getParkingAdmins(){
        return Users::all()->where("level","parkingAdmin");
    }
    public function getOneParkingAdmin($id,Request $request){
        return "Getting One Parking Admin".$id;
    }

    public function postParkingAdmins(Request $request){
        $validator = Validator::make($request->all(), [ 
            'username' => 'required', 
            'email' => 'required|email',
            'password' => 'required', 
            'c_password' => 'required|same:password',
            'level' => 'required',
            'name' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $values = array_except($request->all(), ['_token']);
        $values['password'] = bcrypt($values['password']); 
        $checkIfExists = Users::where("email","=",$values['email'])->exists();
        if(!$checkIfExists){
            Users::create($values);
            return $values;
        }else{
            return ["status"=>400,"message"=>"Please user another email"];
        }
        
    }

    public function updateParkingAdmins($id, Request $request){
        return "Updating Parking Admins";
    }

    public function deleteParkingAdmins($id, Request $request){
        return "Deleting Parking Admins";
    }
}
