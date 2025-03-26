<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\ProductCategories;
use App\Suppliers;
use App\Brand;
use App\ProductSubCategory;
use App\Oem;
use App\CarManufacture;
use App\PartBrand;
use DB;
use DataTables;
use Cookie;
use App\WmsUnit;
use App\PartName;

class ItemListController extends Controller {

    // public function item_list() {
    // 	$product_categories=ProductCategories::get();
    //     return \View::make("backend/item_list/item_list")->with(array('product_categories'=>$product_categories));
    // }
    public function item_list(){
        return \View::make("backend/item_list/item_list")->with([
            'car_manufacture' => CarManufacture::select('car_manufacture_id','car_manufacture')->where('status', 1)->orderBy('car_manufacture_id', 'desc')->get()->toArray(),
            'car_model' => Brand::select('brand_id', 'brand_name')->where('status', 1)->orderBy('brand_id', 'desc')->get()->toArray(),
            'ProductCategories' => ProductCategories::select('category_id', 'category_name')->where('status',0)->orderBy('category_id', 'desc')->get()->toArray(),
            'PartName' => PartName::select('part_name_id', 'part_name')->where('status',1)->orderBy('part_name_id', 'desc')->get()->toArray(),
            'PartBrand' => PartBrand::select('part_brand_id','part_brand_name')->where('status', 1)->orderBy('part_brand_id', 'desc')->get()->toArray(),
        ])->render();
    }
    public function get_category(Request $request){
        $model_id = $request->model_id;
        $category =  ProductCategories::where([['brand_id', '=',$model_id],['status','=', '0']])->get()->toArray();
        return response()->json($category);
    }
    public function get_sub_category(Request $request){
        $category_id = $request->category_id;
        $subcategory =  ProductSubCategory::where([['category_id', '=',$category_id],['status','=', '1']])->get()->toArray();
        return response()->json($subcategory);
    }
     public function get_oem_no_list(Request $request){
        $sub_category_id = $request->sub_category_id;
        $oem_no =  Oem::where([['sub_category_id', '=',$sub_category_id],['status','=', '1']])->get()->toArray();
        return response()->json($oem_no);
    }
    public function get_item_list(Request $request){
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('products as p');
            $query->select('p.*', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            if($keyword) {
                $sql = "pn.part_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('pn.part_name', 'asc');
                else
                    $query->orderBy('p.product_id', 'desc');
            }else {
                $query->orderBy('p.product_id', 'DESC');
            }
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
            if(!empty($request->product_name)) {
                $query->where([['p.part_name_id', '=', $request->product_name]]);
            }
            if(!empty($request->part_no)) {
                $query->where('p.pmpno', 'like', '%' . $request->part_no . '%');
            }
            if(!empty($request->filter_part_brand)) {
                $query->where([['p.part_brand_id', '=', $request->filter_part_brand]]);
            }
            // if(!empty($request->part_no)) {
            //     $query->where('products.pmpno', 'like', '%' . $request->part_no . '%');
            // }
            // if(!empty($request->product_name)) {
            //     $query->where([['part_name.part_name_id', '=', $request->product_name]]);
            // }
            // if(!empty($request->category_id)) {
            //     $query->where('products.ct', '=', $request->category_id );
            // }
            // if(!empty($request->model_id)) {
            //     $query->where('products.brn', '=', $request->model_id );
            // }
            // if(!empty($request->sub_category_id)) {
            //     $query->where('products.sct', '=', $request->sub_category_id );
            // }
            // if($order) {
            //     if($order == "asc")
            //         $query->orderBy('product_id', 'asc');
            //     else
            //         $query->orderBy('product_id', 'desc');
            // }else {
            //     $query->orderBy('product_id', 'DESC');
            // }
            // $query->orderBy('product_id', 'DESC');
            // $query->get();
            $query->where([['p.is_deleted', '=', '0']]);
            $datatable_array=Datatables::of($query)
                ->addColumn('product_id', function ($query) {
                    $product_id = '';
                    if(!empty($query->product_id)) {
                        $product_id .= $query->product_id;
                    }
                    return $product_id;
                })
                ->addColumn('part_no', function ($query) {
                    $part_no = '';
                    if(!empty($query->pmpno)) {
                        $part_no .= $query->pmpno;
                    }
                    return $part_no;
                })
                ->addColumn('name', function ($query) {
                    $product_name = '';
                    if(!empty($query->part_name)) {
                        $product_name .= $query->part_name;
                    }
                    return $product_name;
                })
                ->addColumn('supplier', function ($query) {
                    $supplier_name = '';
                    if(!empty($query->supplier_id)) {
                        $Suppliers = Suppliers::select('full_name')->where([['supplier_id', '=', $query->supplier_id], ['status', '=', '1']])->get()->toArray();
                        if(sizeof($Suppliers) > 0) {
                            if(!empty($Suppliers[0]['full_name'])) $supplier_name = $Suppliers[0]['full_name'];
                        }
                    }
                    return $supplier_name;
                })
                ->addColumn('unit', function ($query) {
                    $unit = '';
                    if(!empty($query->unit)) {
                        $get_unit_name = WmsUnit::where('unit_id',$query->unit)->get()->toArray();
                        if(sizeof($get_unit_name)>0)
                        $unit = $get_unit_name[0]['unit_name'];
                    }
                    return $unit;
                })
                ->addColumn('category', function ($query) {
                    $category_name = '';
                    if(!empty($query->ct)) {
                        $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $query->ct]])->get()->toArray();
                        if(!empty($ProductCategories)) {
                            if(!empty($ProductCategories[0]['category_name'])) {
                                $category_name = $ProductCategories[0]['category_name'];
                            }
                        }
                    }
                    return $category_name;
                })
                ->addColumn('inventory', function ($query) {
                    $model = new DB;
                    $available_stock = available_stock($model,$query->product_id);
                    return $available_stock;
                })
                ->addColumn('add_to_cart', function ($query) {
                    $current_stock = $query->current_stock;
                    $actions = '';
                    $actions .= '<input type="number" class="form-control qty" style="width: 73px;display:inline-block;height: 42px;margin-top: 0px;position: relative;top: 2px;" min="1"> <a data-product-id="' . $query->product_id . '" href="javascript:void(0);" data-qty="' .$current_stock. '" name="button" class="view-subbrand btn btn-success action-btn add_to_cart" title="Add to cart"><i class="mdi mdi-cart" aria-hidden="true"></i></a>';
                    return $actions;
                })
                ->rawColumns(['product_id', 'name', 'supplier', 'unit', 'category', 'inventory', 'add_to_cart'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    public function add_product_form() {
        return \View::make("backend/item_list/add_product_form")->with([
            'product_categories' => ProductCategories::get()->toArray(),
            'suppliers' => Suppliers::get()->toArray()
        ]);
    }

    public function export_item_csv(Request $request){
    	$product_name=$request->product_name;
    	$category=$request->category;
    	$file_name = 'student_details_on_'.date('Ymd').'.csv'; 
	    header("Content-Description: File Transfer"); 
	    header("Content-Disposition: attachment; filename=$file_name"); 
	    header("Content-Type: application/csv;");
	   
	     // get data 
	     $query = DB::table('products');
        $query->join('suppliers', 'products.supplier_id', '=', 'suppliers.supplier_id');
        $query->join('product_categories', 'products.ct', '=', 'product_categories.category_id');
        //$query->join('warehouses', 'products.warehouse_id', '=', 'warehouses.warehouse_id');
        $query->select('products.product_id', 'products.part_name','suppliers.full_name as supplier_name','products.unit','products.pmrprc','products.current_stock','product_categories.category_name');
        
        if(!empty($product_name)) {
            $query->where('products.part_name', 'like', '%' . $product_name . '%');
        }
        if(!empty($category)) {
            $query->where('products.ct', '=', $category );
        }
        $query->orderBy('product_id', 'DESC');
        $data=json_decode(json_encode($query->get()->toArray()),TRUE);
        $data_array=[];
        foreach($data as $val){
            $used_stock_query=DB::table('sale_order_details');
            $used_stock_query->select(DB::raw("SUM(qty) as total_qty"));
            $used_stock_query->where('sale_order_details.product_id', '=', $val['product_id']);
            $used_data=json_decode(json_encode($used_stock_query->get()->toArray()),TRUE);
            if(sizeof($used_data)>0)
            {
                $current_stock = $val['current_stock']-$used_data[0]['total_qty'];
            }
            else
            {
                $current_stock = $val['current_stock'];
            }
            $data_array[]=array('product_id'=>$val['product_id'],'name'=>$val['part_name'],'supplier'=>$val['supplier_name'],'unit'=>$val['unit'],'price'=>$val['pmrprc'],'qty'=>$current_stock,'category'=>$val['category_name']);
        }
	     //file creation 
	     $file = fopen('php://output', 'w');
	 
	     $header = array("Product ID","Name", "Supplier", "Unit", "Price", "Qty", "Category"); 
	     fputcsv($file, $header);
	     foreach ($data_array as $key => $value)
	     { 
	       fputcsv($file, $value); 
	     }
	     fclose($file); 
	     exit; 
    }
    
    public function add_to_cart(Request $request){
      $cart_data = $request->cookie('cart_data');
      $cart_item=[];
      if($cart_data)
      {
        $data=json_decode($cart_data,true);
        if(sizeof($data)>0)
        {
            foreach($data as $key=>$val)
            {
                if($val['product_id'] == $request->product_id)
                {
                    $returnData = ["status" => 0, "msg" => "Product is already in your cart"];
                    return response()->json($returnData);
                }
                else
                {
                    $data[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
                }
            }
            $cart_item = $data;
        }
        else
        {
            $cart_item[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
        }
      }
      else
      {
        $cart_item[]=array('qty'=>$request->qty,'product_id'=>$request->product_id);
      }
      $minutes = 60*24;
      $cookie = cookie('cart_data', json_encode($cart_item), $minutes);
      return response(json_encode(array('status'=>1)))->cookie($cookie);
    }
}