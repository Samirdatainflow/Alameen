<?php

namespace App\Http\Controllers\API\v1;

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\CarManufacture;
use App\Brand;
use App\ProductCategories;
use App\ProductSubCategory;
use App\PartName;
use App\PartBrand;
use DB;

class CommonController extends Controller
{
	// Car Manufacture
	public function car_manufacture_list(Request $request) {
		$returnData = [];
        $CarManufacture = CarManufacture::where([['status', '=', '1']])->orderBy('car_manufacture_id', 'desc')->get()->toArray();
        if(sizeof($CarManufacture) >0) {
        	foreach($CarManufacture as $manufacture) {
        		array_push($returnData, array('label' => $manufacture['car_manufacture'], 'value' => $manufacture['car_manufacture_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
	// Car Model
	public function car_model_list(Request $request) {
		$returnData = [];
        $Brand = Brand::where([['status', '=', '1']])->orderBy('brand_id', 'desc')->get()->toArray();
        if(sizeof($Brand) >0) {
        	foreach($Brand as $brand_data) {
        		array_push($returnData, array('label' => $brand_data['brand_name'], 'value' => $brand_data['brand_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
	// Product Category
	public function category_list(Request $request) {
		$returnData = [];
        $ProductCategories = ProductCategories::where([['status', '=', '0']])->orderBy('category_id', 'desc')->get()->toArray();
        if(sizeof($ProductCategories) >0) {
        	foreach($ProductCategories as $category) {
        		array_push($returnData, array('label' => $category['category_name'], 'value' => $category['category_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
	// Product Sub Category
	public function sub_category_list(Request $request) {
		$validator = Validator::make($request->all(), [
            'category_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
		$returnData = [];
        $ProductSubCategory = ProductSubCategory::where([['category_id', '=', $request->category_id]])->orderBy('sub_category_id', 'desc')->get()->toArray();
        if(sizeof($ProductSubCategory) >0) {
        	foreach($ProductSubCategory as $subcategory) {
        		array_push($returnData, array('label' => $subcategory['sub_category_name'], 'value' => $subcategory['sub_category_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
	// Part Name
	public function part_name_list(Request $request) {
		$returnData = [];
        $PartName = PartName::where([['status', '=', "1"]])->orderBy('part_name_id', 'desc')->get()->toArray();
        if(sizeof($PartName) >0) {
        	foreach($PartName as $pname) {
        		array_push($returnData, array('label' => $pname['part_name'], 'value' => $pname['part_name_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
	// Part Brand
	public function part_brand_list(Request $request) {
		$returnData = [];
        $PartBrand = PartBrand::where([['status', '=', "1"]])->orderBy('part_brand_id', 'desc')->get()->toArray();
        if(sizeof($PartBrand) >0) {
        	foreach($PartBrand as $pbrand) {
        		array_push($returnData, array('label' => $pbrand['part_brand_name'], 'value' => $pbrand['part_brand_id']));
        	}
        	return response()->json(["status" => 1, 'data' => $returnData]);
        }else {
	        return response()->json(["status" => 0, 'msg' => "No record found."]);
	    }
	}
}