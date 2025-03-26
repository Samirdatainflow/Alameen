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

class RetailerLoginController extends Controller
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
    public function login_match(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $user = Tbl_Retailers::where('username', $request->get('username'))->first();
        if(!empty($user)) {
            if(count($user->toArray()) > 0){
                if($user['Inactive'] == 1) {
                    if (Hash::check($request->get('password'), $user['password'])) {
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
                        ];
                        return response()->json(["status" => 1, 'retailer_data' => $retailerData, 'token' => $token], 200);
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
    }
}
