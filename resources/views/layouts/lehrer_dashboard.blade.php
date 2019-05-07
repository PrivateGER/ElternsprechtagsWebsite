<?php


$openRequests = \App\TimeRequest::where("lehrer", "=", \Illuminate\Support\Facades\Auth::user()["lehrerID"])
    ->where("processed", 0)
    ->get();

?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Anfragenmanagment</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class='list-group-item'>Sie haben <b>{{ sizeof($openRequests) }}</b> offene Terminanfrage(n).</li>
                        @foreach($openRequests as $request)
                            <li class='list-group-item'>{{ (new DateTime($request->target_date))->format("d.m H:i\n") }} von {{ $request->requestedByName }}<br /><button class="btn btn-success" onclick="approveRequest({{ $request->id }})">Annehmen</button> <button class="btn btn-danger" onclick="denyRequest({{ $request->id }})">Ablehnen</button></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>