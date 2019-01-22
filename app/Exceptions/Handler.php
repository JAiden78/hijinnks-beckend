<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $segment = \Illuminate\Support\Facades\Request::segment(1); 

        if ($segment == 'api') {
            $message = $exception->getMessage();
            return sendError($message, 420);
//            return Response::json(array('status' => 'error', 'errorMessage' => $message, 'exception' => $message));

        } else {
            return parent::render($request, $exception);
        }
        return parent::render($request, $exception);
//        if($segment == 'api'){
//            $message = $exception->getMessage(); 
//            $code=  $exception->getCode();
//
//        if($code == 0 && !$message){
//            $message = 'Sorry Invalid Url Or Bad Method'; 
//        }
//        return sendError($message, 410);
//        //return Response::json(array('status' => 'error', 'errorMessage' => $message,'exception'=>$message));
//        }else{
//            return parent::render($request, $exception);
//        }

    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect('/adminlogin');
    }
}
