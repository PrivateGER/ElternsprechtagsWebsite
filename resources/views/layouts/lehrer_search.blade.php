<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Lehrersuche</div>

                <div class="card-body">
                    <div class="centered">
                        <input type="text" id="lehrerInput" placeholder="Lehrernamen eingeben..." width="match-content">
                        <div id="lehrerSearchResult"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
<?php
    use Illuminate\Support\Facades\DB;

    $data = DB::select("SELECT * from users WHERE lehrer = 1");
    $data = json_decode(json_encode($data), true);

    $lehrerNames = [];

    foreach ($data as $lehrer) {
        array_push($lehrerNames, $lehrer["name"]);
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