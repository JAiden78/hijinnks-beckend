<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class checkAppKey
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
        $headers = getallheaders();
        if($headers['app_key'] == 'MdeDKSXifoYhQZYpEvh+Eol2PvuPWBuL7rVjaHRO7j0='){
        return $next($request);}
        else{
         return Response::json(array('status'=>'error','errorMessage'=>'You Are Not Authorize For App','errorCode'=>499),499);   
        }
    }
}
