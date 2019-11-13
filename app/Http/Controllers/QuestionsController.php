<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Questions;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Illuminate\Support\Facades\Cache;

class QuestionsController extends Controller
{
    //
    
    public $questions="";
    public function showAll(){

        
        // return Cache::remember("questionsNew", (1*60*10), function(){
            return Questions::all().",";
        // }); 

        return $responseData;
        $reponse = new StreamedResponse(function(){
            Questions::chunk(20, function($questions){
                $handle = fopen("php://output","w");
                fputs($handle, $questions.",");
                fclose($handle);
            });            
        });
        return $reponse;
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
    public function store(Request $request)
    {
       $name = "";
       if($request->get('image'))
       {
          $image_form_name = "image";
          $image = $request->get($image_form_name);
          $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
          \Image::make($request->get($image_form_name))->save(public_path('images/').$name);
        }

       

       return response()->json(['success' => 'You have successfully uploaded an image',"image"=>$name], 200);
     }

    public static function uploadPic($data){
        $image = $data;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(10).'.'.'png';
        \Storage::disk('public')->put('images/'.$imageName, base64_decode($image));
        return $imageName;
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
        $choices = $request->all()["choices"];
        $choices = json_decode($choices);
        $pics = array();
        $num=0;
        foreach ($choices as $choice ) {
           if( (!empty($choice->img) || $choice->img !=" ") && $choice->img != null){
               $imageData = $choice->img;
               $name = self::uploadPic($imageData); 
               $pics[] = $name;
               $choices[$num]->answer = "<img src='http://localhost:8000/storage/images/".$name."' height='50px' width='50px'/>";
               $choices[$num]->img = "";
           }else{
            $pics[] = null;
           }
           $num++;
        }
        $choices = json_encode($choices);
        $input = $request->all(); 
        $input['choices'] = $choices;
        $question = Questions::create($input);
        return response()->json(["status"=>200,"message"=>"ok","choices"=> $choices], 200);
    }

    public function update($id, Request $request){
        $validator = Validator::make($request->all(), [ 
            'title' => 'required', 
            'choices' => 'required', 
            'answer' => 'required',
            'added_by' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors(),"message"=>"Bad Request","requests"=>$request->all()], 400);            
        }

        $choices = $request->all()["choices"];
        $choices = json_decode($choices);
        $pics = array();
        $num=0;
        foreach ($choices as $choice ) {
           if( (!empty($choice->img) || $choice->img !=" ") && $choice->img != null){
               $imageData = $choice->img;
               $name = self::uploadPic($imageData); 
               $pics[] = $name;
               $choices[$num]->answer = "<img src='http://localhost:8000/storage/images/".$name."' height='50px' width='50px'/>";
               $choices[$num]->img = "";
           }else{
            $pics[] = null;
           }
           $num++;
        }
        $choices = json_encode($choices);

        $data = $request->all();
        $question = Questions::find($id);
        $question->title = $data['title'];
        $question->choices = $choices;
        $question->answer = $data['answer'];
        $question->save();
        return response()->json(["status"=>200,"message"=>"ok","question"=>$question], 200);
    }
}
