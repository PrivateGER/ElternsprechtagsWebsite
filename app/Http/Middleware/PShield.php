<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Route;
use Crypt;

class PShield
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $twoHourInterval = Carbon::now()->subHours(2)->toDateTimeString();
        $route = Route::getRoutes()->match($request);
        $currentroute = $route->getName();
      
        $cookieBan = false;
        if(request()->cookie("pshield") !== "") {
          try {
            $decryptedCookie = Crypt::decryptString(request()->cookie("pshield"));
            $cookieBan = filter_var($decryptedCookie, FILTER_VALIDATE_BOOLEAN);
          } catch(DecryptException $e) {
            $cookieBan = false;
          }
        }
      
        if(($currentroute !== "pshieldban" && $cookieBan) || ($currentroute !== "pshieldban" && \App\IPBans::where("ip", $_SERVER["REMOTE_ADDR"])->where("created_at", ">=", $twoHourInterval)->count() > 0)) {
            return redirect()->route("pshieldban");
        }

        return $next($request);
    }
}
