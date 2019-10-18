<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;
class SendQuiz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:quizes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Message to Students Who Subscribed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //


        function cleanData($data){
            $data = html_entity_decode($data);
            $data = preg_replace('/\s+/', ' ', $data);
            return $data;
        }
        function sendMessage($phones, $message){
            $rslt = array();
            try{
                $client = new Client([ 'headers' => [ 'Content-Type' => 'application/json' ]  ]);
                $response = $client->post('https://sms.besoft.rw/api/v1/client/bulksms',
                    ['body' => json_encode(
                        [
                            'token'=>'0CS7mOLNxzs7HZwm2fttoUQNltCcGcOB',
                            'phone'=> $phones,
                            'message'=> $message,
                            'sender_name'=>'BANGUKA'
                        ]
                    )]
                );
                if($response->getStatusCode() == 200){
                    $data=json_decode($response->getBody()->getContents(), true);
                    $rslt[]= ["student"=> $phones, "statusCode"=>$data["statusCode"],"message"=>$data["response"],"len"=> strlen($message)];
                }else{
                    throw new \Exception('Failed');
                }
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $rslt[]= ["student"=> $phones, "statusCode"=> 530,"message"=>"Connection Error"];
            }

            return $rslt;
        }

        
        $all_students = \App\Student::where([
            ["phone","!=", null],
            ["phone","!=", ""],
            ["level","=", \App\UserType::where("name","student")->first()->id],
            ["expire_date", ">=", Carbon::now()]
        ])->get()->groupBy("package_id");
        $packeges = \App\StudentPackage::get()->groupBy("id");

        $packages_sent_questions = array();
        foreach ($all_students as $package_id => $value) {
            $users = $all_students[$package_id];
            $phones = array();
            $todayQuizNumber = 4;
            $questions = json_decode($packeges[$package_id][0]["questions"] , true) ;
            $questions_title = \App\Questions::whereIn("id", $questions[$todayQuizNumber])->get(['title','choices','answer']);
            $indexQ = 0;
            $sms_content = "";
            foreach ($questions_title as $question) {
                $newChoices ="";
                $choices = json_decode($question["choices"], true);
                $newIndex = 1;
                $answer = "";
                foreach ($choices as $choice) {
                    $newChoices .=  $newIndex.".".$choice['answer'];
                    if($questions_title[$indexQ]["answer"] == $choice['id'] ){
                        $answer = cleanData($choice['answer']);
                    }
                    $newIndex++;
                }
                // $questions_title[$indexQ]["choices"] = $newChoices;
                $questions_title[$indexQ]["choices"] = "";

                $msg = cleanData($questions_title[$indexQ]["title"]) . cleanData($questions_title[$indexQ]["choices"]);
                
                $questions_title[$indexQ] = $msg; 
                $sms_content .= ($indexQ+1).".".$msg."\"". $answer."\"\n";
                $indexQ++;
            }
            foreach ($users as $user) {
                $phones[] =  $user->phone;
            }
            $packages_sent_questions[] = ["package_id"=> $package_id,"phones"=> $phones,"message"=> $sms_content ]; 
            
        }

        //return $packages_sent_questions;
        echo "Quizes sent to Students\n";
        foreach ($packages_sent_questions as $sms) {
            $phones = implode(",", $sms["phones"]);
            $message = $sms["message"];
            echo "\n Phones=".$phones."\n Message=". $message;
            //return sendMessage($phones, substr($message, 0, 140));
        }

        


    }
}
