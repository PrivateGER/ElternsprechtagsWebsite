<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    //
    public function chatWindow(Request $request) {
        if(!isset($request->input()["lehrer"]) || !is_bool((boolean)$request->input()["lehrer"])) {
            return redirect("home");
        }

        $recID = 0;

        if((boolean)$request->input()["lehrer"]) {
            $recID = DB::select("SELECT * FROM users WHERE name = :name", array("name" => $request->input()["name"]));
        } else {
            $recID = DB::select("SELECT * FROM users WHERE name = :name", array("name" => $request->input()["name"]));
        }
        
        if(count($recID) > 0) {
            $recID = $recID[0]->id;
        } else {
            return redirect("home");
        }
        
        $self = Auth::id();

        $pdo = DB::connection()->getPdo();
        
        $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (`author` = ? AND `recipient` = ?) OR (`author` = ? AND `recipient` = ?) ORDER BY created_at");
        $stmt->bindParam(1, $self);
        $stmt->bindParam(2, $recID);
        $stmt->bindParam(3, $recID);
        $stmt->bindParam(4, $self);
        $stmt->execute();
        
        $messages = $stmt->fetchAll();
        
        return view("chat", array(
            "messages" => $messages,
            "otherName" => $request->input()["name"],
            "otherID" => $recID
        ));
    }

    public function getMessagesAsJSON(Request $request) {
        if(!isset($request->input()["lehrer"]) || !is_bool((boolean)$request->input()["lehrer"])) {
            return json_encode(array(
                "err" => "Fehlender ?lehrer Parameter."
            ));
        }

        $recID = 0;

        if((boolean)$request->input()["lehrer"]) {
            $recID = DB::select("SELECT * FROM users WHERE name = :name", array("name" => $request->input()["name"]));
        } else {
            $recID = DB::select("SELECT * FROM users WHERE name = :name", array("name" => $request->input()["name"]));
        }

        if(count($recID) > 0) {
            $recID = $recID[0]->id;
        } else {
            return json_encode(array(
                "err" => "Es konnte kein Benutzer unter diesem Namen gefunden werden."
            ));
        }

        $self = Auth::id();

        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (`author` = ? AND `recipient` = ?) OR (`author` = ? AND `recipient` = ?) ORDER BY created_at");
        $stmt->bindParam(1, $self);
        $stmt->bindParam(2, $recID);
        $stmt->bindParam(3, $recID);
        $stmt->bindParam(4, $self);
        $stmt->execute();

        $messages = $stmt->fetchAll();

        $formattedMessages = [];

        foreach ($messages as $message) {
            //var_dump($message);
            if($message["author"] === Auth::id()) {
                array_push($formattedMessages, "Sie: " . $message["message"]);
            } else {
                array_push($formattedMessages, $request->input()["name"] . ": " . $message["message"]);
            }
        }
        header('Content-Type: application/json');

        return json_encode($formattedMessages);
    }

    public function sendMessage(Request $request) {
        if(!isset($request->input()["message"]) || empty($request->input()["message"]) || !isset($request->input()["recipient"])) {
            return json_encode(array(
                "err" => "Leere Nachricht/fehlender Empfänger."
            ));
        }

        if(strlen($request->input()["message"]) > 1000) {
            return json_encode(array(
                "err" => "Bitte halten sie ihre Nachrichten unter 1000 Zeichen."
            ));
        }

        if(\App\User::where("id", $request->input()["recipient"])->count() === 0) {
            return json_encode(array(
                "err" => "Empfänger existiert nicht."
            ));
        }

        $self = Auth::id();

        $newMessage = new \App\ChatMessage();

        $newMessage->author = $self;
        $newMessage->recipient = (int)$request->input()["recipient"];
        $newMessage->message = $request->input()["message"];
        $newMessage->save();

        return json_encode(array());
    }
}
