<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Questions;
class QuestionsController extends Controller
{
    //

    public function showAll(){
        return Questions::all();
    }
    public function retrieveInPacket($offset = 0, $numbers = 1){
        $from = $offset * 10;
        $to = $numbers * 10;
        return Questions::offset($from)->limit($to)->get();
    }
    public function maximumQuestions(){
        return Questions::count();
    }
    public function clean($data){
        $data = addslashes($data);
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function create(Request $request){

        $validator = Validator::make($request->all(), [ 
            'title' => 'required', 
            'choices' => 'required', 
            'answer' => 'required',
            'added_by' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors(),"message"=>"Bad Request","requests"=>$request->all()], 400);            
        }
        $input = $request->all(); 
        $question = Questions::create($input);
        return response()->json(["status"=>200,"message"=>"ok","question"=>$question], 200);
    }
}
