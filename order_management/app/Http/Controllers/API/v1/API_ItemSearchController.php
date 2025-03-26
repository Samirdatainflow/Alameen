<?php

namespace App\Http\Controllers\API\v1;

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Products;
use App\PartName;
use App\ManufacturingNo;
use App\AlternatePartNo;
use DB;

class API_ItemSearchController extends Controller
{
	public function item_search(Request $request) {
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'car_manufacture_id' => 'required|int',
            'car_model_id' => 'required|int',
            'category_id' => 'required|int',
            'sub_category_id' => 'required|int',
            'page' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        if($request->page == 1) {
        	$limit = 10;
        	$offset = 0;
        }else if($request->page == 2) {
        	$limit = 10;
        	$offset = 10;
        }else {
	        $limit = 10;
	        $offset = 10 * ($request->page - 1);
	    }
        $query = DB::table('products as p');
        $query->select('p.product_id', 'p.pmpno', 'pn.part_name', 'p.pmrprc', 'mn.manufacturing_no', 'an.alternate_no');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->join('manufacturing_no as mn', 'mn.product_id', '=', 'p.product_id', 'left');
        $query->join('alternate_part_no as an', 'an.product_id', '=', 'p.product_id', 'left');
        $query->orderBy('p.product_id', 'desc');
        $query->where([['p.is_deleted', '=', '0']]);
        if(!empty($request->car_manufacture_id)) {
            $query->where([['car_manufacture_id', '=', $request->car_manufacture_id]]);
        }
        if(!empty($request->hidden_from_year)) {
            $query->where([['from_year', '>=', $request->hidden_from_year]]);
        }
        if(!empty($request->hidden_from_month)) {
            $query->where([['from_month', '>=', $request->hidden_from_month]]);
        }
        if(!empty($request->hidden_to_year)) {
            $query->where([['to_year', '<=', $request->hidden_to_year]]);
        }
        if(!empty($request->hidden_to_month)) {
            $query->where([['to_month', '<=', $request->hidden_to_month]]);
        }
        if(!empty($request->hidden_model)) {
            $query->whereRaw('FIND_IN_SET('.$request->hidden_model.', car_model)');
        }
        $query->limit($limit);
        $query->offset($offset);
        $ListData = $query->get()->toArray();
        if(sizeof($ListData) > 0) {
        	return response()->json(["status" => 1, 'data' => $ListData]);
        }else {
        	return response()->json(["status" => 1, 'msg' => "No record found.",'data'=>[]]);
        }
	}
}