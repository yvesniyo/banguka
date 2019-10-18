<?php

namespace App\Http\Controllers;

use App\StudentPackage;
use Illuminate\Http\Request;

class StudentPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $questions = \App\Questions::all()->pluck("id");
        $min = 0;
        $packages = array();
        function pack($question_per_day){
            $max = 0;
            switch ($question_per_day) {
                case 5:
                    $max = 179;
                    break;
                case 10:
                    $max = 279;
                    break;
                case 15:
                    $max = 379;
                    break;
                case 20:
                    $max = 479;
                    break;
                case 25:
                    $max = 579;
                    break;
                default:
                    $max = 179;
                    break;
            }
            $min = 0;
            $weekend_add_questions_new = 10;
            $random_number_array = range($min, $max);
            shuffle($random_number_array );
            $days = array();
            $day_count = 1;
            $day_name = 1;
            $question_per_day = $question_per_day;
            $weekends = 0;
            $weeks = [];
            for($i =0; $i <= $max+1; $i+= $question_per_day){
                if($day_count == 6 || $day_count == 7){
                    $weeks[] = $weekends;
                    $week_done_questions = array_slice($random_number_array,$weekends , ($question_per_day*5));
                    shuffle($week_done_questions);
                    $week_done_questions = array_slice($week_done_questions, 0, $weekend_add_questions_new);
                    $days[$day_name] = array_merge($week_done_questions, array_slice($random_number_array ,$i, $weekend_add_questions_new)); 
                    $i+=5;
                    if($day_count == 7)
                        $day_count = 0;
                        $weekends+= ($question_per_day*5);
                }else{
                    $days[$day_name] = array_slice($random_number_array ,$i, $question_per_day);
                }
                if(count($days[$day_name]) == 0 ){
                    unset($days[$day_name]);
                }
                $day_count++;
                $day_name++;
            }
            return $days;
        }
        $packages["5QuestionsDay"] = pack(5);
        $packages["10QuestionsDay"] = pack(10);
        $packages["15QuestionsDay"] = pack(15);
        $packages["20QuestionsDay"] = pack(20);
        $packages["25QuestionsDay"] = pack(25);

        // $fiveQuestionsDay = pack(5);
        // $tenQuestionsDay = pack(10);
        // $tenFiveQuestionsDay = pack(15);
        // $twoZeroQuestionsDay = pack(20);
        // $twoFiveQuestionsDay = pack(25);

        // $package = \App\StudentPackage::create([
        //     "name"=> "5QuestionsDay",
        //     "questions" => json_encode($fiveQuestionsDay),
        //     "required_days" => count($fiveQuestionsDay)
        // ]);
        // $package = \App\StudentPackage::create([
        //     "name"=> "10QuestionsDay",
        //     "questions" => json_encode($tenQuestionsDay),
        //     "required_days" => count($tenQuestionsDay)
        // ]);
        // $package = \App\StudentPackage::create([
        //     "name"=> "15QuestionsDay",
        //     "questions" => json_encode($tenFiveQuestionsDay),
        //     "required_days" => count($tenFiveQuestionsDay)
        // ]);
        // $package = \App\StudentPackage::create([
        //     "name"=> "20QuestionsDay",
        //     "questions" => json_encode($twoZeroQuestionsDay),
        //     "required_days" => count($twoZeroQuestionsDay)
        // ]);
        // $package = \App\StudentPackage::create([
        //     "name"=> "25QuestionsDay",
        //     "questions" => json_encode($twoFiveQuestionsDay),
        //     "required_days" => count($twoFiveQuestionsDay)
        // ]);
        
        return $packages;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StudentPackage  $studentPackage
     * @return \Illuminate\Http\Response
     */
    public function show(StudentPackage $studentPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StudentPackage  $studentPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(StudentPackage $studentPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StudentPackage  $studentPackage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentPackage $studentPackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StudentPackage  $studentPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentPackage $studentPackage)
    {
        //
    }
}
