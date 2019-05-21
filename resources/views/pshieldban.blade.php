<html>
<head>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <style>
        @media (min-width:801px)  {
            .header {
                font-size: 7vw;
            }
        }
        @media (min-width:1025px) {
            .header {
                font-size: 4vw;
            }
        }
    </style>

</head>
<body>
    <div style="text-align: center;">
        <i class="fas fa-shield-alt" style="font-size: 30vw; color: crimson"></i>
        <br />
        <h1 class="header">Deine IP wurde von PShield als Gefahr eingestuft und gebannt. Komm später wieder.</h1>
        <h3>Du solltest diesen Bildschirm nicht sehen? Kontaktiere {{ getenv("SUPPORT_EMAIL") }}.</h3>
        <div style="display: flex; justify-content: center;"><h3>Ban ID: {{ hash("sha256", $_SERVER["REMOTE_ADDR"] . getenv("APP_KEY")) }}</h3></div>
    </div>
</body>
</html>

