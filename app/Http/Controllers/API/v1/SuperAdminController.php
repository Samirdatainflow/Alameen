<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Superadmin_Login;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SuperAdminController extends Controller
{
    public function superadmin_login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Tbl_Superadmin_Login::where('username', $request->get('username'))->first();
        if(count($user) > 0){
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user','token'),201);
        }else {
            return response()->json(["status" => 0, "msg" => "Login record not match"]);
        }
    }
    public function company_management_list() {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'),200);
    }
}
