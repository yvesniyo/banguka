<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Exam;
use Illuminate\Support\Facades\Auth; 
use App\NotificationType;
use App\Notification;
use App\Questions;
use Carbon\Carbon;
use App\Mark;
use App\ActivityTiming;

use Illuminate\Support\Facades\Cache;
class ExamController extends Controller
{

    public $notificationExams;
    public function list(){
        $user_id = Auth::user()->id;
        $exams = User::find(1)->exams;
        return response()->json(["status"=>200,"message"=>"ok","exam"=> $exams], 200);
    }

    public function studentExamWorkPlace(User $student,Exam $exam){
        $examQuestions = json_decode($exam->questions);

        $submited = Mark::where([
            ['exam_id', $exam->id],
            ['user_id', $student->id]
        ])->exists();

        if($submited){
            return response()->json(["status"=>400,"message"=>"You have done this exam already!!!"], 200);
        }

        $timing = ActivityTiming::where([
            ['exam_id', $exam->id],
            ['student_id', $student->id]
        ]);

        if(!$timing->exists()){
            $timing = ActivityTiming::create(["exam_id"=> $exam->id,
             "starting_time"=> Carbon::now(),
             "student_id" => $student->id
             ]);
        }else{
            $timing = $timing->first();
        }
        


        $exam = Questions::whereIn("id", $examQuestions)->selectRaw("id, title,choices")->get();
        

        return response()->json([
            "status"=>200,
            "message"=>"ok",
            "starting_time"=> $timing->starting_time,
            "exam"=> $exam
        ], 200);

    }

    public function studentExamReview(User $student,Exam $exam){

        $student_done_this_exam = \App\Mark::where([
            ['exam_id', $exam->id],
            ['user_id', $student->id]
        ])->exists();

        $marks = Exam::where("notifications_id", $exam->notifications_id)
            ->leftJoin("marks","exams.id","=","marks.exam_id")
            ->selectRaw("exams.id, exams.created_at ,marks.answers,marks.pass,marks.result as results,title,questions,marks.id as marks_id, marks.user_id as student_id")
            ->first();
        $studentAnswers = json_decode($marks->answers, true);

        $examQuestions = json_decode($exam->questions);

        $questionsAndAnswers = Questions::whereIn("id", $examQuestions)->selectRaw('id,answer as correctAnswer,choices,title')->get();

        foreach ($questionsAndAnswers as $questionAndAnswer) {
            $question_id = $questionAndAnswer->id;
            if(isset($studentAnswers[$question_id])){
                $questionAndAnswer->student_answer = $studentAnswers[$question_id];
                $questionAndAnswer->isAnswered = true;
                if($studentAnswers[$question_id] == $questionAndAnswer->correctAnswer){
                    $questionAndAnswer->isItCorrect = true;
                }else{
                    $questionAndAnswer->isItCorrect = false;                    
                }
            }else{
                $questionAndAnswer->student_answer = null;
                $questionAndAnswer->isAnswered = false;
                $questionAndAnswer->isItCorrect = null;  
            }
            $questionAndAnswer->correctAnswer = $questionAndAnswer->correctAnswer + 0;
        }
        return response()->json(["message"=>"Exam Review","status"=>200,"submited"=> $student_done_this_exam, "marks"=> $marks->results+0 ,"review"=>$questionsAndAnswers], 200);           
    }

    public function studentExam(User $student){
        if($student->level != "3"){
            return response()->json(["message"=>"Page Not Found","status"=>404], 404);           
        }
        $notification_type_id =  NotificationType::where("name","=","exam")->first()->id;
        $notifications = Notification::where("notification_type_id","=", $notification_type_id)->select("id","people_to_notify")->get();
        
        $this->notificationExams = array();
        foreach ($notifications as $notification) {
            $people_to_notify = json_decode($notification->people_to_notify);
            if(in_array($student->id, $people_to_notify)){
                $this->notificationExams[] = $notification->id;
            }
        }

        $allExamsDoneByUser = Exam::whereIn("notifications_id", $this->notificationExams)->orderBy("created_at","DESC")->get();
        $allMarksForUser = Mark::where("user_id",$student->id)->get();
        $newMarks = array();
        foreach ($allMarksForUser as $marks) {
            $newMarks[$marks->exam_id] = $marks;
        }
        foreach ($allExamsDoneByUser as $exam) {
            if(isset($newMarks[$exam->id])){
                $marks = $newMarks[$exam->id];
                $exam->attend = true;
                $exam->pass = $marks->pass;
                $totalQuestions = count(json_decode($exam->questions));
                $exam->results = $marks->result;
                $results = $exam->results * 100/ $totalQuestions;
                if($results >= 60){
                    $exam->resultsLevel = "Pass";
                }else{
                    $exam->resultsLevel = "Failed";
                }
                $exam->resultsPercentage = $results;
                $exam->answers = $marks->answers;
                $exam->marks_id = $marks->id;
                $exam->student_id = $marks->user_id;
                $exam->marks_exam_id = $marks->exam_id;
            }else{
                $exam->attend = false;
                $exam->pass = "N/A";
                $exam->results = "N/A";
                $exam->resultsLevel = "N/A";
                $exam->resultsPercentage = "N/A";
                $exam->marks_id = null;
                $exam->student_id = null;
                $exam->marks_exam_id = null;
            }
        }
        $newMarks = null;
        return response()->json(["message"=>"exams","status"=>200, "exams"=>$allExamsDoneByUser], 200);           

    }

    public function generate(){
        
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
        $students_level = \App\UserType::where("name","=","student")->get()->first()->id;
        $people_to_notify = json_encode(User::where("level","=",$students_level)->pluck("id"));
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
