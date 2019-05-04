<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lehrer extends Model
{
    protected $table = "lehrer";

    //
    public static function getAllLehrerNames() {
        return static::all()->unique("Internes Kürzel");
    }
}
