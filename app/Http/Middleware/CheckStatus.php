<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class CheckStatus
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
       $status = User::select('status')->where('email',$request->email)->first();

       if( $status['status'] == 'مفعل')  {
           return $next($request);
       }else{
           session()->flash('err','الحساب غير مفعل');
           return redirect()->back();
       }
    }

//    protected function credentials(Request $request)
//    {
//        return ['email' => $request->email, 'password' => $request->password, 'status' => 'مفعل'];
//    }

}
