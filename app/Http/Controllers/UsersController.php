<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Users;
use Validator;
use Carbon\Carbon;
class UsersController extends Controller
{
    public $successStatus = 200;
    public static $groupBy ="m-d-Y";

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
    public function login(Request $request){ 
        $userLoginId = Users::where("email","=",request('email'))->limit(1)->get();
        if(isset($userLoginId[0]->id)){
            $lastLogins = \DB::table("oauth_access_tokens")->where("user_id","=",$userLoginId[0]->id)->update(['revoked' => 1]);
        }
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user();
            $success['token'] = "Bearer ".$user->createToken('MyApp')-> accessToken; 
            return response()->json(['status' => "ok", "token"=> $success["token"]], $this-> successStatus); 
        }else{ 
            return response()->json(['error'=>'Unauthorised'. json_encode($request->all())], 200); 
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
    } 


    public function chart($type){
        if($type == "m"){
            self::$groupBy = "MMMM";
        }else if($type == "y"){
            self::$groupBy = "y";
        }
        $users = Users::where("id","!=",0)->orderBy("created_at");
        $users = $users->get()->groupBy(function($val){
            return Carbon::parse($val->created_at)->isoFormat(self::$groupBy);
        });

        $usersCount = $users->map(function($items, $key){
            return collect($items)->count();
        });


        return response()->json(["status"=>200,"message"=>"ok", "users"=> $usersCount],200);
    }
}
