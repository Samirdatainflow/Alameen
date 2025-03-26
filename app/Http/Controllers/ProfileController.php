<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Countries;
use DB;
use DataTables;
use App\Location;
use App\ZoneMaster;
use App\Row;
use App\Rack;

class ProfileController extends Controller {

    public function index() {
        return \View::make("backend/profile")->with([
            'UserData' => Users::where([['user_id', '=', Session::get('user_id')]])->get()->toArray(),
            ]);
    }
    // Save
    public function profile_save(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = Users::where([['email', '=', $request->email], ['user_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter email already exist. Please try with another email."];
            }else {
                $saveData = Users::where('user_id', $request->hidden_id)->update(array('email' => $request->email, 'password' => md5($request->password)));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Profile update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Profile update failed! Something is wrong."];
                }
            }
        }else {
            $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
}