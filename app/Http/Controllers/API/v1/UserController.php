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
use App\Tbl_Designation;
use App\Tbl_States;
use App\Tbl_Cities;
use DB;
//use Hash;
use Image;
use Storage;
use JWTAuth;
use File;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function user_profile(Request $request) {
        $userData = [];
        if(!empty($request->user_id)) {
            $user_data = Tbl_User_Master::where('id', $request->user_id)->first();
            if(!empty($user_data)) {
                $role = "";
                if(!empty($user_data['fk_role_id'])) {
                    $TypeData = Tbl_Role::where('id', $user_data['fk_role_id'])->first();
                    if(!empty($TypeData)) {
                        $role = $TypeData['role'];
                    }
                }
                $designation = "";
                if(!empty($user_data['designation'])) {
                    $DesignationData = Tbl_Designation::where('id', $user_data['designation'])->first();
                    if(!empty($DesignationData)) {
                        $designation = $DesignationData['designation_name'];
                    }
                }
                $userData = [
                    "id" => $user_data['id'],
                    "name" => $user_data['name'],
                    "email" => $user_data["email_id"],
                    'user_name' => $user_data['user_name'],
                    'mobile' => $user_data['mobile'],
                    "role" => $role,
                    'designation' => $designation,
                    "user_type" => $user_data['user_type']
                ];
                return response()->json(["status" => 1, 'data' => $userData], 200);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Something is wrong."]);
        }
    }
    public function user_profile_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'name' => 'required|string',
            'mobile' => 'required|int',
            'email' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $data = Tbl_User_Master::where('id', $request->user_id)->update(['name' => $request->name, 'mobile' => $request->mobile, 'email_id' => $request->email]);
        if($data) {
            return response()->json(["status" => 1, "msg" => "Update successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Update faild."]);
        }
    }
    public function user_old_password_check(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'old_password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $user_data = Tbl_User_Master::where('id', $request->user_id)->first();
        if(!empty($user_data)) {
            if(count($user_data->toArray()) > 0){
                if (Hash::check($request->old_password, $user_data['user_password'])) {
                    return response()->json(["status" => 1, "msg" => "Match found."]);
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
    public function user_password_update(Request $request) {
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
        // $user_data = Tbl_User_Master::where('id', $request->user_id)->first();
        // if(!empty($user_data)) {
        //     if(count($user_data->toArray()) > 0){
        //         if (Hash::check($request->password, $user_data['user_password'])) {
        //             return response()->json(["status" => 0, "msg" => "Enter password match your old password. Please enter new password."]);
        //         }else {
        //             $data = Tbl_User_Master::where('id', $request->user_id)->update(['user_password' => bcrypt($request->password)]);
        //             if($data) {
        //                 return response()->json(["status" => 1, "msg" => "Password update successful."]);
        //             }else {
        //                 return response()->json(["status" => 0, "msg" => "Passsword update faild."]);
        //             }
        //         }
        //     }else {
        //         return response()->json(["status" => 0, "msg" => "No record found."]);
        //     }
        // }else {
        //     return response()->json(["status" => 0, "msg" => "No record found."]);
        // }
    }
    public function user_role_list(Request $request) {
        $user_type = Tbl_Role::select('id', 'role')->where('status', '!=', '2')->get();
        if(count($user_type) > 0) {
            return response()->json(["status" => 1, "data" => $user_type]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_designation(Request $request) {
        $user_type = Tbl_designation::select('id', 'designation_name')->where('status', '!=', '2')->get();
        if(count($user_type) > 0) {
            return response()->json(["status" => 1, "data" => $user_type]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function user_profile_pic_update(Request $request) {
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
	    $path = public_path().'/user_pic/';
	    $stor_image=\File::put($path. '/' . $imageName, base64_decode($profile_pic));
	    if($stor_image) {
	    	$path = url('public/user_pic/'.$imageName);
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
    public function postTest() {
	    $data = Input::all();
	    $png_url = "product-".time().".png";
	    $path = public_path().'img/designs/' . $png_url;

	    Image::make(file_get_contents($data->base64_image))->save($path);     
	    $response = array(
	        'status' => 'success',
	    );
	    return Response::json( $response  );
    }
    // List user state
    public function list_user_state(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
        ]);
        if($validator->fails()){
                return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->user_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $state = [];
            $query = DB::table('tbl_company_master as c');
            $query->select('c.country');
            $query->join('tbl_user_master as u', 'u.fk_parent_id', '=', 'c.id', 'left');
            $query->where([['u.inactive', '!=', '2'], ['u.id', '=', $fk_company_id]]);
            $user_data = $query->get();
            if(count($user_data) > 0) {
                $select_state = Tbl_States::select('id', 'name')->where('country_id', $user_data[0]->country)->get();
                if(count($select_state) > 0) {
                    $state = $select_state;
                    return response()->json(["status" => 1, "data" => $state]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found."]);
                }
            }

            // $select_stage = Tbl_Retailers_Stage::select('id', 'stage_name')->where([['Inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            // if(count($select_stage) > 0) {
            //     return response()->json(["status" => 1, "data" => $select_stage]);
            // }else {
            //     return response()->json(["status" => 0, "msg" => "No record found."]);
            // }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    
    // List City
    public function list_user_city(Request $request) {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $cityData = [];
        $select_data = Tbl_Cities::select(array('id', 'name'))->where('state_id', $request->state_id)->get();
        if(count($select_data) > 0) {
            foreach($select_data as $s_data) {
                array_push($cityData, array('label' => $s_data->name, 'value' => $s_data->id));
            }
            return response()->json(["status" => 1, "data" => $cityData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
}
