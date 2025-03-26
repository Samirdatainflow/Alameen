<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use DB;
use DataTables;
use App\Brand;
use App\ProductCategories;
use App\ProductSubCategory;
use App\Oem;
use App\Products;
use App\ApplicationNo;
use App\ManufacturingNo;
use App\AlternatePartNo;
use App\PartName;
use App\CarManufacture;
use App\PartBrand;

class ItemSearchController extends Controller {

    public function index() {
        return \View::make("backend/item/item_search")->with([
            'CarManufacture' => CarManufacture::select('car_manufacture_id', 'car_manufacture')->where([['status', '=', '1']])->orderBy('car_manufacture_id', 'desc')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where([['status', '=', '1']])->orderBy('part_name_id', 'desc')->get()->toArray(),
            'PartBrand' => PartBrand::select('part_brand_id', 'part_brand_name')->where([['status', '=', '1']])->orderBy('part_brand_id', 'desc')->get()->toArray(),
            'Brand' => Brand::select('brand_id', 'brand_name')->where([['status', '=', '1']])->orderBy('brand_id', 'desc')->get()->toArray(),
        	'ProductCategories' => ProductCategories::select('category_id', 'category_name')->where([['status', '=', '0']])->orderBy('category_id', 'desc')->get()->toArray(),
        ]);
    }
    // Get Category By Model
    public function get_category_by_model(Request $request) {
    	if ($request->ajax()) {
    		$ProductCategories = ProductCategories::select('category_id', 'category_name')->where([['brand_id', '=', $request->id], ['status', '=', '0']])->get()->toArray();
    		if(sizeof($ProductCategories) > 0) {
    			return response()->json(['status' => 1, 'data' => $ProductCategories]);
    		}else {
    			return response()->json(['status' => 0, 'msg' => 'No record found.']);
    		}
    	}
    }
    // Get Sub Category By Category
    public function get_subcategory_by_category(Request $request) {
    	if ($request->ajax()) {
    		$ProductSubCategory = ProductSubCategory::select('sub_category_id', 'sub_category_name')->where([['category_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
    		if(sizeof($ProductSubCategory) > 0) {
    			return response()->json(['status' => 1, 'data' => $ProductSubCategory]);
    		}else {
    			return response()->json(['status' => 0, 'msg' => 'No record found.']);
    		}
    	}
    }
    //
    public function get_oem_no_by_sub_category(Request $request) {
    	if ($request->ajax()) {
    		$Oem = Oem::select('oem_id', 'oem_no')->where([['sub_category_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
    		if(sizeof($Oem) > 0) {
    			return response()->json(['status' => 1, 'data' => $Oem]);
    		}else {
    			return response()->json(['status' => 0, 'msg' => 'No record found.']);
    		}
    	}
    }
    public function get_search_item_details(Request $request) {
    	if ($request->ajax()) {
            DB::enableQueryLog();
            $returnData = [];
            $page = $request->has('page') ? $request->post('page') : 1;
            $limit = $request->has('limit') ? $request->post('limit') : 50;
            $query2 = DB::table('products');
            $query2->select('*');
            $query2->where([['ct', '=',$request->hidden_ct], ['sct', '=',$request->sub_category_id], ['is_deleted','=', '0']]);
            if(!empty($request->hidden_car_manufacture)) {
                $query2->where([['car_manufacture_id', '=', $request->hidden_car_manufacture]]);
            }
            if(!empty($request->hidden_from_year) && !empty($request->hidden_from_month)) {
            	$query2->where(DB::raw("CONCAT(from_year,'-',from_month)"), '>=', $request->hidden_from_year.'-'.$request->hidden_from_month);
            }
            if(!empty($request->hidden_to_year) && !empty($request->hidden_to_month)) {
            	$query2->where(DB::raw("CONCAT(to_year,'-',to_month)"), '<=', $request->hidden_to_year.'-'.$request->hidden_to_month);
            }
            if(!empty($request->hidden_model)) {
                $query2->whereRaw('FIND_IN_SET('.$request->hidden_model.', car_model)');
            }
            $query2->limit($limit)->offset(($page - 1) * $limit);
            $Products = $query2->get()->toArray();
            if(sizeof($Products) > 0) {
                foreach($Products as $data) {
                    // $application = [];
                    // $ApplicationNo = ApplicationNo::select('application_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($ApplicationNo) > 0) {
                    //     $application = $ApplicationNo;
                    // }
                    $manufacturing_no = [];
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($ManufacturingNo) > 0) {
                        $manufacturing_no = $ManufacturingNo;
                    }
                    $alternate_no = [];
                    $AlternatePartNo = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($AlternatePartNo) > 0) {
                        $alternate_no = $AlternatePartNo;
                    }
                    $part_name = "";
                    $PartName = PartName::select('part_name')->where([['part_name_id', '=', $data->part_name_id]])->get()->toArray();
                    if(sizeof($PartName) > 0) {
                        if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                    }
                    array_push($returnData, array('product_id' => $data->product_id, 'part_name' => $part_name, 'pmpno' => $data->pmpno, 'pmrprc' => $data->pmrprc, 'manufacturing_no' => $manufacturing_no, 'alternate_no' => $alternate_no));
                }
            }
            $html = view("backend/item/view_search_items")->with([
                'product_data' => $returnData
            ])->render();
            return response()->json(["status" => 1, "message" => $html, 'total_row' => sizeof($Products), 'select_row' => sizeof($Products)]);
        }
    }
    public function filter_item_search(Request $request) {
    	if ($request->ajax()) {
    		$returnData = [];
    		$query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno', 'p.pmrprc', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->where([['p.ct', '=',$request->hidden_ct], ['p.sct', '=',$request->hidden_sct], ['p.is_deleted','=', '0']]);
            if(!empty($request->filter_val)) {
                $query->whereRaw('(p.pmpno LIKE ? or pn.part_name LIKE ?)', array('%' . $request->filter_val . '%','%' . $request->filter_val . '%'));
            }
            if(!empty($request->hidden_car_manufacture)) {
                $query->where([['p.car_manufacture_id', '=', $request->hidden_car_manufacture]]);
            }
            if(!empty($request->hidden_model)) {
                $query->whereRaw('FIND_IN_SET('.$request->hidden_model.', p.car_model)');
            }
            if(!empty($request->hidden_from_year)) {
                $query->where([['p.from_year', '>=', $request->hidden_from_year]]);
            }
            if(!empty($request->hidden_from_month)) {
                $query->where([['p.from_month', '>=', $request->hidden_from_month]]);
            }
            if(!empty($request->hidden_to_year)) {
                $query->where([['p.to_year', '<=', $request->hidden_to_year]]);
            }
            if(!empty($request->hidden_to_month)) {
                $query->where([['p.to_month', '<=', $request->hidden_to_month]]);
            }
    		$Products = $query->limit('50')->get()->toArray();
    		if(sizeof($Products) > 0) {
                foreach($Products as $data) {
                    // $application = [];
                    // $ApplicationNo = ApplicationNo::select('application_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    // if(sizeof($ApplicationNo) > 0) {
                    //     $application = $ApplicationNo;
                    // }
                    $manufacturing_no = [];
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($ManufacturingNo) > 0) {
                        $manufacturing_no = $ManufacturingNo;
                    }
                    $alternate_no = [];
                    $AlternatePartNo = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($AlternatePartNo) > 0) {
                        $alternate_no = $AlternatePartNo;
                    }
                    array_push($returnData, array('product_id' => $data->product_id, 'part_name' => $data->part_name, 'pmpno' => $data->pmpno, 'pmrprc' => $data->pmrprc, 'manufacturing_no' => $manufacturing_no, 'alternate_no' => $alternate_no));
                }
            }
            $html = view("backend/item/view_search_items")->with([
                'product_data' => $returnData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
    	}
    }
    // Cart set in Cookie
    public function add_to_cart(Request $request){
        $cart_data = $request->cookie('cart_data');
        $cart_item=[];
        if($cart_data) {
            $data=json_decode($cart_data,true);
            if(sizeof($data)>0) {
                foreach($data as $key=>$val) {
                    if($val['product_id'] == $request->product_id) {
                        $returnData = ["status" => 0, "msg" => "Product is already in your cart"];
                        return response()->json($returnData);
                    }else {
                        $data[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
                    }
                }
                $cart_item = $data;
            }else {
                $cart_item[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
            }
        }else {
            $cart_item[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
        }
        $minutes = 60*24;
        $cookie = cookie('cart_data', json_encode($cart_item), $minutes);
        return response(json_encode(array('status'=>1)))->cookie($cookie);
    }
}