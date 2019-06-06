<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PShieldController extends Controller
{
    public function banIP() {
        $ban = new \App\IPBans;
        $ban->ip = request()->ip();
        $ban->banHash = hash("sha256", request()->ip() . getenv("APP_KEY"));
        $ban->save();
        return redirect("blockedByPShield");
    }

    public function banPage() {
        return view("pshieldban");
    }
}
