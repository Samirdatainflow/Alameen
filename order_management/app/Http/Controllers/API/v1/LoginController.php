<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Clients;
//use Hash;
// use JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function authenticate(Request $request) {
        // print_r($request->all());
        // die;
        $arrayData = [];
        $v = Validator::make(
            [
                'email' => $request->email,
                'password' => $request->password
            ],
            [
                'email' => 'required',
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
            $data = Clients::where('customer_email_id', $request->email)->where('password',base64_encode($request->password))
               ->get();
            if(count($data) > 0){
                if($data[0]->delete_status ==  0) {
                    $user = Clients::where([['customer_email_id', '=', $request->email]])->first();
                    //$token = JWTAuth::fromUser($user);
                    $arrayData=array('id' => $data[0]->client_id,'customer_name' => $data[0]->customer_name, 'customer_email_id' => $data[0]->customer_email_id);
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
        }
        return response()->json($response, 200);
    }
    
}
