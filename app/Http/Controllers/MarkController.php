<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Exam;
use App\Questions;
use App\Mark;
use Validator;
class MarkController extends Controller
{
    //

    public function store(User $student,Exam $exam, Request $request){
        $validator = Validator::make($request->all(), [ 
            'studentAnswers' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors(),"message"=>"Please Answer the questions","requests"=>$request->all()], 400);           
        }
        $submited = Mark::where([
            ['exam_id', $exam->id],
            ['user_id', $student->id]
        ])->exists();

        if($submited){
            return response()->json(["status"=>400,"message"=>"You have already submited!!!"], 200);
        }
        $req_array = $request->all();
        $examQuestionsIds = json_decode($exam->questions);
        $examQuestions = Questions::whereIn("id", $examQuestionsIds)->get();

        $studenAnswers = json_decode($req_array['studentAnswers'], true);

        $results = 0;
        foreach ($examQuestions as $examQuestion) {
            if(isset($studenAnswers[$examQuestion->id])
                && $studenAnswers[$examQuestion->id] == $examQuestion->answer
             ){
                $results++;
            }
        }

        $totalQuestions = count($examQuestionsIds);
        $percentageMarks = $results * 100 / $totalQuestions;

        $pass = $percentageMarks >= 60 ? 1 : 0;
        $marks = round(($results * 20 / $totalQuestions));
        $user_id = $student->id;
        $exam_id = $exam->id;
        $answers = $studenAnswers;

        $data['pass'] = $pass;
        $data['result'] = $marks;
        $data['user_id'] = $user_id;
        $data['exam_id'] = $exam_id;
        $data['answers'] = json_encode($answers);
        $mark = Mark::create($data);
        
        return response()->json(["status"=>200,"message"=>"Thank you for submiting exam","marks"=> $marks], 200);


        
    }
}
