<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Company_Master;
use App\Tbl_States;
use App\Tbl_Distributor;
use App\Tbl_Distributor_Feedback;
use App\Tbl_Team_Activity;
use App\Tbl_Schedule_Visit;
use App\Tbl_Distributor_Note;
use App\Tbl_Distributor_Query;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class DistributorController extends Controller
{
    public function list_distributor(Request $request){
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
        $distributor_data = Tbl_Distributor::select('id', 'distributor', 'street')->where([['fk_company_id', '=', $fk_company_id], ['Inactive', '=', "1"]])->get();
        if(count($distributor_data) > 0) {
            return response()->json(["status" => 1, "data" => $distributor_data]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    public function distributor_profile_view(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $query = DB::table('tbl_distributor as d');
        $query->select('d.id', 'd.distributor', 'd.mobile', 'd.street', 'c.name as city_name', 's.name as state_name');
        $query->join('tbl_cities as c', 'c.id', '=', 'd.city', 'left');
        $query->join('tbl_states as s', 's.id', '=', 'd.state', 'left');
        $query->where([['d.Inactive', '=', '1'], ['d.fk_company_id', '=', $fk_company_id], ['d.id', '=', $request->distributor_id]]);
        $select_data = $query->get();
        if(count($select_data) > 0) {
            $returnData = [];
            $returnData['id'] = $select_data[0]->id;
            $returnData['distributor'] = $select_data[0]->distributor;
            $returnData['mobile'] = $select_data[0]->mobile;
            $returnData['street'] = $select_data[0]->street;
            $returnData['city_name'] = $select_data[0]->city_name;
            $returnData['state_name'] = $select_data[0]->state_name;
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }

    }
    // Feedback
    public function distributor_feedback_pic_save(Request $request) {
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
	    $path = public_path().'/distributor/images/feedback/';
	    $stor_image=\File::put($path. '/' . $imageName, base64_decode($feedback_pic));
	    if($stor_image) {
            return response()->json(["status" => 1, "msg" => "Picture save successful.", 'image_name' => $imageName]);
        }else {
            
            return response()->json(["status" => 0, "msg" => "Picture save faild."]);
        }
    }
    public function save_distributor_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int',
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
        $data = Tbl_Distributor_Feedback::create([
            'fk_company_id' => $fk_company_id,
            'fk_distributor_id' => $request->distributor_id,
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
                'fk_distributor_id' => $request->distributor_id,
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

    public function save_schedule_visit(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int',
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
        // echo date('H:i:s a', strtotime($request->start_time));
        // exit();
        $data = Tbl_Schedule_Visit::create([
            'fk_company_id' => $fk_company_id,
            'fk_distributor_id' => $request->distributor_id,
            'visit_date' => date('Y-m-d', strtotime($request->visit_date)),
            'start_time' => date('H:i:s', strtotime($request->start_time)),
            'end_time' => date('H:i:s', strtotime($request->end_time)),
            'agenda' => $request->agenda,
            'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '11',
                'fk_distributor_id' => $request->distributor_id,
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
    public function get_schedule_visit(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_schedule_visit as v');
        $query->select('v.id', 'v.visit_date', 'v.start_time', 'v.end_time', 'v.agenda', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'v.created_by', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['v.fk_distributor_id', $request->get('distributor_id')], ['v.Inactive', "1"]]);
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
    public function distributor_schedule_visit_details(Request $request){
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
    public function distributor_schedule_details_by_id(Request $request){
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
                array_push($returnData, array('id' => $data->id, 'visit_date' => date('Y-m-d', strtotime($data->visit_date)), 'start_time' => date('h:i', strtotime($data->start_time)), 'end_time' => date('h:i', strtotime($data->end_time)), 'agenda' => $data->agenda));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
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
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        // echo date('H:i:s a', strtotime($request->start_time));
        // exit();
        $data = Tbl_Schedule_Visit::where([['id', '=', $request->visitId]])->update(['visit_date' => date('Y-m-d', strtotime($request->visit_date)), 'start_time' => date('H:i:s', strtotime($request->start_time)), 'end_time' => date('H:i:s', strtotime($request->end_time)), 'agenda' => $request->agenda]);
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
        $data = Tbl_Schedule_Visit::where([['id', '=', $request->visitId]])->update(['Inactive' => "2"]);
        if($data) {
            return response()->json(["status" => 1, "msg" => "Delete successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Delete faild."]);
        }
    }
    public function save_take_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int',
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
        $data = Tbl_Distributor_Note::create([
            'fk_company_id' => $fk_company_id,
            'fk_distributor_id' => $request->distributor_id,
            'fk_user_id' => $request->user_id,
            'note' => $request->note,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '10',
                'fk_distributor_id' => $request->distributor_id,
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
    public function gate_take_note(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $query = DB::table('tbl_distributor_note as n');
        $query->select('n.id', 'n.note', 'n.created_at', 'u.name as user_name', 'r.role');
        $query->join('tbl_user_master as u', 'u.id', '=', 'n.fk_user_id', 'left');
        $query->join('tbl_role as r', 'r.id', '=', 'u.fk_role_id', 'left');
        $query->where([['n.fk_distributor_id', $request->get('distributor_id')],['n.fk_user_id', $request->get('user_id')], ['n.Inactive', "1"]]);
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
    public function distributor_note_by_id(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_data = Tbl_Distributor_Note::select('note')->where([['id', '=', $request->noteId], ['Inactive', '=', '1']])->get()->toArray();
        if(sizeof($select_data) > 0) {
            return response()->json(["status" => 1, "data" => $select_data[0]['note']]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    public function update_take_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int',
            'note' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $update_data = Tbl_Distributor_Note::where([['id', '=', $request->noteId]])->update(['note' => $request->note]);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    public function delete_distributor_note(Request $request) {
        $validator = Validator::make($request->all(), [
            'noteId' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $update_data = Tbl_Distributor_Note::where([['id', '=', $request->noteId]])->update(['Inactive' => "2"]);
        if($update_data) {
            return response()->json(["status" => 1, "msg" => "Delete successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Delete faild."]);
        }
    }
    // Save Query
    public function save_distributor_query(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'distributor_id' => 'required|int',
            'query_txt' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $data = Tbl_Distributor_Query::create([
            'fk_company_id' => $fk_company_id,
            'fk_distributor_id' => $request->distributor_id,
            'query' => $request->query_txt,
            'created_by' => $request->user_id,
            'Inactive' => '1'
        ]);
        if($data) {
            $data2 = Tbl_Team_Activity::create([
                'fk_company_id' => $fk_company_id,
                'fk_activity_type_id' => '12',
                'fk_distributor_id' => $request->distributor_id,
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
}
