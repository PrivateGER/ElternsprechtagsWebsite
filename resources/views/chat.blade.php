@extends("layouts.app")

@section('content')
<?php

$selfID = Auth::id();

?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <div id="messages">
                            @foreach($messages as $message)
                                @if($message["author"] === $selfID)
                                    <li class="list-group-item">Sie: {{ $message["message"] }}</li>
                                @else
                                    <li class="list-group-item">{{ $otherName }}: {{ $message["message"] }}</li>
                                @endif
                            @endforeach
                        </div>
                        <li class="list-group-item">
                            <div class="centered">
                                <input type="text" placeholder="Nachricht eingeben..." name="message" id="chatMessage" class="form-control-plaintext">
                                <br />
                                <button type="submit" class="btn btn-success mx-auto centered" onclick="sendChatMessage()">Senden</button>
                            </div>
                        </li>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
     let recipient = "{{ $otherID }}";
     let name = "{{ $otherName }}";
     let lehrer = "{{ request()->input()["lehrer"] }}";
 
     window.addEventListener('DOMContentLoaded', () => {
      	setInterval(() => {
     		updateChatMessages();
      	}, 2500);
     });
</script>
@endsection