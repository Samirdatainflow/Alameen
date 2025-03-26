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
use App\Tbl_States;
use App\Tbl_Retailers_Route;
use App\Tbl_Team_Activity;
use App\Tbl_Team_Activity_Type;
use App\Tbl_Retailers_Note;
use App\Tbl_Retailer_Feedback_Purpose;
use App\Tbl_Retailer_Feedback;
use App\Tbl_Schedule_Visit;
use App\Tbl_Retailers_Stage;
use App\Tbl_Fcm_Token;
use App\Tbl_Firebase_Setting;
use App\Tbl_Task_Type;
use App\Tbl_Cities;
use App\Tbl_Order_Stock;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use DateTime;

class RetailerController extends Controller
{
    public function create_retailer(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'name' => 'required|string',
            'street' => 'required|string',
            'type' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $labels = null;
        if(!empty($request->get('labels'))) {
            $labels = $request->get('labels');
        }
        $chain = null;
        if(!empty($request->get('chain'))) {
            $chain = $request->get('chain');
        }
        $stage = null;
        if(!empty($request->get('stage'))) {
            $stage = $request->get('stage');
        }
        $custom_fields = "";
        if(!empty($request->get('custom_fields'))) {
            $custom_fields = serialize($request->get('custom_fields'));
        }
        $retailer_image = "";
        if(!empty($request->get('retailer_image'))) {
            $retailer_image = $request->get('retailer_image');
        }
        $data = Tbl_Retailers::create([
            'user_id' => $request->get('user_id'),
            'fk_company_id' => $fk_company_id,
            'name' => $request->get('name'),
            'external_id' => $request->get('external_id'),
            'street' => $request->get('street'),
            'locality' => $request->get('locality'),
            'city' => $request->get('city'),
            'district' => $request->get('district'),
            'state' => $request->get('state'),
            'pincode' => $request->get('pincode'),
            'mobile' => $request->get('mobile'),
            'contact_person_name' => $request->get('contact_person_name'),
            'type' => $request->get('type'),
            'sub_type' => $request->get('sub_type'),
            'class' => $request->get('class'),
            'labels' => $labels,
            'chain' => $chain,
            'stage' => $stage,
            'custom_fields' => $custom_fields,
            'sales_manager' => $request->get('sales_manager'),
            'territory_manager' => $request->get('territory_manager'),
            'route' => $request->get('route'),
            'landline' => $request->get('landline'),
            'email' => $request->get('email'),
            'fax' => $request->get('fax'),
            'type' => $request->get('type'),
            'remarks' => $request->get('remarks'),
            'credit_limit' => $request->get('credit_limit'),
            'retailer_image' => $retailer_image,
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'created_by' => $request->get('user_id'),
            'Inactive' => '1'
        ]);
        $last_id = $data->id;
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '2',
                'fk_retailer_id' => $last_id,
                'image' => $retailer_image,
                'created_by' => $request->get('user_id'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'Inactive' => '1',
            ]);
            if($data2) {
                if(!empty($request->get('route'))) {
                    $retailers_order = 0;
                    $select_retailers_route = Tbl_Retailers_Route::select('retailers_order')->where([['fk_retailers_id', '=', $data->id], ['fk_route_id', '=', $request->get('route')], ['fk_company_id', '=', $fk_company_id]])->orderBy('retailers_order', 'DESC')->get();
                    if(count($select_retailers_route) > 0) {
                        $retailers_order = intval($select_retailers_route[0]['retailers_order'])+1;
                    }
                    $insert_retailers_route = Tbl_Retailers_Route::create([
                        'fk_company_id' => $fk_company_id,
                        'fk_retailers_id' => $data->id,
                        'fk_route_id' => $request->get('route'),
                        'retailers_order' => $retailers_order,
                    ]);
                    if($insert_retailers_route) {
                        return response()->json(["status" => 1, "msg" => "Save successful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Save faild. Something is wrong, please try again."]);
                    }
                }else {
                    return response()->json(["status" => 1, "msg" => "Save successful."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
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
    public function retailer_sales_manager(Request $request) {
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
            $select_sales_manager = Tbl_User_Master::select('id', 'name')->where([['fk_role_id', '2'], ['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_sales_manager) > 0) {
                return response()->json(["status" => 1, "data" => $select_sales_manager]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function retailer_territory_manager(Request $request) {
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
            $select_territory_manager = Tbl_User_Master::select('id', 'name')->where([['fk_role_id', '3'], ['inactive', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_territory_manager) > 0) {
                return response()->json(["status" => 1, "data" => $select_territory_manager]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function retailer_profile_pic_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'profile_pic' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $profile_pic = $request->profile_pic;  // your base64 encoded
	    $profile_pic = str_replace('data:image/png;base64,', '', $profile_pic);
	    $profile_pic = str_replace(' ', '+', $profile_pic);
	    $imageName = str_random(10) . '.png';
	    $path = public_path().'/retailers/images/';
	    $stor_image=\File::put($path. '/' . $imageName, base64_decode($profile_pic));
	    if($stor_image) {
            $path = url('public/retailers/images/'.$imageName);
            $select_retailers = Tbl_Retailers::where([['id', '=', $request->retailer_id], ['user_id', '=', $request->user_id], ['Inactive', '!=', '2']])->get();
            if(count($select_retailers) > 0) {
                $update_profile_pic = Tbl_Retailers::where([['id', '=', $request->retailer_id], ['user_id', '=', $request->user_id]])->update(['retailer_image' => $imageName]);
                if($update_profile_pic) {
                    return response()->json(["status" => 1, "msg" => "Profile picture update successful.", 'path' => $path, 'image_name' => $imageName]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Profile picture update faild."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Record not found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Error."]);
        }
    }
    public function retailer_profile_pic_save(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_pic' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $profile_pic = $request->profile_pic;  // your base64 encoded
	    $profile_pic = str_replace('data:image/png;base64,', '', $profile_pic);
	    $profile_pic = str_replace(' ', '+', $profile_pic);
	    $imageName = str_random(10) . '.png';
	    $path = public_path().'/retailers/images/';
	    $stor_image=\File::put($path. '/' . $imageName, base64_decode($profile_pic));
	    if($stor_image) {
            return response()->json(["status" => 1, "msg" => "Profile picture save successful.", 'image_name' => $imageName]);
        }else {
            
            return response()->json(["status" => 0, "msg" => "Profile picture save faild."]);
        }
    }
    public function check_retailer_mobile_exist(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'mobile' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Retailers::select('mobile')->where([['user_id', '=', $request->get('user_id')], ['mobile', '=', $request->get('mobile')]])->get();
        if(count($select_data) > 0) {
            return response()->json(["status" => 0, "msg" => "Enter mobile no already taken. Please try with another mobile no."]);
        }else {
            return response()->json(["status" => 1]);
        }
    }
    public function retailer_profile_view(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_retailers as r');
        $query->select('r.id', 'r.name', 'r.external_id', 'r.street', 'r.locality', 'c.name as city_name', 'r.district', 's.name as state_name', 'r.pincode', 'r.mobile', 'r.contact_person_name', 't.code as type', 'st.code as sub_type', 'cl.class_name', 'l.label_text', 'ch.name as chain', 'sta.stage_name', 'ro.name as route_name', 'r.landline', 'r.email', 'r.retailer_image', 'r.fax', 'r.remarks', 'r.latitude', 'r.longitude', 'r.credit_limit');
        $query->join('tbl_retailer_type as t', 't.id', '=', 'r.type', 'left');
        $query->join('tbl_retailer_sub_type as st', 'st.id', '=', 'r.sub_type', 'left');
        $query->join('tbl_retailers_classification as cl', 'cl.id', '=', 'r.class', 'left');
        $query->join('tbl_retailers_labels as l', 'l.id', '=', 'r.labels', 'left');
        $query->join('tbl_retailers_chain as ch', 'ch.id', '=', 'r.chain', 'left');
        $query->join('tbl_retailers_stage as sta', 'sta.id', '=', 'r.stage', 'left');
        $query->join('tbl_routes as ro', 'ro.id', '=', 'r.route', 'left');
        $query->join('tbl_cities as c', 'c.id', '=', 'r.city', 'left');
        $query->join('tbl_states as s', 's.id', '=', 'r.state', 'left');
        $query->where([['r.Inactive', '!=', '2'], ['r.fk_company_id', '=', $fk_company_id], ['r.id', '=', $request->retailer_id]]);
        $select_data = $query->get();
        //print_r($select_data); exit();
	    if(count($select_data) > 0) {
            $returnData = [];
            foreach($select_data as $sdata) {
                $returnData['id'] = $sdata->id;
                $returnData['name'] = $sdata->name;
                $returnData['external_id'] = $sdata->external_id;
                $returnData['street'] = $sdata->street;
                $returnData['locality'] = $sdata->locality;
                $returnData['city_name'] = $sdata->city_name;
                $returnData['district'] = $sdata->district;
                $returnData['state_name'] = $sdata->state_name;
                $returnData['pincode'] = $sdata->pincode;
                $returnData['mobile'] = $sdata->mobile;
                $returnData['contact_person_name'] = $sdata->contact_person_name;
                $returnData['type'] = $sdata->type;
                $returnData['sub_type'] = $sdata->sub_type;
                $returnData['class_name'] = $sdata->class_name;
                $returnData['label_text'] = $sdata->label_text;
                $returnData['chain'] = $sdata->chain;
                $returnData['stage_name'] = $sdata->stage_name;
                $returnData['route_name'] = $sdata->route_name;
                $returnData['landline'] = $sdata->landline;
                $returnData['email'] = $sdata->email;
                $returnData['retailer_image'] = $sdata->retailer_image;
                $returnData['fax'] = $sdata->fax;
                $returnData['remarks'] = $sdata->remarks;
                $returnData['latitude'] = $sdata->latitude;
                $returnData['longitude'] = $sdata->longitude;
                $returnData['credit_limit'] = $sdata->credit_limit;         
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    
    public function save_retailer_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'note' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $data = Tbl_Retailers_Note::create([
            'fk_company_id' => $fk_company_id,
            'fk_retailers_id' => $request->retailer_id,
            'fk_user_id' => $request->user_id,
            'note' => $request->note,
            'Inactive' => '1'
        ]);
	    if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '10',
                'fk_retailers_id' => $request->retailer_id,
                'created_by' => $request->user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'Inactive' => '1'
            ]);
            if($data2) {
                return response()->json(["status" => 1, "msg" => "Save successful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function get_retailer_note(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_retailers_note as n');
        $query->select('n.id', 'n.note', 'n.created_at', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'n.fk_user_id', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['n.fk_retailers_id', $request->get('retailer_id')],['n.fk_user_id', $request->get('user_id')], ['n.Inactive', "1"]]);
        $query->orderBy('id', 'DESC');
        $note_get_details = $query->get();
        //Tbl_Retailers_Note::select('note', 'created_at')->where([['fk_retailers_id', $request->get('retailer_id')],['fk_user_id', $request->get('user_id')], ['Inactive', "1"]])->orderBy('id', 'DESC')->get();
        if(count($note_get_details) > 0) {
            foreach($note_get_details as $data) {
                array_push($returnData, array('id' => $data->id, 'note' => $data->note, 'date' => date('h:i A, d M Y', strtotime($data->created_at)), 'user_name' => $data->user_name, 'role' => $data->role));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function get_retailer_note_by_id(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Retailers_Note::select('note')->where([['id', '=', $request->noteId], ['Inactive', '=', '1']])->get()->toArray();
        if(sizeof($select_data) > 0) {
            return response()->json(["status" => 1, "data" => $select_data[0]['note']]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function save_retailer_note_by_id(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int',
            'note' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $update_data = Tbl_Retailers_Note::where([['id', '=', $request->noteId]])->update(['note' => $request->note]);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function delete_retailer_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $update_data = Tbl_Retailers_Note::where([['id', '=', $request->noteId]])->update(['Inactive' => '2']);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Delete successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Delete faild."]);
        }
    }
    public function list_retailer_feedback_purpose(Request $request) {
        $purposeData = [];
        $select_data = Tbl_Retailer_Feedback_Purpose::select('id', 'purpose_name')->where([['Inactive', "1"]])->orderBy('purpose_name', 'ASC')->get();
        if(count($select_data) > 0) {
            foreach($select_data as $data) {
                array_push($purposeData, array('label' => $data->purpose_name, 'value' => $data->id));
            }
            return response()->json(["status" => 1, "purposeData" => $purposeData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function retailer_feedback_pic_save(Request $request) {
        $validator = Validator::make($request->all(), [
            'feedback_pic' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $feedback_pic = $request->feedback_pic;  // your base64 encoded
	    $feedback_pic = str_replace('data:image/png;base64,', '', $feedback_pic);
	    $feedback_pic = str_replace(' ', '+', $feedback_pic);
	    $imageName = str_random(10) . '.png';
	    $path = public_path().'/retailers/images/feedback/';
	    $stor_image=\File::put($path. '/' . $imageName, base64_decode($feedback_pic));
	    if($stor_image) {
            return response()->json(["status" => 1, "msg" => "Picture save successful.", 'image_name' => $imageName]);
        }else {
            
            return response()->json(["status" => 0, "msg" => "Picture save faild."]);
        }
    }
    public function save_retailer_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'fk_purpose_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        //return response()->json(["status" => 0, "msg" => $request->retailer_id."--".$request->user_id]); exit();
        $data = Tbl_Retailer_Feedback::create([
            'fk_company_id' => $fk_company_id,
            'fk_retailer_id' => $request->retailer_id,
            'fk_purpose_id' => $request->fk_purpose_id,
            'feedback_type' => $request->feedback_type,
            'fk_sku_id' => $request->fk_sku_id,
            'remarks' => $request->remarks,
            'photo' => $request->photo,
            'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
	    if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '5',
                'fk_retailer_id' => $request->retailer_id,
                'created_by' => $request->user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'Inactive' => '1'
            ]);
            if($data2) {
                return response()->json(["status" => 1, "msg" => "Save successful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    /* =====================
        Schedule
    ===================== */
    public function list_task_type (Request $request) {
        $returnData = [];
        $select_data = Tbl_Task_Type::select('task_type_id', 'task_type_name')->where([['status', '=', "1"]])->orderBy('task_type_id', 'DESC')->get();
        if(count($select_data) > 0) {
            foreach($select_data as $data) {
                array_push($returnData, array('label' => $data->task_type_name, 'value' => $data->task_type_id));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function save_schedule_visit(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'visit_date' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'agenda' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $fk_type_id = "";
        if($request->task_type == "1") {
            $taskType = Tbl_Task_Type::create([
                'task_type_name' => $request->other_type,
                'status' => '1'
            ]);
            if($taskType) {
                $fk_type_id = $taskType->id;
            }
        }else {
            $fk_type_id = $request->task_type;
        }
        $data = Tbl_Schedule_Visit::create([
            'fk_company_id' => $fk_company_id,
            'fk_retailer_id' => $request->retailer_id,
            'visit_date' => date('Y-m-d', strtotime($request->visit_date)),
            'start_time' => date('H:i:s', strtotime($request->start_time)),
            'end_time' => date('H:i:s', strtotime($request->end_time)),
            'agenda' => $request->agenda,
            'fk_type_id' => $fk_type_id,
            'visit_status' => $request->visit_status,
            'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
	    if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '11',
                'fk_retailer_id' => $request->retailer_id,
                'created_by' => $request->user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'Inactive' => '1'
            ]);
            if($data2) {
                return response()->json(["status" => 1, "msg" => "Save successful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function update_schedule_visit(Request $request) {
        $validator = Validator::make($request->all(), [
            'visitId' => 'required|int',
            'visit_date' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'agenda' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_type_id = "";
        if($request->task_type == "1") {
            $taskType = Tbl_Task_Type::create([
                'task_type_name' => $request->other_type,
                'status' => '1'
            ]);
            if($taskType) {
                $fk_type_id = $taskType->id;
            }
        }else {
            $fk_type_id = $request->task_type;
        }
        $data = Tbl_Schedule_Visit::where([['id', '=', $request->visitId]])->update([
            'visit_date' => date('Y-m-d', strtotime($request->visit_date)),
            'start_time' => date('H:i:s', strtotime($request->start_time)),
            'end_time' => date('H:i:s', strtotime($request->end_time)),
            'agenda' => $request->agenda,
            'fk_type_id' => $fk_type_id,
            'visit_status' => $request->visit_status,
        ]);
        if($data) {
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function delete_schedule_visit(Request $request) {
        $validator = Validator::make($request->all(), [
            'visitId' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $data = Tbl_Schedule_Visit::where([['id', '=', $request->visitId]])->update([
            'Inactive' => '2'
        ]);
        if($data) {
            return response()->json(["status" => 1, "msg" => "Delete successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Delete faild."]);
        }
    }
    public function get_retailer_schedule_visit(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_schedule_visit as v');
        $query->select('v.id', 'v.visit_date', 'v.start_time', 'v.end_time', 'v.agenda', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'v.created_by', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['v.fk_retailer_id', $request->get('retailer_id')], ['v.Inactive', "1"]]);
        $query->orderBy('v.id', 'DESC');
        $note_get_details = $query->get();
        if(count($note_get_details) > 0) {
            foreach($note_get_details as $data) {
                $start_time = '';
                if(!empty($data->start_time) && $data->start_time > 0) {
                    $start_time = date('h:i A', strtotime($data->start_time));
                }
                $end_time = '';
                if(!empty($data->end_time) && $data->end_time > 0) {
                    $end_time = date('h:i A', strtotime($data->end_time));
                }
                array_push($returnData, array('id' => $data->id, 'visit_date' => date('d M Y', strtotime($data->visit_date)), 'start_time' => $start_time, 'end_time' => $end_time, 'agenda' => $data->agenda, 'user_name' => $data->user_name, 'role' => $data->role));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function get_retailer_schedule_visit_details(Request $request){
        $validator = Validator::make($request->all(), [
            'visitId' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_schedule_visit as v');
        $query->select('v.id', 'v.visit_date', 'v.start_time', 'v.end_time', 'v.agenda', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'v.created_by', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['v.id', $request->get('visitId')]]);
        $note_get_details = $query->get();
        if(count($note_get_details) > 0) {
            foreach($note_get_details as $data) {
                array_push($returnData, array('id' => $data->id, 'visit_date' => date('d M Y', strtotime($data->visit_date)), 'start_time' => date('h:i A', strtotime($data->start_time)), 'end_time' => date('h:i A', strtotime($data->end_time)), 'agenda' => $data->agenda, 'user_name' => $data->user_name, 'role' => $data->role));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function edit_retailer_schedule_visit_details(Request $request){
        $validator = Validator::make($request->all(), [
            'visitId' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_schedule_visit as v');
        $query->select('v.id', 'v.visit_date', 'v.start_time', 'v.end_time', 'v.agenda', 'v.fk_type_id', 'v.visit_status', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'v.created_by', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['v.id', $request->get('visitId')]]);
        $note_get_details = $query->get();
        if(count($note_get_details) > 0) {
            foreach($note_get_details as $data) {
                $start_time = '00:00';
                if(!empty($data->start_time) && $data->start_time > 0) {
                    $start_time = date('h:i', strtotime($data->start_time));
                }
                $end_time = '00:00';
                if(!empty($data->end_time) && $data->end_time > 0) {
                    $end_time = date('h:i', strtotime($data->end_time));
                }
                array_push($returnData, array('id' => $data->id, 'visit_date' => date('Y-m-d', strtotime($data->visit_date)), 'start_time' => $start_time, 'end_time' => $end_time, 'agenda' => $data->agenda, 'fk_type_id' => $data->fk_type_id, 'visit_status' => $data->visit_status));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function get_retailer_today_schedule(Request $request){
        $returnData = [];
        $selectFcmUserID = Tbl_Fcm_Token::select('fk_user_id')->get()->toArray();
        if(sizeof($selectFcmUserID) > 0) {
            foreach($selectFcmUserID as $fcmdata) {
                $selectData = Tbl_Schedule_Visit::select('start_time', 'end_time', 'agenda')->where([['fk_retailer_id', '=', $fcmdata['fk_user_id']], ['visit_date', '=', date('Y-m-d')]])->get()->toArray();
                if(sizeof($selectData) > 0) {
                    foreach($selectData as $data) {
                        $minutes = 0;
                        if(strtotime($data['start_time']) > strtotime(date('H:i:s'))) {
                            $start_date = new DateTime(date('H:i:s'));
                            $since_start = $start_date->diff(new DateTime($data['start_time']));
                            $minutes += $since_start->h * 60;
                            $minutes += $since_start->i;
                        }
                        //echo $minutes;
                        if($minutes == 30) {
                            array_push($returnData, array('start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'agenda' => $data['agenda'], 'time_distance' => $minutes));
                            /* ======================
                            Send push notification
                            ====================== */
                            $token_id = "";
                            $select_token = Tbl_Fcm_Token::select('token_id')->where([['fk_user_id', '=', $fcmdata['fk_user_id']]])->get()->toArray();
                            if(sizeof($select_token) > 0) {
                                $token_id = $select_token[0]['token_id'];
                            }
                            $authorization_key = "";
                            $select_authorization = Tbl_Firebase_Setting::select('authorization_key')->get()->toArray();
                            if(sizeof($select_authorization) > 0) {
                                $authorization_key = $select_authorization[0]['authorization_key'];
                            }
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $fields = array (
                                        'registration_ids' => array (
                                                $token_id
                                        ),
                                        'data' => array (
                                            "title" => "You have a recent schedule.",
                                            "text" => "Please check your schedule.",
                                        ),
                                        'notification' => array(
                                            "title" => "You have a recent schedule.",
                                            "text" => "Please check your schedule.", 
                                        ),
                                    );
                            $fields = json_encode ( $fields );

                            $headers = array (
                                    'Authorization: key=' . $authorization_key,
                                    'Content-Type: application/json'
                            );
                            $ch = curl_init ();
                            curl_setopt ( $ch, CURLOPT_URL, $url );
                            curl_setopt ( $ch, CURLOPT_POST, true );
                            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
                            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
                            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

                            $result = curl_exec ( $ch );
                            curl_close ( $ch );
                        }
                    }
                    return response()->json(["status" => 1, "data" => $returnData, 'minutes' => $minutes]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found."]);
                }
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* =====================
        Stage
    ===================== */
    public function list_retailer_stage(Request $request) {
        $returnData = [];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $selected_stage = "";
        $selectRetailer = Tbl_Retailers::select('stage')->where([['id', '=', $request->retailer_id]])->get();
        if(count($selectRetailer) > 0) {
            if(!empty($selectRetailer[0]->stage)) $selected_stage = $selectRetailer[0]->stage;
        }
        $select_data = Tbl_Retailers_Stage::select('id', 'stage_name')->where([['fk_company_id', '=', $fk_company_id], ['inactive', '=', "1"]])->orderBy('stage_name', 'ASC')->get();
        if(count($select_data) > 0) {
            foreach($select_data as $data) {
                array_push($returnData, array('label' => $data->stage_name, 'value' => $data->id));
            }
            return response()->json(["status" => 1, "data" => $returnData, 'selected_stage' => $selected_stage]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function save_retailer_stage(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'retailer_id' => 'required|int',
            'stage_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $update_data = Tbl_Retailers::where([['id', '=', $request->retailer_id]])->update(['stage' => $request->stage_id]);
	    if($update_data) {
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function get_geolocation(Request $request) {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        //
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($request->latitude).','.trim($request->longitude).'&key=AIzaSyAdiMVa6EXhT44bqwpOYpfUXH_AXl7njOI&sensor=false';
         $json = @file_get_contents($url);
         $data=json_decode($json);
         print_r($data); exit();
         $status = $data->status;
         if($status=="OK")
         {
           return $data->results[0]->formatted_address;
         }
         else
         {
           return false;
         }
         exit();
        //
        $returnData = [];
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $geolocation = $latitude.','.$longitude;
        $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false&key=AIzaSyBivJ_SqRX-nq4qCYgp1TeICr42ZzRx_F0';
        //$request = 'https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyBivJ_SqRX-nq4qCYgp1TeICr42ZzRx_F0';
        // Link - https://developers.google.com/maps/documentation/geolocation/get-api-key
        //print_r($request);
        $file_contents = file_get_contents($request);
        $json_decode = json_decode($file_contents);
        print_r($json_decode);
        if(isset($json_decode->results[0])) {
            $response = array();
            foreach($json_decode->results[0]->address_components as $addressComponet) {
                if(in_array('political', $addressComponet->types)) {
                        $response[] = $addressComponet->long_name; 
                }
            }

            if(isset($response[0])){ $first  =  $response[0];  } else { $first  = 'null'; }
            if(isset($response[1])){ $second =  $response[1];  } else { $second = 'null'; } 
            if(isset($response[2])){ $third  =  $response[2];  } else { $third  = 'null'; }
            if(isset($response[3])){ $fourth =  $response[3];  } else { $fourth = 'null'; }
            if(isset($response[4])){ $fifth  =  $response[4];  } else { $fifth  = 'null'; }

            if( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null' ) {
                echo "<br/>Address:: ".$first;
                echo "<br/>City:: ".$second;
                echo "<br/>State:: ".$fourth;
                echo "<br/>Country:: ".$fifth;
            }
            else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null'  ) {
                echo "<br/>Address:: ".$first;
                echo "<br/>City:: ".$second;
                echo "<br/>State:: ".$third;
                echo "<br/>Country:: ".$fourth;
            }
            else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null' ) {
                echo "<br/>City:: ".$first;
                echo "<br/>State:: ".$second;
                echo "<br/>Country:: ".$third;
            }
            else if ( $first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
                echo "<br/>State:: ".$first;
                echo "<br/>Country:: ".$second;
            }
            else if ( $first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
                echo "<br/>Country:: ".$first;
            }
          }
    }
    public function get_history(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $selectData = Tbl_Team_Activity::where([['fk_retailer_id', '=', $request->retailer_id], ['Inactive', '=', '1']])->get();
        if(count($selectData) > 0) {
            foreach($selectData as $data) {
                $type_name = '';
                $created_by = '';
                $created_at = '';
                if(!empty($data->fk_activity_type_id)) {
                    $selectType = Tbl_Team_Activity_Type::select('type_name')->where([['id', '=', $data->fk_activity_type_id]])->get();
                    if(sizeof($selectType) > 0) {
                        $type_name = $selectType[0]->type_name;
                    }
                }
                if(!empty($data->created_by)) {
                    $selectUser = Tbl_User_Master::select('name')->where([['id', '=', $data->created_by]])->get();
                    if(sizeof($selectUser) > 0) {
                        $created_by = $selectUser[0]->name;
                    }
                }
                if(!empty($data->created_at)) {
                    $created_at = date('M, d Y', strtotime($data->created_at));
                }
                array_push($returnData, array('id' => $data->id, 'type_name' => $type_name, 'created_by' => $created_by, 'created_at' => $created_at));
            }
        }
        return response()->json(["status" => 1, "data" => $returnData]);
    }
    public function get_retailer_more_details(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $selectData = Tbl_Retailers::where([['id', '=', $request->retailer_id], ['Inactive', '=', '1']])->get();
        if(count($selectData) > 0) {
            foreach($selectData as $data) {
                $city_name = '';
                $state_name = '';
                if(!empty($data->city)) {
                    $selectCity = Tbl_Cities::select('name')->where([['id', '=', $data->city]])->get();
                    if(sizeof($selectCity) > 0) {
                        $city_name = $selectCity[0]->name;
                    }
                }
                if(!empty($data->state)) {
                    $selectState = Tbl_States::select('name')->where([['id', '=', $data->state]])->get();
                    if(sizeof($selectState) > 0) {
                        $state_name = $selectState[0]->name;
                    }
                }
                $returnData['id'] = $data->id;
                $returnData['street'] = $data->street;
                $returnData['locality'] = $data->locality;
                $returnData['city'] = $city_name;
                $returnData['district'] = $data->district;
                $returnData['state'] = $state_name;
                $returnData['pincode'] = $data->pincode;
                $returnData['mobile'] = $data->mobile;
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function list_order_history(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_order_stock as s');
        $query->select('s.id', 'p.product_name', 's.quantity', 's.created_at');
        $query->join('tbl_products as p', 'p.id', '=', 's.fk_product_id', 'left');
        $query->where([['s.stock_by', '=', $request->retailer_id], ['s.stock_type', '=', '0']]);
        $query->orderBy('s.id', 'DESC');
        $selectData = $query->get();
        if(count($selectData) > 0) {
            foreach($selectData as $data) {
                $date = '';
                if(!empty($data->created_at)) {
                    $date = date('M d Y', strtotime($data->created_at));
                }
                array_push($returnData, array('product_name' => $data->product_name, 'quantity' => $data->quantity, 'date' => $date));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
}
