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
use App\Tbl_Retailers_Note;
use App\Tbl_Retailer_Feedback_Purpose;
use App\Tbl_Retailer_Feedback;
use App\Tbl_Schedule_Visit;
use App\Tbl_Retailers_Stage;
use App\Tbl_Product_Skus;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class SingleRetailerCtrl extends Controller
{
    public function retailer_profile_view_by_id(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
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
        $query->where([['r.Inactive', '!=', '2'], ['r.id', '=', $request->retailer_id]]);
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
    public function get_retailer_schedule_visit(Request $request){
        $validator = Validator::make($request->all(), [
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
                array_push($returnData, array('id' => $data->id, 'visit_date' => date('d M Y', strtotime($data->visit_date)), 'start_time' => date('h:i A', strtotime($data->start_time)), 'end_time' => date('h:i A', strtotime($data->end_time)), 'agenda' => $data->agenda, 'user_name' => $data->user_name, 'role' => $data->role));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function save_schedule_visit(Request $request) {
        $validator = Validator::make($request->all(), [
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
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->get('retailer_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $data = Tbl_Schedule_Visit::create([
            'fk_company_id' => $fk_company_id,
            'fk_retailer_id' => $request->retailer_id,
            'visit_date' => date('Y-m-d', strtotime($request->visit_date)),
            'start_time' => date('H:i:s', strtotime($request->start_time)),
            'end_time' => date('H:i:s', strtotime($request->end_time)),
            'agenda' => $request->agenda,
            //'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '11',
                'fk_retailer_id' => $request->retailer_id,
                //'created_by' => $request->user_id,
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
        $query->where([['n.fk_retailers_id', $request->get('retailer_id')], ['n.Inactive', "1"]]);
        $query->orderBy('id', 'DESC');
        $note_get_details = $query->get();
        //Tbl_Retailers_Note::select('note', 'created_at')->where([['fk_retailers_id', $request->get('retailer_id')],['fk_user_id', $request->get('user_id')], ['Inactive', "1"]])->orderBy('id', 'DESC')->get();
        if(count($note_get_details) > 0) {
            foreach($note_get_details as $data) {
                array_push($returnData, array('note' => $data->note, 'date' => date('h:i A, d M Y', strtotime($data->created_at)), 'user_name' => $data->user_name, 'role' => $data->role));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function save_retailer_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'note' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->get('retailer_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $data = Tbl_Retailers_Note::create([
            'fk_company_id' => $fk_company_id,
            'fk_retailers_id' => $request->retailer_id,
            //'fk_user_id' => $request->user_id,
            'note' => $request->note,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '10',
                'fk_retailers_id' => $request->retailer_id,
                //'created_by' => $request->user_id,
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
    // Retailer Feed back select
    public function get_retailer_feedback(Request $request){
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_retailer_feedback as f');
        $query->select('f.id', 'f.remarks', 'f.created_at', 'u.name as user_name');
        $query->join('tbl_user_master as u', 'u.id', '=', 'f.created_by', 'left');
        //$query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['f.fk_retailer_id', $request->get('retailer_id')], ['f.Inactive', "1"]]);
        $query->orderBy('f.id', 'DESC');
        $feedback_details = $query->get();
        //Tbl_Retailers_Note::select('note', 'created_at')->where([['fk_retailers_id', $request->get('retailer_id')],['fk_user_id', $request->get('user_id')], ['Inactive', "1"]])->orderBy('id', 'DESC')->get();
        if(count($feedback_details) > 0) {
            foreach($feedback_details as $data) {
                array_push($returnData, array('remarks' => $data->remarks, 'date' => date('h:i A, d M Y', strtotime($data->created_at)), 'user_name' => $data->user_name));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
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
    public function list_product_sku(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $skuData = [];
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
            $select_sku = Tbl_Product_Skus::select('id', 'sku_type')->where([['status', '!=', '2'], ['fk_company_id', '=', $fk_company_id]])->get();
            if(count($select_sku) > 0) {
                foreach($select_sku as $data) {
                    array_push($skuData, array('label' => $data->sku_type, 'value' => $data->id));
                }
                return response()->json(["status" => 1, "sku_data" => $skuData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function save_retailer_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'fk_purpose_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->get('retailer_id'))->get();
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
            //'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '5',
                'fk_retailer_id' => $request->retailer_id,
                //'created_by' => $request->user_id,
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
    public function retailer_password_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'old_password' => 'required|string',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $user_data = Tbl_Retailers::where('id', $request->retailer_id)->first();
        if(!empty($user_data)) {
            if(count($user_data->toArray()) > 0){
                if (Hash::check($request->old_password, $user_data['password'])) {
                    //return response()->json(["status" => 1, "msg" => "Match found."]);
                    if (Hash::check($request->password, $user_data['password'])) {
                        return response()->json(["status" => 0, "msg" => "Enter password match your old password. Please enter new password."]);
                    }else {
                        $data = Tbl_Retailers::where('id', $request->retailer_id)->update(['password' => bcrypt($request->password)]);
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
    public function retailer_profile_details(Request $request) {
        $returnData = [];
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Retailers::where('id', $request->retailer_id)->get();
        if(sizeof($select_data) > 0) {
            $returnData['name'] = $select_data[0]['name'];
            $returnData['external_id'] = "";
            if(!empty($select_data[0]['external_id'])) {
                $returnData['external_id'] = $select_data[0]['external_id'];
            }
            $returnData['street'] = $select_data[0]['street'];
            $returnData['locality'] = "";
            if(!empty($select_data[0]['locality'])) {
                $returnData['locality'] = $select_data[0]['locality'];
            }
            $returnData['district'] = "";
            if(!empty($select_data[0]['district'])) {
                $returnData['district'] = $select_data[0]['district'];
            }
            $returnData['pincode'] = "";
            if(!empty($select_data[0]['pincode'])) {
                $returnData['pincode'] = $select_data[0]['pincode'];
            }
            $returnData['mobile'] = "";
            if(!empty($select_data[0]['mobile'])) {
                $returnData['mobile'] = $select_data[0]['mobile'];
            }
            $returnData['contact_person_name'] = "";
            if(!empty($select_data[0]['contact_person_name'])) {
                $returnData['contact_person_name'] = $select_data[0]['contact_person_name'];
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function retailer_profile_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
            'name' => 'required|string',
            'street' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $arrayData = array('name' => $request->name, 'external_id' => $request->external_id, 'street' => $request->street, 'locality' => $request->locality, 'district' => $request->district, 'pincode' => $request->pincode, 'mobile' => $request->mobile, 'contact_person_name' => $request->contact_person_name);
        $update_data = Tbl_Retailers::where('id', $request->retailer_id)->update($arrayData);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Update successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Update faild."]);
        }
    }
    public function retailer_profile_pic_update(Request $request) {
        $validator = Validator::make($request->all(), [
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
        $path = public_path().'/user_pic/';
        $stor_image=\File::put($path. '/' . $imageName, base64_decode($profile_pic));
        if($stor_image) {
            $path = url('public/user_pic/'.$imageName);
            $update_profile_pic = Tbl_Retailers::where('id', $request->retailer_id)->update(['retailer_image' => $imageName]);
            if($update_profile_pic) {
                return response()->json(["status" => 1, "msg" => "Profile picture update successful.", 'path' => $path, 'image_name' => $imageName]);
            }else {
                return response()->json(["status" => 0, "msg" => "Profile picture update faild."]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "Error."]);
        }
    }
    public function retailer_call_option_details(Request $request) {
        $returnData = [];
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Retailers::select('sales_manager', 'territory_manager')->where('id', $request->retailer_id)->get();
        if(sizeof($select_data) > 0) {
            if(!empty($select_data[0]['sales_manager'])) {
                $query = DB::table('tbl_user_master as u');
                $query->select('u.id', 'u.name', 'u.mobile', 'u.reports_to', 'r.role');
                $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
                $query->where('u.id', $select_data[0]['sales_manager']);
                $select_sales_manager = $query->get();
                if(sizeof($select_sales_manager) > 0) {
                    array_push($returnData, array('id' => $select_sales_manager[0]->id, 'name' => $select_sales_manager[0]->name, 'mobile' => $select_sales_manager[0]->mobile, 'role' => $select_sales_manager[0]->role));
                    if(!empty($select_sales_manager[0]->reports_to)) {
                        $query = DB::table('tbl_user_master as u');
                        $query->select('u.id', 'u.name', 'u.mobile', 'u.reports_to', 'r.role');
                        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
                        $query->where('u.id', $select_sales_manager[0]->reports_to);
                        $select_data = $query->get();
                        if(sizeof($select_data) > 0) {
                            array_push($returnData, array('id' => $select_data[0]->id, 'name' => $select_data[0]->name, 'mobile' => $select_data[0]->mobile, 'role' => $select_data[0]->role));
                        }
                    }
                }
            }
            if(!empty($select_data[0]['territory_manager'])) {
                $query = DB::table('tbl_user_master as u');
                $query->select('u.id', 'u.name', 'u.mobile', 'u.reports_to', 'r.role');
                $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
                $query->where('u.id', $select_data[0]['territory_manager']);
                $select_territory_manager = $query->get();
                if(sizeof($select_territory_manager) > 0) {
                    array_push($returnData, array('id' => $select_territory_manager[0]->id, 'name' => $select_territory_manager[0]->name, 'mobile' => $select_territory_manager[0]->mobile, 'role' => $select_territory_manager[0]->role));
                    if(!empty($select_territory_manager[0]->reports_to)) {
                        $query = DB::table('tbl_user_master as u');
                        $query->select('u.id', 'u.name', 'u.mobile', 'u.reports_to', 'r.role');
                        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
                        $query->where('u.id', $select_territory_manager[0]->reports_to);
                        $select_data = $query->get();
                        if(sizeof($select_data) > 0) {
                            array_push($returnData, array('id' => $select_data[0]->id, 'name' => $select_data[0]->name, 'mobile' => $select_data[0]->mobile, 'role' => $select_data[0]->role));
                        }
                    }
                }
            }
            if(sizeof($returnData) > 0) {
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found"]);
            }
        }else {
            return response()->json(["status" => 0, "msg" => "No record found"]);
        }
    }
}