<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Tbl_Fcm_Token;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class OthersController extends Controller
{
	public function store_app_fcm_token(Request $request) {
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'fcm_token' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Fcm_Token::where([['fk_user_id', '=', $request->user_id], ['token_id', '=', $request->fcm_token]])->get()->toArray();
        if(sizeof($select_data) > 0) {
        	$data = Tbl_Fcm_Token::where([['fk_user_id', '=', $request->user_id], ['token_id', '=', $request->fcm_token]])->update(['fk_user_id' => $request->user_id, 'token_id' => $request->fcm_token]);
        	if($data) {
	        	return response()->json(["status" => 1, "msg" => "Save successful."]);
	        }else {
	            return response()->json(["status" => 0, "msg" => "Save faild."]);
	        }
        }else {
	        $data = Tbl_Fcm_Token::create([
	            'fk_user_id' => $request->user_id,
	            'token_id' => $request->fcm_token
	        ]);
	        if($data) {
	        	return response()->json(["status" => 1, "msg" => "Save successful."]);
	        }else {
	            return response()->json(["status" => 0, "msg" => "Save faild."]);
	        }
	    }
	}
}
?>