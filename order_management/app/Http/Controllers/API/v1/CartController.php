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
use App\SaleOrderDetails;
//use Hash;
//use JWTAuth;
//use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class CartController extends Controller
{
	public function cart_item_details(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'cart_data' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $return_data=[];
        $data_array = $request->cart_data;
        for($i=0;$i<sizeof($data_array);$i++) {
            $return_data[]=$this->product_details_by_part_no($data_array[$i]['part_no'],$data_array[$i]['qty']);
        }
        return response()->json(array('status'=>1,'data'=>$return_data));
    }
    public function product_details_by_part_no($part_no,$qty){
        $data_array=[];
        $query = DB::table('products');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct');
        $query->join('wms_units as u', 'u.unit_id', '=', 'products.unit', 'left');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name','u.unit_name' );
        $query->where('products.pmpno', '=', $part_no);
        $data=$query->get()->toArray();
        if(sizeof($data)>0) {
            $model = new DB;
            $available_stock = available_stock_by_part_no($model,$part_no);
            $data_array=array('product_id'=>$data[0]->product_id,'part_no'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>round($data[0]->pmrprc,2),'current_stock'=>$available_stock,'qty'=>$qty,'unit_name'=>$data[0]->unit_name);
        }
        return $data_array;
    }
    public function submit_cart_order(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'cart_data' =>'required'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $data_array = $request->cart_data;
        $sub_total = 0;
        for($i=0;$i<sizeof($data_array);$i++) {
            $sub_total +=($data_array[$i]['pmrprc']*$data_array[$i]['qty']);
        }
        $order_data = array('client_id'=>$request->user_id,'sub_total'=>$sub_total,'gst'=>0,'grand_total'=>($sub_total+0),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'));
        $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
        for($i=0;$i<sizeof($data_array);$i++) {
            $product_tax = 0;
            $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
            $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$data_array[$i]['product_id'],'product_tax'=>0,'product_price'=>$data_array[$i]['pmrprc'],'qty'=>$data_array[$i]['qty']);
            SaleOrderDetails::insert($order_details);
        }
        $returnData = array('status' => 1, 'msg' => "Order is created successfully");
        return response()->json($returnData);
    }
}