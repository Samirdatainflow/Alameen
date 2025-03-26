<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class CheckSuperAdmin
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
        if(is_null(Session::get('superadmin_id'))){
            return redirect('superadmin-login');
        }else{
            //return redirect('admin/admin-dashboard');
        }
        return $next($request);
    }
}
