<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Database\Seeder;
use App\User as User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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


    foreach (DB::select("SELECT DISTINCT `Nachname`, `Vorname` from schueler  ORDER BY `Nachname` LIMIT 1") as $schueler) {
        $decodedSchueler = json_decode(json_encode($schueler), true);
        $schuelerPass = generate(12);

        $this->comment(json_decode(json_encode($schueler), true)["Nachname"]. json_decode(json_encode($schueler), true)["Vorname"]. " | Pass: " . $schuelerPass . " | " . strtolower($decodedSchueler["Nachname"]) . "." .strtolower($decodedSchueler["Vorname"]).'@gsma.schulbistum.com');

        User::create( [
            'email' =>  strtolower($decodedSchueler["Nachname"]) . "." .strtolower($decodedSchueler["Vorname"]).'@gsma.schulbistum.com' ,
            'password' => Hash::make( $schuelerPass ) ,
            'name' => $decodedSchueler["Nachname"] . $decodedSchueler["Vorname"],
            'lehrer' => '0',
            'schuelerID' => \Ramsey\Uuid\Uuid::uuid4()
        ]);

        $emailData = $decodedSchueler;
        $emailData["password"] = $schuelerPass;
        $emailData["email"] = strtolower($decodedSchueler["Nachname"]) . "." .strtolower($decodedSchueler["Vorname"]).'@gsma.schulbistum.com';

        new_password_email($emailData["email"], $emailData);
    }

    DB::statement("TRUNCATE timetable;");
    DB::statement("TRUNCATE time_requests;");

    foreach (\App\Lehrer::get()->unique("Internes Kürzel")->take(10) as $lehrer) {
        $decodedLehrer = json_decode(json_encode($lehrer), true);
        $lehrerPass = generate(16);

        $this->comment(json_decode(json_encode($lehrer), true)["Nachname"] . json_decode(json_encode($lehrer), true)["Vorname"]. " | Pass: " . $lehrerPass . " | " . strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.com');

        User::create( [
            'email' =>  strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.com' ,
            'password' => Hash::make( $lehrerPass ) ,
            'name' => $decodedLehrer["Nachname"] . $decodedSchueler["Vorname"],
            'lehrer' => '1',
            'lehrerID' => $decodedLehrer["Internes Kürzel"]
        ]);

        DB::table("timetable")->insert(array(
            "lehrer" => $decodedLehrer["Internes Kürzel"],
            "target_date" => "31-12-12",
            "requestedByID" => "9999",
            "requestedByName" => "Platzhalter, bitte ignorieren"
        ));

        $emailData = $decodedLehrer;
        $emailData["password"] = $lehrerPass;
        $emailData["email"] = strtolower($decodedLehrer["Vorname"]) . "." . strtolower($decodedLehrer["Nachname"]) . '@gsma.schulbistum.com';

        new_password_email($emailData["email"], $emailData);
    }

});



Artisan::command("random-pass", function () {
    $this->comment(generate(16, "a-zA-Z0-9!*%$&"));
});


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