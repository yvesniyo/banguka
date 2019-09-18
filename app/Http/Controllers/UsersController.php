<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Users;
use Validator;
class UsersController extends Controller
{
    public $successStatus = 200;

    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = Users::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus); 
    }
    public function login(){ 
        $userLoginId = Users::where("email","=",request('email'))->limit(1)->get();
        if(isset($userLoginId[0]->id)){
            $lastLogins = \DB::table("oauth_access_tokens")->where("user_id","=",$userLoginId[0]->id)->update(['revoked' => 1]);
        }
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        }else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    public function logout(){
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(["status"=>200,"message"=>"User successfuly logout"],200);
         }else{
            return response()->json(["status"=>400,"message"=>"Bad Request"],400);
         }
    }
    public function details(Request $request) 
    { 

        return $request->user();
        //return "Welcome";
        $user = Auth::user();
        return response()->json( ['success' => $user,"Status "=> $this->successStatus]); 
    } 
}
