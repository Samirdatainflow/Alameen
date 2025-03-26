<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Company_Master;
use App\Tbl_Role;
use App\Tbl_Retailers;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function index(Request $request) {
        $arrayData = [];
        $v = Validator::make(
            [
                'username' => $request->username,
                'password' => $request->password
            ],
            [
                'username' => 'required',
                'password' => 'required'
            ]
        );
        if ($v->fails())
        {
            //return redirect()->back()->withErrors($v->errors());
            $response = [
                'success' => false,
                'message' => $v->errors(),
            ];
        }else {
            $data = Tbl_User_Master::where('user_name',$request->username)->orWhere('email_id', $request->username)
               ->get();
            if(count($data) > 0){
                if (Hash::check($request->password, $data[0]->user_password)) {
                    if($data[0]->inactive > 0) {
                        array_push($arrayData, array('id' => $data[0]->id, 'user_name' => $data[0]->user_name, 'user_description' => $data[0]->user_description, 'email_id' => $data[0]->email_id));
                        $response = [
                            'success' => true,
                            'data' => $arrayData,
                        ];
                    }else{
                        $response = [
                            'success' => false,
                            'message' => "Account not active.",
                        ];
                    }
                }else{
                    $response = [
                        'success' => false,
                        'message' => "Login record not match.",
                    ];
                }
            }else{
                $response = [
                    'success' => false,
                    'message' => "Login record not match.",
                ];
            }
        }
        return response()->json($response, 200);
    }
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:tbl_user_master',
            'email_id' => 'required|string|email|max:255|unique:tbl_user_master',
            'user_password' => 'required|string|min:5',
            'user_description' => 'required|string|max:255',
        ]);
        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Tbl_User_Master::create([
            'user_name' => $request->get('user_name'),
            'user_description' => $request->get('user_description'),
            'email_id' => $request->get('email_id'),
            'user_password' => Hash::make($request->get('user_password')),
            'inactive' => "0",
        ]);

        $token = JWTAuth::fromUser($user);
        //return response()->json($token, 200);
        return response()->json(compact('user','token'),201);
    }
    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'user_password' => 'required|string',
        ]);
        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Tbl_User_Master::where('user_name', $request->get('user_name'))->first();
        if(!empty($user)) {
            if(count($user->toArray()) > 0){
                if (Hash::check($request->get('user_password'), $user['user_password'])) {
                    if($user['inactive'] > 0) {
                        $token = JWTAuth::fromUser($user);
                        $company_name = "";
                        $logo = "";
                        $user_master = Tbl_Company_Master::where('id', $user['fk_parent_id'])->first();
                        if(!empty($user_master)) {
                            if(count($user_master->toArray()) > 0){
                                $company_name = $user_master['company_name'];
                                $logo = url('/').'/public/company_logo/'.$user_master['logo'];
                            }
                        }
                        $userData = [
                            "id" => $user['id'],
                            "fk_parent_id" => $user['fk_parent_id'],
                            'user_name' => $user['user_name'],
                            "role" => $user['role'],
                            'company_name' => $company_name,
                            'logo' => $logo
                        ];
                        return response()->json(["status" => 1, 'user' => $userData, 'token' => $token], 200);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Account not active."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Login record not match."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Login record not match."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Login record not match"]);
        }
    }
    public function user_login(Request $request) {
        $validator = Validator::make($request->all(), [
            'company_username' => 'required|string',
            'user_name' => 'required|string',
            'user_password' => 'required|string',
        ]);
        if($validator->fails()){
                return response()->json(["status" => 0, "msg" => $validator->errors()]);
                //return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Tbl_User_Master::where([['user_name', '=', $request->get('user_name')]])->first();
        if(!empty($user)) {
            if(count($user->toArray()) > 0){
                $select_company_username = Tbl_User_Master::select('id')->where([['user_name', '=', $request->company_username], ['inactive', '!=', '2']])->get();
                if(count($select_company_username) > 0) {
                    if($select_company_username[0]->id == $user['fk_company_id']) {
                        if($this->check_active_company($user['fk_company_id'])) {
                            if (Hash::check($request->get('user_password'), $user['user_password'])) {
                                if($user['user_type'] == "company") {
                                    return response()->json(["status" => 0, "msg" => "Login record not match."]);
                                }else if($user['inactive'] == 2) {
                                    return response()->json(["status" => 0, "msg" => "Your account is block from Admin."]);
                                }else if($user['inactive'] > 0) {
                                    $update_data = Tbl_User_Master::where('id', $user['id'])->update(['last_login' => date('Y-m-d H:i:s')]);
                                    if($update_data) {
                                        $token = JWTAuth::fromUser($user);
                                        $company_name = "";
                                        $logo = "";
                                        $get_profile_pic = Tbl_User_Master::select('profile_pic')->where('id', $user['id'])->first();
                                        if(!empty($get_profile_pic)) {
                                            if(count($get_profile_pic->toArray()) > 0){
                                                $profile_pic = "";
                                                if(!empty($get_profile_pic['profile_pic'])) {
                                                    $profile_pic = url('/').'/public/user_pic/'.$get_profile_pic['profile_pic'];
                                                }
                                            }
                                        }
                                        $role = "";
                                        if(!empty($user['fk_role_id'])) {
                                            $TypeData = Tbl_Role::where('id', $user['fk_role_id'])->first();
                                            if(!empty($TypeData)) {
                                                $role = $TypeData['role'];
                                            }
                                        }
                                        $userData = [
                                            "id" => $user['id'],
                                            "name" => $user['name'],
                                            "email" => $user["email_id"],
                                            'user_name' => $user['user_name'],
                                            "role" => $role,
                                            "user_type" => $user['user_type'],
                                            "profile_pic" => $profile_pic
                                        ];
                                        return response()->json(["status" => 1, 'user' => $userData, 'token' => $token], 200);
                                    }else {
                                        return response()->json(["status" => 0, "msg" => "Login faild something is wrong."]);
                                    }
                                }else {
                                    return response()->json(["status" => 0, "msg" => "Account not active."]);
                                }
                            }else {
                                return response()->json(["status" => 0, "msg" => "Login record not match."]);
                            }
                        }else {
                            return response()->json(["status" => 0, "msg" => "Account not found."]);
                        }
                    }else {
                        $user = Tbl_Retailers::where('username', $request->get('user_name'))->first();
                        if(!empty($user)) {
                            if(count($user->toArray()) > 0){
                                if($user['Inactive'] == 1) {
                                    if (Hash::check($request->get('user_password'), $user['password'])) {
                                            $token = JWTAuth::fromUser($user);
                                            $retailer_image = "";
                                            if(!empty($user['retailer_image'])) {
                                                $retailer_image = url('/').'/public/user_pic/'.$user['retailer_image'];
                                            }
                                            $retailerData = [
                                            "id" => $user['id'],
                                            "name" => $user['name'],
                                            'username' => $user['username'],
                                            'retailer_image' => $retailer_image,
                                            'user_type' => 'retailer',
                                        ];
                                        return response()->json(["status" => 1, 'user' => $retailerData, 'token' => $token], 200);
                                    }else {
                                        return response()->json(["status" => 0, "msg" => "Login record not match."]);
                                    }
                                }else if($user['Inactive'] == 1) {
                                    return response()->json(["status" => 0, "msg" => "Account is not Active."]);
                                }else {
                                    return response()->json(["status" => 0, "msg" => "Account block from Admin."]);
                                }
                            }else {
                                return response()->json(["status" => 0, "msg" => "Login record not match."]);
                            }
                        }else {
                            return response()->json(["status" => 0, "msg" => "Account not found."]);
                        }
                        return response()->json(["status" => 0, "msg" => "Account not found."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Account not found."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Login record not match."]);
            }
        }else {
            $user = Tbl_Retailers::where('username', $request->get('user_name'))->first();
            if(!empty($user)) {
                if(count($user->toArray()) > 0){
                    if($user['Inactive'] == 1) {
                        if (Hash::check($request->get('user_password'), $user['password'])) {
                                $token = JWTAuth::fromUser($user);
                                $retailer_image = "";
                                if(!empty($user['retailer_image'])) {
                                    $retailer_image = url('/').'/public/user_pic/'.$user['retailer_image'];
                                }
                                $retailerData = [
                                "id" => $user['id'],
                                "name" => $user['name'],
                                'username' => $user['username'],
                                'retailer_image' => $retailer_image,
                                'user_type' => 'retailer',
                            ];
                            return response()->json(["status" => 1, 'user' => $retailerData, 'token' => $token], 200);
                        }else {
                            return response()->json(["status" => 0, "msg" => "Login record not match."]);
                        }
                    }else if($user['Inactive'] == 1) {
                        return response()->json(["status" => 0, "msg" => "Account is not Active."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Account block from Admin."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Login record not match."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Login record not match"]);
            }
            //return response()->json(["status" => 0, "msg" => "Login record not match"]);
        }
    }
    public function power_user_login(Request $request) {
        $validator = Validator::make($request->all(), [
            'company_username' => 'required|string',
            'user_name' => 'required|string',
            'user_password' => 'required|string',
        ]);
        if($validator->fails()){
                return response()->json(["status" => 0, "msg" => $validator->errors()]);
                //return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Tbl_User_Master::where([['user_name', '=', $request->get('user_name')]])->first();
        if(!empty($user)) {
            if(count($user->toArray()) > 0){
                $select_company_username = Tbl_User_Master::select('id')->where([['user_name', '=', $request->company_username], ['inactive', '!=', '2']])->get();
                if(count($select_company_username) > 0) {
                    if($select_company_username[0]->id == $user['fk_company_id']) {
                        if($this->check_active_company($user['fk_company_id'])) {
                            if (Hash::check($request->get('user_password'), $user['user_password'])) {
                                if($user['user_type'] == "company") {
                                    return response()->json(["status" => 0, "msg" => "Login record not match."]);
                                }else if($user['inactive'] == 2) {
                                    return response()->json(["status" => 0, "msg" => "Your account is block from Admin."]);
                                }else if($user['inactive'] > 0) {
                                    $update_data = Tbl_User_Master::where('id', $user['id'])->update(['last_login' => date('Y-m-d H:i:s')]);
                                    if($update_data) {
                                        $token = JWTAuth::fromUser($user);
                                        $company_name = "";
                                        $logo = "";
                                        $get_profile_pic = Tbl_User_Master::select('profile_pic')->where('id', $user['id'])->first();
                                        if(!empty($get_profile_pic)) {
                                            if(count($get_profile_pic->toArray()) > 0){
                                                $profile_pic = "";
                                                if(!empty($get_profile_pic['profile_pic'])) {
                                                    $profile_pic = url('/').'/public/poweruser/images/'.$get_profile_pic['profile_pic'];
                                                }
                                            }
                                        }
                                        $role = "";
                                        if(!empty($user['fk_role_id'])) {
                                            $TypeData = Tbl_Role::where('id', $user['fk_role_id'])->first();
                                            if(!empty($TypeData)) {
                                                $role = $TypeData['role'];
                                            }
                                        }
                                        $userData = [
                                            "id" => $user['id'],
                                            "name" => $user['name'],
                                            "email" => $user["email_id"],
                                            'user_name' => $user['user_name'],
                                            "role" => $role,
                                            "user_type" => $user['user_type'],
                                            "profile_pic" => $profile_pic
                                        ];
                                        return response()->json(["status" => 1, 'user' => $userData, 'token' => $token], 200);
                                    }else {
                                        return response()->json(["status" => 0, "msg" => "Login faild something is wrong."]);
                                    }
                                }else {
                                    return response()->json(["status" => 0, "msg" => "Account not active."]);
                                }
                            }else {
                                return response()->json(["status" => 0, "msg" => "Login record not match."]);
                            }
                        }else {
                            return response()->json(["status" => 0, "msg" => "Account not found."]);
                        }
                    }else {
                        return response()->json(["status" => 0, "msg" => "Account not found."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Account not found."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Login record not match."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Login record not match"]);
        }
    }
    public function check_active_company($company_id){
        $select_data = Tbl_User_Master::select('inactive')->where('fk_company_id', $company_id)->get();
        if(count($select_data) > 0) {
            if($select_data[0]->inactive == "1") {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
}
