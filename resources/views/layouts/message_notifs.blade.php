<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$unreadNotifs = DB::select("SELECT DISTINCT(author), created_at FROM chat_messages WHERE recipient = :recipient AND readMessage = 0", array(
    "recipient" => Auth::id()
));

?>
<br />
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Benachrichtigungen</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @if(count($unreadNotifs) > 0)
                            <li class="list-group-item">Sie haben <b>{{ count($unreadNotifs) }}</b> ungelesene Chatnachrichten.</li>
                            @foreach($unreadNotifs as $notif)
                                <?php $notifName = \App\User::select("name")->where("id", $notif->author)->get()[0]->name; ?>
                                <li class="list-group-item">
                                    Von {{ $notifName }} am <?php $dt = new DateTime($notif->created_at); echo $dt->format("d.m H:i") ?>
                                    <a href="/home/chat?name={{ $notifName }}&lehrer={{ Auth::user()["lehrer"] }}" style="float: right">Zum Chat</a>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item">Sie haben keine ungelesenen Chatnachrichten.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
