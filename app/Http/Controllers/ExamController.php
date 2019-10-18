<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Exam;
use Illuminate\Support\Facades\Auth; 
use App\NotificationType;
use App\Notification;
use Carbon\Carbon;
class ExamController extends Controller
{

    public function list(){
        $user_id = Auth::user()->id;
        $exams = User::find(1)->exams;
        return response()->json(["status"=>200,"message"=>"ok","exam"=> $exams], 200);
    }
    public function store(Request $request){

        $teacher_id = Auth::user()->id;

        $validator = Validator::make($request->all(), [ 
            'questions' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors(),"message"=>"Bad Request","requests"=>$request->all()], 400);           
        }

        // notification for users who subscribed to this teacher

        $notification_type_id =  NotificationType::where("name","=","exam")->first()->id;
        $students_of_this_teacher = User::where("teacher","=",$teacher_id)->pluck("id");
        $people_to_notify = json_encode($students_of_this_teacher);
        $people_notified = json_encode([]);

        $notificationData["notification_type_id"] = $notification_type_id;
        $notificationData["people_to_notify"] = $people_to_notify;
        $notificationData["people_notified"] = $people_notified;
        $notificationData["title"] = "New Quick Exam";
        $notificationData["content"] = "Please check out this new exams to prepare your
                                        self for the national exams <a href='#'>Click here to start</a>";
        $notification = Notification::create($notificationData);

        // add exam questions to db
        $data_from_front = $request->all();
        $title_of_exam ="Teacher,".Auth::user()->name ."-". Carbon::now();;
        $questions = $data_from_front["questions"];
        $added_by = Auth::user()->id;
        $notification_id = $notification->id;

        $examData["title"] = $title_of_exam;
        $examData["questions"]  = json_encode($questions);
        $examData["added_by"] = $added_by;
        $examData['notifications_id'] = $notification_id;

        $exam = Exam::create($examData);

        return response()->json(["status"=>200,"message"=>"ok","exam"=> $exam], 200);

    }
}
