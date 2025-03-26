<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use DB;
use DataTables;
use App\Helpers\Helper;
use App\Brand;

class CommonController extends Controller {

    public function get_model_by_model_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::model_list_by_model_name($request->search_key);
        }
        return response()->json($returnData);
    }
    public function get_car_model_by_car_manufacture(Request $request) {
        $returnData = [];
        $arrayData = [];
        $modelDatas = Brand::where([['status', '=', '1'], ['car_manufacture_id', '=', $request->id]])->select('brand_id', 'brand_name')->get()->toArray();
        if(sizeof($modelDatas) > 0) {
            foreach($modelDatas as $data) {
                array_push($arrayData, array('brand_id' => $data['brand_id'], 'brand_name' => $data['brand_name']));
            }
            $returnData = ["status" => 1, "data"=>$arrayData];
        }else {
            $returnData = ["status" => 0, "msg" => " No records found."];
        }
        return $returnData;
    }
}