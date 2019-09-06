<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parking;

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
        return Parking::create($request);
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
