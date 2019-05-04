@extends("layouts.app")

@section("content")
    <input type="hidden" id="lehrerID" value="{{ $lehrer->{"Internes Kürzel"} }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $lehrer->Vorname . " " . $lehrer->Nachname }}</div>

                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($timeIntervals as $timeInterval)
                                <li class='list-group-item'><b><u><?=$timeInterval[0]->format("d.m H:i")." - ".$timeInterval[1]->format("d.m H:i")?></u></b></li>

                                <?php
                                    $interval = DateInterval::createFromDateString('5 minutes');
                                    $period = new DatePeriod($timeInterval[0], $interval, $timeInterval[1]);
                                ?>

                                @foreach ($period as $dt)

                                    @foreach($lehrerTimeTable as $time)
                                        @if($time->target_date === $dt->format("Y-m-d H:i:s"))
                                            <li class='list-group-item'><?=$dt->format("H:i\n")?><p class="right_button">Belegt von {{ $time->requestedByName }}</p></li>
                                        @else
                                            <li class='list-group-item'><?=$dt->format("H:i\n")?><button class="right_button" onclick="requestDate('{{ $dt->format("Y-m-d H:i:s") }}')">Anfragen</button></li>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
