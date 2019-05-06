<br />
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Ihr Terminplan</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                    @if($termine->count() > 0)
                        @foreach($termine as $termin)

                        @endforeach
                    @else
                        <li class="list-group-item"><p>Sie haben noch keine Termine.</p></li>
                    @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
