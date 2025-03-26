<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use Validator;
use App\Tbl_Attendance;
use App\Tbl_User_Master;
//use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class AttendanceController extends Controller
{
    public function save_checkin_checkout(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $fk_company_id = "";
        $select_company = Tbl_User_Master::select('fk_company_id')->where('id', $request->get('user_id'))->get();
        if(count($select_company) > 0) {
            $fk_company_id = $select_company[0]['fk_company_id'];
        }
        $select_attendance = Tbl_Attendance::where([['fk_user_id', '=', $request->user_id]])->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), "=", date('Y-m-d'))->orderBy('id', 'DESC')->limit('1')->get();
        if(sizeof($select_attendance) > 0) {
            if($select_attendance[0]->check_out == "") {
                $update_data = Tbl_Attendance::where([['id', '=', $select_attendance[0]->id]])->update(['check_out' => date('H:i:s'), 'latitude' => $request->latitude, 'longitude' => $request->longitude]);
                if($update_data) {
                    $listData = [];
                    $listData = $this->listCheckinCheckout($request->user_id);
                    return response()->json(["status" => 1, "msg" => "Check out done.", 'data' => $listData, 'position' => '1']);
                }else {
                    return response()->json(["status" => 0, "msg" => "Check out faild."]);
                }
            }else {
                $data = Tbl_Attendance::create([
                    'fk_company_id' => $fk_company_id,
                    'fk_user_id' => $request->user_id,
                    'check_in' => date('H:i:s'),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
                if($data) {
                    $listData = [];
                    $listData = $this->listCheckinCheckout($request->user_id);
                    return response()->json(["status" => 1, "msg" => "Check in done.", 'data' => $listData, 'position' => '0']);
                }else {
                    return response()->json(["status" => 0, "msg" => "Check in faild."]);
                }
            }
        }else {
            $data = Tbl_Attendance::create([
                'fk_company_id' => $fk_company_id,
                'fk_user_id' => $request->user_id,
                'check_in' => date('H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            if($data) {
                $listData = [];
                $listData = $this->listCheckinCheckout($request->user_id);
                return response()->json(["status" => 1, "msg" => "Check in done.", 'data' => $listData, 'position' => '0']);
            }else {
                return response()->json(["status" => 0, "msg" => "Check in faild."]);
            }
        }
        // $data2 = Tbl_Team_Activity::create([
        //     'fk_company_id' => $fk_company_id,
        //     'fk_activity_type_id' => '5',
        //     'fk_distributor_id' => $request->distributor_id,
        //     'created_by' => $request->user_id,
        //     'latitude' => $request->latitude,
        //     'longitude' => $request->longitude,
        //     'Inactive' => '1'
        // ]);
    }
    public function checkin_checkout_position(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $select_attendance = Tbl_Attendance::where([['fk_user_id', '=', $request->user_id]])->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), "=", date('Y-m-d'))->orderBy('id', 'DESC')->limit('1')->get();
        if(sizeof($select_attendance) > 0) {
            if($select_attendance[0]->check_out == "") {
                return response()->json(["status" => 0]);
            }else {
                return response()->json(["status" => 1]);
            }
        }else {
            return response()->json(["status" => 1]);
        }
    }
    public function list_checkin_checkout(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $select_attendance = Tbl_Attendance::where([['fk_user_id', '=', $request->user_id]])->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), "=", date('Y-m-d'))->orderBy('id', 'DESC')->get();
        if(sizeof($select_attendance) > 0) {
            $i= 1;
            foreach($select_attendance as $adata) {
                $check_out = "";
                if(!empty($adata->check_out)) {
                    $check_out = date('h:i A', strtotime($adata->check_out));
                }
                array_push($returnData, array('sl' => $i, 'check_in' => date('h:i A', strtotime($adata->check_in)), 'check_out' => $check_out));
                $i++;
            }
            return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
            return response()->json(["status" => 0, 'data' => "No record found"]);
        }
    }
    public function listCheckinCheckout($user_id) {
        $returnData = [];
        $select_attendance = Tbl_Attendance::where([['fk_user_id', '=', $user_id]])->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), "=", date('Y-m-d'))->orderBy('id', 'DESC')->get();
        if(sizeof($select_attendance) > 0) {
            $i= 1;
            foreach($select_attendance as $adata) {
                $check_out = "";
                if(!empty($adata->check_out)) {
                    $check_out = date('h:i A', strtotime($adata->check_out));
                }
                array_push($returnData, array('sl' => $i, 'check_in' => date('h:i A', strtotime($adata->check_in)), 'check_out' => $check_out));
                $i++;
            }
        }
        return $returnData;
    }
}
