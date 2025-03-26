<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
//use App\Tbl_Company_Master;
use Hash;
class LoginController extends Controller {
    /* ====================
        Login Section
    ==================== */
    public function index() {
        if(!empty(Session::get('user_type'))) {
            return redirect(Session::get('user_type').'/dashboard');
        }else {
            return \View::make("login")->with(array());
        }
    }
    public function login_match(Request $request) {
        $returnData = [];
        if(Session::get('active_token')) {
            $returnData = ["status" => 0, "msg" => "You already logged in with another profile."];
        }else {
            $select_user = Users::where([['email', '=', $request->email], ['password', '=', md5($request->password)]])->get();
            if(count($select_user) > 0){
                if($select_user[0]->status=="Active")
                {
                    Session::put('user_id', $select_user[0]->user_id);
                    Session::put('user_role', $select_user[0]->fk_user_role);
                    Session::put('user_type', $select_user[0]->user_type);
                    $returnData = ["status" => 1];
                }
                else
                {
                    $returnData = ["status" => 0, "msg" => "Your account is inactive"];
                }
                
            }else {
                $returnData = ["status" => 0, "msg" => "Login faild. Login record did not match."];
            }
        }
        return response()->json($returnData);
    }
    function Quinchefieldcheck($table, $field, $value) {
        $data = $table::where($field, $value)->get();
        if(count($data) > 0){
            return true;
        }else {
            return false;
        }
    }
}
