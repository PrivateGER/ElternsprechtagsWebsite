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
            //return redirect("home");
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
            "otherName" => $request->input()["name"]
        ));
    }
}
