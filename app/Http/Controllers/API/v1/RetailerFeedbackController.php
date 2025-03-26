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
use App\Tbl_Retailer_Feedback;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use DateTime;

class RetailerFeedbackController extends Controller
{
	public function get_retailer_feedback(Request $request) {
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
        $returnData = [];
        $query = DB::table('tbl_retailer_feedback as f');
        $query->select('f.id as f_id', 'f.remarks', 'f.created_at', 'f.photo', 'f.feedback_type', 'p.purpose_name', 'ps.sku_type', 'u.name as user_name');
        $query->join('tbl_retailer_feedback_purpose as p', 'p.id', '=', 'f.fk_purpose_id', 'left');
        $query->join('tbl_product_skus as ps', 'ps.id', '=', 'f.fk_sku_id', 'left');
        $query->join('tbl_user_master as u', 'u.id', '=', 'f.created_by', 'left');
        $query->where([['f.Inactive', '!=', '2'], ['f.fk_company_id', '=', $fk_company_id], ['f.fk_retailer_id', '=', $request->retailer_id]]);
        $select_data = $query->get();
        //print_r($select_data); exit();
        if(count($select_data) > 0) {
        	foreach($select_data as $data) {
                $user_name = "";
                if(!empty($data->user_name)) {
                    $user_name = $data->user_name;
                }
                $photo = "";
                if(!empty($data->photo)) {
                    $photo = url('/')."/public/retailers/images/feedback/".$data->photo;
                }
	        	array_push($returnData, array('id' => $data->f_id, 'remarks' => $data->remarks, 'purpose_name' => $data->purpose_name, 'feedback_type' => $data->feedback_type, 'sku_type' => $data->sku_type, 'date' => date('h:i A, d M Y', strtotime($data->created_at)), 'user_name' => $user_name, 'photo' => $photo));
	        }
	        return response()->json(["status" => 1, "data" => $returnData]);
        }else {
        	return response()->json(["status" => 0, "msg" => "No record found."]);
        }
	}
    /* ======================
        Feedback Delete
    ====================== */
    public function delete_retailer_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'feedback_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $updateData = Tbl_Retailer_Feedback::where([['id', '=', $request->feedback_id]])->update(['Inactive' => '2']);
        if($updateData) {
            return response()->json(["status" => 1, "msg" => "Delete successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Delete faild."]);
        }
    }
    /* ======================
        Get Feedback Details
    ====================== */
    public function get_retailer_feedback_details(Request $request) {
        $validator = Validator::make($request->all(), [
            'feedback_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $selectData = Tbl_Retailer_Feedback::where([['id', '=', $request->feedback_id]])->get()->toArray();
        if(count($selectData) > 0) {
            foreach($selectData as $data) {
                array_push($returnData, array('id' => $data['id'], 'fk_purpose_id' => $data['fk_purpose_id'], 'feedback_type' => $data['feedback_type'], 'fk_sku_id' => $data['fk_sku_id'], 'remarks' => $data['remarks'], 'photo' => $data['photo']));
            }
            return response()->json(["status" => 1, "data" => $returnData]);
        }else {
            return response()->json(["status" => 0, "msg" => "No record found."]);
        }
    }
    /* ======================
        Update Feedback Details
    ====================== */
    public function update_retailer_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'feedback_id' => 'required|int',
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
        $data = Tbl_Retailer_Feedback::where([['id', $request->feedback_id]])->update([
            'fk_purpose_id' => $request->fk_purpose_id,
            'feedback_type' => $request->feedback_type,
            'fk_sku_id' => $request->fk_sku_id,
            'remarks' => $request->remarks,
            'photo' => $request->photo
        ]);
        if($data) {
            return response()->json(["status" => 1, "msg" => "Update successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Something is wrong."]);
        }
    }
}