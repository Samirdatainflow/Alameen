<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\SaleOrder;
use App\Deliveries;

class DashboardController extends Controller {
    public function index() {
        return \View::make("backend/dashboard")->with([
            'SaleOrder' => SaleOrder::where([['delete_status', '=', '0'],['client_id','=',Session::get('user_id')]])->count(),
            'ApproveOrder' => SaleOrder::where([['delete_status', '=', '0'], ['is_approved', '=', '1'],['client_id','=',Session::get('user_id')]])->count(),
            'RejectOrder' => SaleOrder::where([['delete_status', '=', '0'], ['is_rejected', '=', '1'],['client_id','=',Session::get('user_id')]])->count(),
            'Deliveries' => Deliveries::where([['status', '=', '0'],['client_id','=',Session::get('user_id')]])->count(),
        ]);
    }
    public function logout(){
        Session::flush();
        return redirect('/');
    }
}
