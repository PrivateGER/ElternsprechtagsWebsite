<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PShieldController extends Controller
{
    public function banIP() {
        $ban = new \App\IPBans;
        $ban->ip = $_SERVER["REMOTE_ADDR"];
        $ban->banHash = hash("sha256", $_SERVER["REMOTE_ADDR"] . getenv("APP_KEY"));
        $ban->save();
        return redirect("blockedByPShield");
    }

    public function banPage() {
        return view("pshieldban");
    }
}
