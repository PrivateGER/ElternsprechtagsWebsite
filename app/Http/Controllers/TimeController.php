<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyCsrfToken;
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
        if(Auth::user()["lehrer"] == 1) {
            return json_encode(array(
                "err" => "Sie sind kein Schüler. Wie sind sie überhaupt hier hingekommen?"
            ));
        }

        if(!isset(request()->post()["date"]) || empty(request()->post()["date"])) {
            return json_encode(array(
               "err" => "Kein Datum angegeben."
            ));
        }

        if(!isset(request()->post()["lehrerID"]) || empty(request()->post()["lehrerID"])) {
            return json_encode(array(
                "err" => "Kein Lehrer angegeben."
            ));
        }

        if(\App\TimeRequest::where("lehrer", request()->post()["lehrerID"])->where("requestedByID", (string)Auth::id())->where("target_date", request()->post()["date"])->count() > 0) {
            return json_encode(array(
                "err" => "Sie haben diese Zeit schon angefragt."
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

        return json_encode(array());
    }

    public function lehrer_time_table() {
        $timeIntervals = [array(new DateTime("2019-06-02 12:00"), new DateTime("2019-06-02 14:05"))];

        $lehrername = Input::get("lehrername");

        $lehrer = DB::select("SELECT * FROM lehrer WHERE CONCAT(`Vorname`, ' ', `Nachname`) = :lehrername", array("lehrername" => $lehrername));

        if(sizeof($lehrer) === 0) {
            return redirect("/home");
        }

        $lehrerTimeTable = \App\TimeRequest::where("lehrer", "=", $lehrer[0]->{"Internes Kürzel"})
            ->where("processed", "=", "1")
            ->where("denied", "=", "0")
            ->get();

        $timetableSize = count($lehrerTimeTable);

        // Falls für diesen Lehrer noch kein Termin angefragt wurde, wird einer zum auslösen des foreach eingefügt.
        if($timetableSize === 0) {
            $newTimeTable = array();
            $newTimeTable[0]["lehrer"] = $lehrer[0]->{"Internes Kürzel"};
            $newTimeTable[0]["target_date"] = "2500-12-12 01:00:00";
            $newTimeTable[0]["denied"] = 1;
            $newTimeTable[0]["processed"] = 1;
            $newTimeTable[0]["requestedByID"] = 99999;
            $newTimeTable[0]["requestedByName"] = "Platzhalter";
            $lehrerTimeTable = $this->convertToObject($newTimeTable);
        }

        return view("lehrer_time_table", ["lehrer" => $lehrer[0], "timeIntervals" => $timeIntervals, "lehrerTimeTable" => $lehrerTimeTable]);
    }

    public function schuelerCancelRequest() {

        if(Auth::user()["lehrer"] == 1) {
            return json_encode(array(
                "err" => "Sie sind kein Schüler. Wie sind sie überhaupt hier hingekommen?"
            ));
        }

        if(!isset(request()->post()["reqID"])) {
            return json_encode(array(
               "err" => "RequestID fehlt."
            ));
        }

        $targetRequest = \App\TimeRequest::where("requestedByID", "=", Auth::id())
            ->where("id", "=", request()->post()["reqID"])
            ->get();

        if(count($targetRequest) === 0) {
            return json_encode(array(
                "err" => "Es wurde keine Anfrage unter dieser ID gefunden oder Sie haben keinen Zugriff auf sie."
            ));
        } else {
             DB::delete("DELETE FROM time_requests WHERE id = :id", array("id" => request()->post()["reqID"]));
             return json_encode(array());
        }
    }

    public function lehrerTerminPlan() {
        $termine = \App\TimeRequest::where("lehrer", Auth::id())
            ->where("processed", 1)
            ->where("denied", 0)
            ->orderBy("target_date", "desc")
            ->get();

        return view("layouts.lehrer_terminplan", array("termine" => $termine));
    }

    function convertToObject($array) {
        $object = new \stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->convertToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }
}