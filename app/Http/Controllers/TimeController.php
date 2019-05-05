<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TimeController extends Controller
{
    //
    private $timeIntervals;

    public function __construct() {
        $timeIntervals = [array(new DateTime("2019-06-02 12:00"), new DateTime("2019-06-02 14:05"))];

    }

    public function requestDate() {
        if(!isset($_POST["date"]) || empty($_POST["date"])) {
            return json_encode(array(
               "err" => "Kein Datum angegeben."
            ));
        }

        if(!isset($_POST["lehrerID"]) || empty($_POST["lehrerID"])) {
            return json_encode(array(
                "err" => "Kein Lehrer angegeben."
            ));
        }

        DB::table("time_requests")->insert(array(
            "lehrer" => $_POST["lehrerID"],
            "target_date" => $_POST["date"],
            "requestedByID" => Auth::id(),
            "requestedByName" => Auth::user()["name"],
            "denied" => 0,
            "processed" => 0
        ));
    }

    public function lehrer_time_table() {
        $timeIntervals = [array(new DateTime("2019-06-02 12:00"), new DateTime("2019-06-02 14:05"))];

        $lehrername = Input::get("lehrername");

        $lehrer = DB::select("SELECT * FROM lehrer WHERE CONCAT(`Vorname`, ' ', `Nachname`) = :lehrername", array("lehrername" => $lehrername));

        var_dump($lehrer);
        if(sizeof($lehrer) === 0) {
            return redirect("/home");
        }

        $lehrerTimeTable = \App\Timetable::where("lehrer", "=", $lehrer[0]->{"Internes Kürzel"})->get();

        return view("lehrer_time_table", ["lehrer" => $lehrer[0], "timeIntervals" => $timeIntervals, "lehrerTimeTable" => $lehrerTimeTable]);
    }
}
