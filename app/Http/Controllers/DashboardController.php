<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Products;
use DB;
class DashboardController extends Controller {
    
    public function index() {
        return \View::make("backend/dashboard")->with(array(
            'total_size' => Products::where('is_deleted',0)->count(),
            'stock_alert' => Products::whereRaw('(`current_stock` > 0 and `stock_alert` >= `current_stock` and `is_deleted` = 0)')->count(),
            'out_of_stock' => Products::where([['current_stock', '=', '0'],['is_deleted' , '=', 0]])->count()
        ));
    }
    function Quinchefieldcheck($table, $field, $value) {
        $data = $table::where($field, $value)->get();
        if(count($data) > 0){
            return true;
        }else {
            return false;
        }
    }
    public function logout(){
        Session::flush();
        return redirect('/');
    }
}
