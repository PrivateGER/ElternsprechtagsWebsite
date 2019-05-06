<?php


$openRequests = \App\TimeRequest::where("requestedByID", \Illuminate\Support\Facades\Auth::id())
    ->where("processed", "=", "0")
    ->orderBy("added", "desc")
    ->get();

$approvedRequests = \App\TimeRequest::where("requestedByID", \Illuminate\Support\Facades\Auth::id())
        ->where("processed", "=", "1")
        ->where("denied", "=", "0")
        ->orderBy("target_date", "desc")
        ->get();

?>
<br />
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Ihre Anfragen</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @if($openRequests->count() > 0)
                            <li class='list-group-item'><b>{{ count($openRequests) }}</b> offene Terminanfrage(n).</li>
                            @foreach($openRequests as $request)
                                <?php $lehrer = \App\Lehrer::where("Internes Kürzel", "=", $request->lehrer)->get(); ?>
                                <li class='list-group-item'>
                                    {{ (new DateTime($request->target_date))->format("d.m H:i\n") }} bei {{ $lehrer[0]["Anrede"] . " " . $lehrer[0]["Nachname"] }}
                                    <button class="right_button" onclick="cancelReqSchueler({{ $request->id }})">Zurückziehen</button>
                                </li>
                            @endforeach
                        @else
                            <li class='list-group-item'>Sie haben keine offenen Anfragen.</li>
                        @endif

                        @if($approvedRequests->count() > 0)
                            <li class="list-group-item">Sie haben <b>{{ count($approvedRequests) }}</b> Termin(e).<br />Um abzusagen, melden Sie sich bitte direkt bei dem zuständigen Lehrer.</li>
                            @foreach($approvedRequests as $request)
                                <?php $lehrer = \App\Lehrer::where("Internes Kürzel", "=", $request->lehrer)->get(); ?>
                                <li class="list-group-item termin">{{ (new DateTime($request->target_date))->format("d.m H:i\n") }} bei {{ $lehrer[0]["Anrede"] . " " . $lehrer[0]["Nachname"]}}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>