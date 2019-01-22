<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
class CheckUserSession
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
        $checksession = User::where('session_token',$headers['session_token'])->first();
        if($checksession){
            Auth::login($checksession);
            return $next($request);
        }
        else{
            return Response::json(array('status'=>'error','errorMessage'=>'Session Expired','errorCode'=>401),401);   
        }
    }
}
