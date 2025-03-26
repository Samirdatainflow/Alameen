<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class PoweruserController extends Controller
{
    public function poweruser_profile_details(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_User_Master::select('name', 'email_id', 'mobile')->where([['inactive', '!=', '2'], ['id', '=', $request->user_id]])->get();
        $returnData = [];
	    if(count($select_data) > 0) {
            $returnData['name'] = $select_data[0]['name'];
            $returnData['email_id'] = $select_data[0]['email_id'];
            $returnData['mobile'] = $select_data[0]['mobile'];
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function poweruser_profile_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'name' => 'required|string',
            'email_id' => 'required|string',
            'mobile' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $arrayData = array('name' => $request->name, 'email_id' => $request->email_id, 'mobile' => $request->mobile);
        $update_data = Tbl_User_Master::where('id', $request->user_id)->update($arrayData);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Update successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Update faild."]);
        }
    }
    public function poweruser_password_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'old_password' => 'required|string',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $user_data = Tbl_User_Master::where('id', $request->user_id)->first();
        if(!empty($user_data)) {
            if(count($user_data->toArray()) > 0){
                if (Hash::check($request->old_password, $user_data['user_password'])) {
                    //return response()->json(["status" => 1, "msg" => "Match found."]);
                    if (Hash::check($request->password, $user_data['user_password'])) {
                        return response()->json(["status" => 0, "msg" => "Enter password match your old password. Please enter new password."]);
                    }else {
                        $data = Tbl_User_Master::where('id', $request->user_id)->update(['user_password' => bcrypt($request->password)]);
                        if($data) {
                            return response()->json(["status" => 1, "msg" => "Password update successful."]);
                        }else {
                            return response()->json(["status" => 0, "msg" => "Passsword update faild."]);
                        }
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Password not match. Please enter correct password."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function poweruser_profile_pic_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'profile_pic' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $profile_pic = $request->profile_pic;  // your base64 encoded
        $profile_pic = str_replace('data:image/png;base64,', '', $profile_pic);
        $profile_pic = str_replace(' ', '+', $profile_pic);
        $imageName = str_random(10) . '.png';
        $path = public_path().'/poweruser/images';
        $stor_image=\File::put($path. '/' . $imageName, base64_decode($profile_pic));
        if($stor_image) {
            $path = url('public/poweruser/images/'.$imageName);
            $update_profile_pic = Tbl_User_Master::where('id', $request->user_id)->update(['profile_pic' => $imageName]);
            if($update_profile_pic) {
                return response()->json(["status" => 1, "msg" => "Profile picture update successful.", 'path' => $path, 'image_name' => $imageName]);
            }else {
                return response()->json(["status" => 0, "msg" => "Profile picture update faild."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Error."]);
        }
    }
}