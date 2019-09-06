<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parking;
use Validator;
class ParkingsController extends Controller
{
    public function index()
    {
        //
        return Parking::all();
    }
    public function postParkings(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [ 
            'names' => 'required', 
            'address' => 'required', 
            'user_id' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $values = array_except($request->all(), ['_token']);
        return Parking::create($values);
    }

    public function getOneParking($id)
    {
        //
        $Parcking = Parking::find($id);
        return $Parcking;
    }

    public function updateParkings($id, Request $request)
    {
        //
        $Parcking = Parking::findOrFail($id);
        $Parcking->update($request->all());

        return $Parcking;
    }

    public function deleteParking($id, Request $request)
    {
        $Parcking = Parking::findOrFail($id);
        $Parcking->delete();
        return 204;
    }
}
