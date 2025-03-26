<?php

namespace App\Http\Controllers\API\v1;

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Products;
use App\PartName;
use App\CarManufacture;
use App\Suppliers;
use App\WmsUnit;
use App\ProductCategories;
//use Hash;
//use JWTAuth;
//use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class ItemListController extends Controller
{
	public function item_list(Request $request) {
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'page' => 'required|int',
            'no_of_row' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        if($request->page == 1) {
        	$limit = $request->no_of_row;
        	$offset = 0;
        }else if($request->page == 2) {
        	$limit = $request->no_of_row;
        	$offset = 10;
        }else {
	        $limit = $request->no_of_row;
	        $offset = 10 * ($request->page - 1);
	    }
        $query = DB::table('products as p');
        $query->select('p.product_id', 'p.pmpno', 'pn.part_name', 's.full_name as supplier', 'u.unit_name', 'pc.category_name');
        $query->addSelect(DB::raw("'' as qty"));
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->join('suppliers as s', 's.supplier_id', '=', 'p.supplier_id', 'left');
        $query->join('wms_units as u', 'u.unit_id', '=', 'p.unit', 'left');
        $query->join('product_categories as pc', 'pc.category_id', '=', 'p.ct', 'left');
        $query->orderBy('p.product_id', 'desc');
        $query->where([['p.is_deleted', '=', '0']]);
        if(!empty($request->filter_car_manufacture)) {
            $query->where([['p.car_manufacture_id', '=', $request->filter_car_manufacture]]);
        }
        if(!empty($request->filter_car_model)) {
            $query->whereRaw("FIND_IN_SET('".$request->filter_car_model."',p.car_model)");
        }
        if(!empty($request->filter_from_year)) {
            $query->where([['p.from_year', '>=', $request->filter_from_year]]);
        }
        if(!empty($request->filter_from_month)) {
            $query->where([['p.from_month', '>=', $request->filter_from_month]]);
        }
        if(!empty($request->filter_to_year)) {
            $query->where([['p.to_year', '<=', $request->filter_to_year]]);
        }
        if(!empty($request->filter_to_month)) {
            $query->where([['p.to_month', '<=', $request->filter_to_month]]);
        }
        if(!empty($request->category_id)) {
            $query->where([['p.ct', '=', $request->category_id]]);
        }
        if(!empty($request->sub_category_id)) {
            $query->where([['p.sct', '=', $request->sub_category_id]]);
        }
        if(!empty($request->part_name_id)) {
            $query->where([['p.part_name_id', '=', $request->part_name_id]]);
        }
        if(!empty($request->part_no)) {
            $query->where('p.pmpno', 'like', '%' . $request->part_no . '%');
        }
        if(!empty($request->filter_part_brand)) {
            $query->where([['p.part_brand_id', '=', $request->filter_part_brand]]);
        }
        $query2 = $query->count();
        $query->limit($limit);
        $query->offset($offset);
        $ListData = $query->get()->toArray();
        if(sizeof($ListData) > 0) {
        	return response()->json(["status" => 1, 'data' => $ListData, 'total_row' => $query2]);
        }else {
        	return response()->json(["status" => 0, 'msg' => "No record found."]);
        }
	}
}