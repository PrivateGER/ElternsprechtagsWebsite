<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyCsrfToken;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class TimeController extends Controller
{
    //
    private $timeIntervals;

    public function __construct() {
        $timeIntervals = [array(new DateTime("2019-06-02 12:00"), new DateTime("2019-06-02 15:05"))];

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
            "lehrer" => request()->post()["lehrerID"],
            "target_date" => request()->post()["date"],
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

        if(count($lehrer) === 0) {
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
    
    public function acceptRequestLehrer() {
        if(Auth::user()["lehrer"] == 0) {
            return json_encode(array(
                "err" => "Sie sind kein Lehrer. Wie sind sie überhaupt hier hingekommen?"
            ));
        }

        if(!isset(request()->post()["reqID"])) {
            return json_encode(array(
               "err" => "RequestID fehlt."
            ));
        }
        
        $targetRequest = \App\TimeRequest::where("lehrer", "=", Auth::user()["lehrerID"])
            ->where("id", "=", request()->post()["reqID"])
            ->get();

        if(count($targetRequest) === 0) {
            return json_encode(array(
                "err" => "Es wurde keine Anfrage unter dieser ID gefunden oder Sie haben keinen Zugriff auf sie."
            ));
        } else {
            DB::update("UPDATE time_requests SET processed = 1 WHERE id = :id", array("id" => request()->post()["reqID"]));

            DB::update("UPDATE time_requests SET processed = 1, denied = 1 where `lehrer` = :lehrer and `target_date` = :date and `id` <> :origID", array("lehrer" => Auth::user()["lehrerID"], "date" => $targetRequest[0]->target_date, "origID" => request()->post()["reqID"]));

            $deletedRequests = DB::select("SELECT * FROM time_requests WHERE `lehrer` = :lehrer and `target_date` = :date and `id` <> :origID", array("lehrer" => Auth::user()["lehrerID"], "date" => $targetRequest[0]->target_date, "origID" => request()->post()["reqID"]));
            foreach ($deletedRequests as $deniedRequest) {

                $emailAddress = \App\User::where("name", $deniedRequest->requestedByName)
                        ->get()[0]->email;

                $data = array(
                    "date" => $deniedRequest->target_date,
                    "lehrer" => Auth::user()["name"]
                );

                Mail::send("emails.termindenied", array("data" => $data), function ($message) use ($emailAddress) {
                    $message->from(getenv("SUPPORT_EMAIL"), getenv("APP_NAME"). " Administration");
                    $message->to($emailAddress)->subject("Termininformation | " . getenv("APP_NAME"));
                });
            }

            $acceptedEmailAddress = \App\User::where("name", $targetRequest[0]->requestedByName)
                ->get()[0]->email;

            $data = array(
                "date" => $targetRequest[0]->target_date,
                "lehrer" => Auth::user()["name"]
            );

            Mail::send("emails.terminaccepted", array("data" => $data), function ($message) use ($acceptedEmailAddress) {
                $message->from(getenv("SUPPORT_EMAIL"), getenv("APP_NAME"). " Administration");
                $message->to($acceptedEmailAddress)->subject("Termininformation | " . getenv("APP_NAME"));
            });

            return json_encode(array());
        }
    }

    public function denyRequestLehrer() {

        if(Auth::user()["lehrer"] == 0) {
            return json_encode(array(
                "err" => "Sie sind kein Lehrer. Wie sind sie überhaupt hier hingekommen?"
            ));
        }

        if(!isset(request()->post()["reqID"])) {
            return json_encode(array(
                "err" => "RequestID fehlt."
            ));
        }

        $targetRequest = \App\TimeRequest::where("lehrer", Auth::user()["lehrerID"])
            ->where("id", request()->post()["reqID"])
            ->get();

        if(count($targetRequest) === 0) {
            return json_encode(array(
                "err" => "Es wurde keine Anfrage unter dieser ID gefunden oder Sie haben keinen Zugriff auf sie."
            ));
        } else {
            DB::update("UPDATE time_requests SET processed = 1, denied = 1 WHERE id = :id", array("id" => $targetRequest[0]->id));
            return json_encode(array());
        }
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
