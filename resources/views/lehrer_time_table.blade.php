@extends("layouts.app")

@section("content")
    <input type="hidden" id="lehrerID" value="{{ $lehrer->{"Internes Kürzel"} }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $lehrer->Vorname . " " . $lehrer->Nachname }}</div>

                    <div class="card-body">
                        <a href="/home"><button class="btn btn-outline-info btn-block rounded-el">Zurück</button></a>
                        <ul class="list-group list-group-flush">
                            @foreach($timeIntervals as $timeInterval)
                                <li class='list-group-item'><b><u><?=$timeInterval[0]->format("d.m H:i")." - ".$timeInterval[1]->format("d.m H:i")?></u></b></li>

                                <?php
                                    $interval = DateInterval::createFromDateString('5 minutes');
                                    $period = new DatePeriod($timeInterval[0], $interval, $timeInterval[1]);
                                ?>

                                @foreach ($period as $dt)
                                    <?php
                                        $available = true;
                                        foreach($lehrerTimeTable as $time) {
                                            if($time->target_date === $dt->format("Y-m-d H:i:s")) {
                                                $available = false;
                                            }
                                        }
                                    ?>
                                        @if(!$available)
                                            <li class='list-group-item'><?=$dt->format("H:i\n")?><p class="right_button">Belegt von {{ $time->requestedByName }}</p></li>
                                        @else
                                            <?php
                                                $requestCount = \App\TimeRequest::where("lehrer", $lehrer->{"Internes Kürzel"})
                                                    ->where("processed", 0)
                                                    ->where("target_date", $dt)
                                                    ->count();
                                            ?>
                                            @if($requestCount === 0)
                                                <li class='list-group-item'><?=$dt->format("H:i\n")?><button class="btn btn-primary right_button rounded-el" onclick="requestDate('{{ $dt->format("Y-m-d H:i:s") }}')">Anfragen</button></li>
                                            @else
                                                <li class='list-group-item'><?=$dt->format("H:i\n")?><button class="btn btn-primary right_button rounded-el button_space" onclick="requestDate('{{ $dt->format("Y-m-d H:i:s") }}')">Anfragen</button><p class="right_button ">Es sind schon {{ $requestCount }} Anfrage(n) für diese Zeit vorhanden!</p></li>
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
