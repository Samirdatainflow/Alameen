<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Clients;
//use App\Tbl_Company_Master;
use Hash;
class OtherController extends Controller {
    /* ====================
        Login Section
    ==================== */
    // public function index() {
    //     if(!empty(Session::get('user_type'))) {
    //         return redirect(Session::get('user_type').'/dashboard');
    //     }else {
    //         return \View::make("login")->with(array());
    //     }
    // }
    public function create_order(Request $request) {
    	Session::put('user_id', $request->client_id);
    	Session::put('order_status', 'create_order');
    	return redirect('/new-order');
        // $returnData = [];
        // if(Session::get('active_token')) {
        //     $returnData = ["status" => 0, "msg" => "You already logged in with another profile."];
        // }else {
        //     $select_user = Clients::where([['customer_email_id', '=', $request->email], ['password', '=', base64_encode($request->password)]])->get();
        //     if(count($select_user) > 0){
        //         if($select_user[0]->delete_status == "0")
        //         {
        //             Session::put('user_id', $select_user[0]->client_id);
        //             $returnData = ["status" => 1];
        //         }
        //         else
        //         {
        //             $returnData = ["status" => 0, "msg" => "Your account is deleted"];
        //         }
                
        //     }else {
        //         $returnData = ["status" => 0, "msg" => "Login faild. Login record did not match."];
        //     }
        // }
        // return response()->json($returnData);
    }
    // function Quinchefieldcheck($table, $field, $value) {
    //     $data = $table::where($field, $value)->get();
    //     if(count($data) > 0){
    //         return true;
    //     }else {
    //         return false;
    //     }
    // }
}
