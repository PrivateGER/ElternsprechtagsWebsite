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


    foreach (DB::select("SELECT DISTINCT `COL 1`, `COL 2` from schueler  ORDER BY `COL 1` LIMIT 20") as $schueler) {
        $decodedSchueler = json_decode(json_encode($schueler), true);
        $schuelerPass = generate(12);

        $this->comment(json_decode(json_encode($schueler), true)["COL 2"]. json_decode(json_encode($schueler), true)["COL 1"]. " | Pass: " . $schuelerPass . " | " . strtolower($decodedSchueler["COL 2"]) . "." .strtolower($decodedSchueler["COL 1"]).'@gsma.schulbistum.com');

        User::create( [
            'email' =>  strtolower($decodedSchueler["COL 2"]) . "." .strtolower($decodedSchueler["COL 1"]).'@gsma.schulbistum.com' ,
            'password' => Hash::make( $schuelerPass ) ,
            'name' => $decodedSchueler["COL 2"] . $decodedSchueler["COL 1"],
        ]);

        $emailData = $decodedSchueler;
        $emailData["password"] = $schuelerPass;
        $emailData["email"] = strtolower($decodedSchueler["COL 2"]) . "." .strtolower($decodedSchueler["COL 1"]).'@gsma.schulbistum.com';

        new_password_email(strtolower($decodedSchueler["COL 2"]) . "." .strtolower($decodedSchueler["COL 1"]).'@gsma.schulbistum.com', $emailData);
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

    Mail::send("emails.newpass", array("data" => $data), function ($message) use ($address) {
        $message->from(getenv("SUPPORT_EMAIL"), getenv("APP_NAME"). " Administration");
        $message->to($address)->subject("Zugangsdaten für " . getenv("APP_NAME"));
    });
}