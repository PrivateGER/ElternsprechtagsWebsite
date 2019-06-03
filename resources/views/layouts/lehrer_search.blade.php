<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Lehrersuche</div>

                <div class="card-body">
                    <div class="centered">
                        <form action="/home/lehrer/search" method="get">
                             @csrf
                            <input type="text" id="lehrerInput" name="lehrername" placeholder="Lehrernamen eingeben..." class="form-control-plaintext">
                            <div id="lehrerSearchResult"></div>
                            <br />
                            <button class="btn btn-primary">Suchen <i class="fa fa-fw fa-search"></i></button>
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
window.addEventListener('DOMContentLoaded', () => {
    $("#lehrerInput").autocomplete({
        source: lehrer
    });
});
</script>