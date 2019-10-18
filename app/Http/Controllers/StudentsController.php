<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
class StudentsController extends Controller
{
    
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

    public function list(){
        $level = \App\UserType::where("name","student")->first()->id;
        $teacher_id = Auth::user()->id;
        $students = \App\Users::where([
            ["level", $level],
            ["teacher", $teacher_id]
        ])->get();

        return response()->json(["status"=>200,"message"=>"ok","students"=> $students], 200);
    }
}
