<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function chatWindow(Request $request) {
        if(!isset($request->input()["lehrer"]) || !is_int($request->input()["lehrer"]) || ($request->input()["lehrer"] < 0 || $request->input()["lehrer"] > 1)) {
            return redirect("home");
        }

        if($request->input()["lehrer"] === Auth::user()["lehrer"]) {
            return json_encode(array(

            ));
        }
    }
}
