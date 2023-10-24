<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
      * @return string|null
     */
    protected function redirectTo(Request $request){
        if(!$request->expectsJson()){
            return route('login');
        }
    }  


    // public function handle($request,Closure $next,...$guard){
    //     if($jwt = $request->cookie('jwt')){
    //         $request->headers->set('Authorization','Bearer ' . $jwt);
    //         $this->authenticate($request,$guard);
    //         return $next($request);
    //     }
    // }
}
