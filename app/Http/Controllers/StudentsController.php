<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
class StudentsController extends Controller
{
    
    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }
    public function store(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 400);  
        }
        $user_exists = \App\User::where("phone", $data['phone'])
                ->orWhere("email",$data['email'])->exists();
        if($user_exists){
            return response()->json(['status'=>400,"message"=>"User Already In"], 400);
        }
        $student_data["email"] = $data['email'];
        $student_data["name"] = $data['name'];
        $student_data["whatsapp"] = $data['whatsapp'];
        $student_data["teacher"] = Auth::user()->id;
        $student_data["status"] = \App\Status::id("trial");
        $student_data["phone"] = $data['phone'];
        $student_data['intake_date'] = Carbon::now();
        $student_data['expire_date'] = Carbon::now()->addDays(3);
        $student_data['package_id'] = 1; // TODO set package from teacher
        $code ="STU".Str::random(4);
        while(true){
            if(!\App\User::where('code', '=', $code)->exists()){
                break;
            }
        }
        $student_data['code'] = $code;
        $password = Str::random(4); // TODO send password to sms and email
        $student_data['password'] = bcrypt($password);

        $student_data["level"] = \App\UserType::where("name","=","student")->first()->id;

        $student = \App\Student::create($student_data);

        return response()->json(["status"=>200,"message"=>"ok","student"=> $student], 200);

    }

    public function update(Request $request){
        $user = \App\User::findOrFail(auth()->user()->id);
        $datas = $request->all();
        if($request->has("newPassword")){
            $currentPassword = $datas['currentPassword'];
            if(Hash::check($currentPassword, $user->password)){
                $user->password = bcrypt($datas["newPassword"]);
            }else{
                unset($user);
                return response()->json(["status"=>400,"message"=>"Old Password is incorrect!!","student"=> []], 200);
            }
        }
        $user->name = $request->input('name');
        $user->email = $datas["email"];
        $user->username = $datas["username"];
        $user->phone = $datas["phone"];
        $user->whatsapp = $datas["whatsapp"];

        if ($request->has('picture')) {
            $image = $request->file('picture');
            $name = Str::slug($request->input('name')).'_'.time();
            $folder = '/uploads/images/';
            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
            $this->uploadOne($image, $folder, 'public', $name);
            $user->profile_image = $filePath;
        }
        $user->save();
        return response()->json(["status"=>200,"message"=>"ok","student"=> $user], 200);
    }

    public function list(){
        $level = \App\UserType::where("name","student")->first()->id;
        $teacher_id = Auth::user()->id;
        $students = \App\Users::where([
            ["level", $level],
            ["teacher", $teacher_id]
        ])->get();

        return response()->json(["status"=>200,"message"=>"ok","students"=> $students], 200);
    }

    public  function createSimpleAccount(Request $request){

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'password'=> 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors(),'status'=>400], 200);  
        }
        $user_exists = \App\User::where("phone", $data['phone'])
                ->orWhere("email",$data['email'])->exists();
        if($user_exists){
            return response()->json(['status'=>400,"message"=>"User Already In"], 400);
        }
        $student_data["email"] = $data['email'];
        $student_data["name"] = $data['name'];
        $student_data["whatsapp"] = "";
        $student_data["teacher"] = "none";
        $student_data["status"] = \App\Status::id("trial");
        $student_data["phone"] = $data['phone'];
        $student_data['intake_date'] = Carbon::now();
        $student_data['expire_date'] = Carbon::now()->addDays(3);
        $student_data['package_id'] = 1; // TODO set package from teacher
        $code ="STU".Str::random(4);
        while(true){
            if(!\App\User::where('code', '=', $code)->exists()){
                break;
            }
        }
        $student_data['code'] = $code;
        $password = $data['password']; // TODO send password to sms and email
        $student_data['password'] = bcrypt($password);

        $student_data["level"] = \App\UserType::where("name","=","student")->first()->id;

        $student = \App\Student::create($student_data);

        // login student
        $userLoginId = \App\Student::where("email","=",$student->email)->limit(1)->get();
        if(isset($userLoginId[0]->id)){
            $lastLogins = \DB::table("oauth_access_tokens")->where("user_id","=",$userLoginId[0]->id)->update(['revoked' => 1]);
        }

        if(Auth::attempt([
            'email' => $student->email,
            'password' => $data['password']])){ 
            $user = Auth::user();
            $success['token'] = "Bearer ".$user->createToken('MyApp')-> accessToken; 
            return response()->json([
                'status' => "ok",
                "token"=> $success["token"],
                "user" => $user],
                200); 
        }else{ 
            return response()->json(['error'=>'Unauthorised'. json_encode($data)], 200); 
        }

    }
}
