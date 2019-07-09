<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Database\Seeder;
use App\User as User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use \ParagonIE\Halite\KeyFactory;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());


})->describe('Display an inspiring quote');

Artisan::command("regenerate-users", function () {

    User::truncate();

    $alleSchueler = DB::select("SELECT DISTINCT `Nachname`, `Vorname` from schueler  ORDER BY `Nachname`");

    foreach ($alleSchueler as $schueler) {
        $decodedSchueler = json_decode(json_encode($schueler), true);
        $schuelerPass = generate(12);

        $email = encodeText(strtolower($decodedSchueler["Nachname"]) . "." .strtolower($decodedSchueler["Vorname"]).'@gsma.schulbistum.de');

        $this->comment(json_decode(json_encode($schueler), true)["Nachname"]. json_decode(json_encode($schueler), true)["Vorname"]. " | Pass: " . $schuelerPass . " | " . strtolower($decodedSchueler["Nachname"]) . "." .strtolower($decodedSchueler["Vorname"]).'@gsma.schulbistum.de');

        User::create( [
            'email' =>  $email,
            'password' => Hash::make( $schuelerPass ) ,
            'name' => encodeText($decodedSchueler["Nachname"] . $decodedSchueler["Vorname"]),
            'lehrer' => '0',
            'schuelerID' => \Ramsey\Uuid\Uuid::uuid4()
        ]);

        $emailData = $decodedSchueler;
        $emailData["password"] = $schuelerPass;
        $emailData["email"] = $email;

        new_password_email($emailData["email"], $emailData);
    }

    DB::statement("TRUNCATE time_requests;");

    $alleLehrer = \App\Lehrer::get()->unique("Internes Kürzel")->take(999);
    foreach ($alleLehrer as $lehrer) {
        $decodedLehrer = json_decode(json_encode($lehrer), true);
        $lehrerPass = generate(16);

        $email = encodeText(strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.de');

        $this->comment(json_decode(json_encode($lehrer), true)["Nachname"] . json_decode(json_encode($lehrer), true)["Vorname"]. " | Pass: " . $lehrerPass . " | " . strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.de');

        User::create( [
            'email' =>  $email,
            'password' => Hash::make( $lehrerPass ) ,
            'name' => encodeText($decodedLehrer["Nachname"] . $decodedLehrer["Vorname"]),
            'lehrer' => '1',
            'lehrerID' => $decodedLehrer["Internes Kürzel"]
        ]);


        $emailData = $decodedLehrer;
        $emailData["password"] = $lehrerPass;
        $emailData["email"] = strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.de';

        new_password_email($emailData["email"], $emailData);
    }

});

Artisan::command("full", function () {
    Artisan::call("regenerate-users");
    Artisan::call("generate-rsa-keypair");
});

Artisan::command("random-pass", function () {
    $this->comment(generate(16, "a-zA-Z0-9!*%$&"));
});

function encodeText(string $text) : string {
    $text = str_replace("é", "e", $text);
    $text = str_replace("ä", "a", $text);

    return $text;
}

function generate(int $length = 10, string $charlist = '0-9a-z'): string
{
    $charlist = count_chars(preg_replace_callback('#.-.#', function (array $match): string {
        return implode('', range($match[0][0], $match[0][2]));
    }, $charlist), 3);
    $chLen = strlen($charlist);

    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $charlist[random_int(0, $chLen - 1)];
    }
    return $res;
}

function new_password_email($address, $data) {

    /*Mail::send("emails.newpass", array("data" => $data), function ($message) use ($address) {
        $message->from(getenv("SUPPORT_EMAIL"), getenv("APP_NAME"). " Administration");
        $message->to($address)->subject("Zugangsdaten für " . getenv("APP_NAME"));
    });*/
}
