<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_User_Master;
use App\Tbl_Company_Master;
use App\Tbl_Enquiry;
use App\Tbl_Retailers;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class EnquiryController extends Controller {
    /* =====================
        Save Enquiry
    ===================== */
	public function save_enquery(Request $request) {
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
        $retailer_id = null;
        if(!empty($request->retailer_id)) {
        	$retailer_id = $request->retailer_id;
        }
        $distributor_id = null;
        if(!empty($request->distributor_id)) {
        	$distributor_id = $request->distributor_id;
        }
        $data = Tbl_Enquiry::create([
            'fk_company_id' => $fk_company_id,
            'fk_user_id' => $request->user_id,
            'fk_retailer_id' => $retailer_id,
            'fk_distributor_id' => $distributor_id,
            'remarks' => $request->remarks,
            'Inactive' => '1'
        ]);
        if($data) {
            // $data2 = Tbl_Team_Activity::create([
            //     'fk_company_id' => $fk_company_id,
            //     'fk_activity_type_id' => '10',
            //     'fk_retailers_id' => $request->retailer_id,
            //     'latitude' => $request->latitude,
            //     'longitude' => $request->longitude,
            //     'Inactive' => '1'
            // ]);
            // if($data2) {
            //     return response()->json(["status" => 1, "msg" => "Save successful."]);
            // }else {
            //     return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            // }
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
    /* =====================
        Single Retailer Save Enquiry
    ===================== */
    public function single_retailer_save_enquiry(Request $request) {
        $validator = Validator::make($request->all(), [
            'retailer_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_Retailers::select('fk_company_id')->where('id', $request->retailer_id)->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $retailer_id = null;
        if(!empty($request->retailer_id)) {
            $retailer_id = $request->retailer_id;
        }
        $distributor_id = null;
        if(!empty($request->distributor_id)) {
            $distributor_id = $request->distributor_id;
        }
        $data = Tbl_Enquiry::create([
            'fk_company_id' => $fk_company_id,
            //'fk_user_id' => $request->user_id,
            'fk_retailer_id' => $retailer_id,
            'fk_distributor_id' => $distributor_id,
            'remarks' => $request->remarks,
            'Inactive' => '1'
        ]);
        if($data) {
            // $data2 = Tbl_Team_Activity::create([
            //     'fk_company_id' => $fk_company_id,
            //     'fk_activity_type_id' => '10',
            //     'fk_retailers_id' => $request->retailer_id,
            //     'latitude' => $request->latitude,
            //     'longitude' => $request->longitude,
            //     'Inactive' => '1'
            // ]);
            // if($data2) {
            //     return response()->json(["status" => 1, "msg" => "Save successful."]);
            // }else {
            //     return response()->json(["status" => 0, "msg" => "Something is wrong."]);
            // }
            return response()->json(["status" => 1, "msg" => "Save successful."]);
        }else {
            return response()->json(["status" => 0, "msg" => "Save faild."]);
        }
    }
}