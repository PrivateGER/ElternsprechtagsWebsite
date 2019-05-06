<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Lehrersuche</div>

                <div class="card-body">
                    <div class="centered">
                        <form action="/home/lehrer/search" method="get">
                             @csrf
                            <input type="text" id="lehrerInput" name="lehrername" placeholder="Lehrernamen eingeben..." width="match-content">
                            <div id="lehrerSearchResult"></div>
                            <br />
                            <input type="submit" value="Suchen">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
<?php
    use Illuminate\Support\Facades\DB;

    $data = \App\Lehrer::getAllLehrerNames();

    $lehrerNames = [];

    foreach ($data as $lehrer) {
        array_push($lehrerNames, $lehrer->Vorname . " " . $lehrer->Nachname);
    }

    echo "let lehrer = JSON.parse('" . json_encode($lehrerNames) . "');";
?>

setupLehrersuche();

$(function () {
   $("#lehrerInput").autocomplete({
       source: lehrer
   });
});


</script>