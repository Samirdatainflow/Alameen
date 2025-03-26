<?php

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
use App\SaleOrder;
use App\SaleOrderTemplate;
use App\WmsSaleOrderAproved;
use App\SaleOrderRejectReason;
use App\PackingDetails;
use DB;

class OrderController extends Controller {
    public function order_list(Request $request){
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
        $query = DB::table('sale_order as so');
        $query->select('so.sale_order_id', 'so.client_id', 'so.grand_total', 'so.discount', 'so.created_at', 'so.is_rejected', 'so.is_approved', 'c.customer_name', 'c.sponsor_name');
        $query->join('clients as c', 'so.client_id', '=', 'c.client_id', 'left');
        $query->where('so.client_id', '=', $request->user_id);
        $query->orderBy('sale_order_id', 'desc');
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
    public function order_approved_list(Request $request){
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
        $query = DB::table('sale_order as so');
        $query->select('so.sale_order_id', 'so.client_id', 'so.grand_total', 'so.discount', 'so.created_at', 'so.is_rejected', 'so.is_approved', 'c.customer_name', 'c.sponsor_name');
        $query->join('clients as c', 'so.client_id', '=', 'c.client_id', 'left');
        $query->where([['so.client_id', '=', $request->user_id], ['is_approved', '=', 1]]);
        $query->orderBy('sale_order_id', 'desc');
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
    public function order_reject_list(Request $request){
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
        $query = DB::table('sale_order as so');
        $query->select('so.sale_order_id', 'so.client_id', 'so.grand_total', 'so.discount', 'so.created_at', 'so.is_rejected', 'so.is_approved', 'c.customer_name', 'c.sponsor_name');
        $query->join('clients as c', 'so.client_id', '=', 'c.client_id', 'left');
        $query->where([['so.client_id', '=', $request->user_id], ['is_rejected', '=', 1]]);
        $query->orderBy('sale_order_id', 'desc');
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
    public function order_deliveries_list(Request $request){
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
        $query = DB::table('shipping as sh');
        $query->select('dm.delivery_management_id', 'dm.shipping_id', 'dm.sale_order_id', 'dm.order_date', 'dm.vehicle_no', 'dm.driver_name', 'dm.contact_no', 'dm.vehicle_in_out_date', 'cu.company_name', 'dm.courier_date', 'dm.courier_number', 'dm.no_of_box');
        $query->join('delivery_management as dm', 'dm.shipping_id', '=', 'sh.shipping_id', 'left');
        $query->join('courier_company as cu', 'cu.courier_company_id', '=', 'dm.courier_company_id', 'left');
        $query->where([['sh.client_id', '=', $request->user_id], ['sh.status', '=', 1]]);
        $query->orderBy('sh.sale_order_id', 'desc');
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
    public function pending_shipment_list(Request $request){
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
        $query = DB::table('packing as p');
        $query->select('p.packing_id', 'p.sale_order_id');
        $query->join('sale_order as so', 'so.sale_order_id', '=', 'p.sale_order_id', 'left');
        $query->join('shipping as sh', 'sh.sale_order_id', '=', 'p.sale_order_id', 'left');
        $query->where([['so.client_id', '=', $request->user_id], ['so.order_status', '=', 1]]);
        $query->whereNull('sh.sale_order_id');
        $query->orderBy('p.sale_order_id', 'desc');
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
    public function pending_shipment_details(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'sale_order_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $PackingDetails = PackingDetails::select('product_id', 'quantity', 'price')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($PackingDetails) > 0) {
            foreach($PackingDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['pmrprc'])) $price = round($Products[0]['pmrprc'],0,3);
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            }
        }
        return response()->json(["status" => 1, 'products' => $returnData]);
    }
    public function view_order_details(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'sale_order_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $SaleOrderDetails = SaleOrderDetails::where([['sale_order_id', '=', $request->sale_order_id], ['is_deleted', '=', '0']])->get()->toArray();
        if(sizeof($SaleOrderDetails) > 0) {
            foreach($SaleOrderDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $Products = Products::select('part_name_id', 'pmpno')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                }
                $qty_appr = '';
                $SaleOrder = SaleOrder::select('is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                if(sizeof($SaleOrder)>0) {
                    $WmsSaleOrderAproved = WmsSaleOrderAproved::select('qty_appr')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
                    if(sizeof($WmsSaleOrderAproved) > 0) {
                        $qty_appr = $WmsSaleOrderAproved[0]['qty_appr'];
                    }
                }
                array_push($returnData, array('sale_order_details_id' => $data['sale_order_details_id'], 'sale_order_id' => $data['sale_order_id'], 'order_line_no' => $data['order_line_no'], 'product_id' => $data['product_id'], 'product_tax' => $data['product_tax'], 'product_price' => $data['product_price'], 'qty' => $data['qty'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'qty_appr'=> $qty_appr));
            }
        }
        $is_rejected=0;
        $is_approved=0;
        $SaleOrder = SaleOrder::select('is_rejected', 'is_approved')->where([['sale_order_id', '=', $request->sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrder)>0) {
            if(!empty($SaleOrder[0]['is_rejected'])) $is_rejected = $SaleOrder[0]['is_rejected'];
            if(!empty($SaleOrder[0]['is_approved'])) $is_approved = $SaleOrder[0]['is_approved'];
        }
        $reject_reason = '';
        if($is_rejected == "1") {
            $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
            if(sizeof($SaleOrderRejectReason) > 0) {
                $reject_reason = $SaleOrderRejectReason[0]['reason'];
            }
        }
        $sales_order_template_name = "";
        $SaleOrderTemplate = SaleOrderTemplate::select('template_name')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
        if(sizeof($SaleOrderTemplate) > 0) {
            $sales_order_template_name = $SaleOrderTemplate[0]['template_name'];
        }
        return response()->json(["status" => 1, 'products' => $returnData,'is_rejected' => $is_rejected, 'reject_reason' => $reject_reason,'is_approved' => $is_approved, 'sales_order_template_name' => $sales_order_template_name]);
    }
    public function remove_order_item(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'sale_order_id' => 'required|int',
            'sale_order_details_id' => 'required|int',
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $qry=SaleOrderDetails::where('sale_order_details_id',$request->sale_order_details_id)->update(array('is_deleted'=>1));
        if($qry) {
            return response()->json(["status" => 1, 'msg' => "Item is deleted successfully."]);
        }else {
            return response()->json(["status" => 0, 'msg' => "Item is deleted faild."]);
        }
    }
    public function view_reason(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'sale_order_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        $SaleOrderRejectReason = SaleOrderRejectReason::select('reason')->where('sale_order_id', $request->sale_order_id)->get()->toArray();
        if(sizeof($SaleOrderRejectReason) > 0) {
            return response()->json(["status" => 1, 'reject_reason' => $SaleOrderRejectReason[0]['reason']]);
        }else {
            return response()->json(["status" => 0, 'msg' => "No reason found."]);
        }
    }
    public function get_product_by_part_no(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $returnData = [];
        if(!empty($request->part_no)) {
            $ProductsData = [];
            $view = "";
            $query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno');
            //$query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->where('p.pmpno', 'like', '%' . $request->part_no . '%');
            $query->limit('100');
            $Products = $query->get()->toArray();
            $product_data=[];
            if(sizeof($Products) > 0) {
                foreach($Products as $data) {
                    $product_data[]=$data->pmpno;
                }
                $returnData = array('status' => 1, 'data' => $product_data);
            }else {
                $returnData = array('status' => 0, 'msg' => "No record found.");
            }
        }
        return response()->json($returnData);
    }
    public function product_details_by_part_no(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'part_no' =>'required'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $data_array=[];
        $query = DB::table('products');
        $query->join('part_name', 'part_name.part_name_id', '=', 'products.part_name_id', 'left');
        $query->join('product_categories', 'product_categories.category_id', '=', 'products.ct');
        $query->select('products.*','product_categories.category_name as c_name', 'part_name.part_name' );
        $query->where('products.pmpno', '=', $request->part_no);
        $data=$query->get()->toArray();
        if(sizeof($data)>0) {
            $model = new DB;
            $available_stock = available_stock_by_part_no($model,$request->part_no);
            $data_array=array('product_id'=>$data[0]->product_id,'part_no'=>$data[0]->pmpno,'part_name'=>$data[0]->part_name,'ct'=>$data[0]->ct,'c_name'=>$data[0]->c_name,'pmrprc'=>round($data[0]->pmrprc,2),'current_stock'=>$available_stock);
        }
        if(sizeof($data_array) > 0) {
            $returnData = array('status' => 1, 'data' => $data_array);
        }else {
            $returnData = array('status' => 0, 'msg' => "No record found.");
        }
        return response()->json($returnData);
    }
    public function submit_order(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'order_data' =>'required'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $data_array = $request->order_data;
        $order_data = array('client_id'=>$request->user_id,'sub_total'=>$request->sub_total,'gst'=>$request->total_tax,'grand_total'=>$request->grand_total,'remarks'=>$request->remarks,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'));
        $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
        for($i=0;$i<sizeof($data_array);$i++) {
            $product_tax = (($data_array[$i]['mrp']*$data_array[$i]['quantity'])*$data_array[$i]['vat'])/100;
            $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
            $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$data_array[$i]['product_id'],'product_tax'=>$data_array[$i]['vat'],'product_price'=>$data_array[$i]['mrp'],'qty'=>$data_array[$i]['quantity']);
            SaleOrderDetails::insert($order_details);
        }
        $returnData = array('status' => 1, 'msg' => "Order is created successfully");
        return response()->json($returnData);
    }
    public function order_again_details(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int'
        ]);
        if($validator->fails()){
            return response()->json(["status" => 0, "msg" => $validator->errors()]);
        }
        $last_order=SaleOrder::select('*')->where('client_id',$request->user_id)->orderBy('sale_order_id','desc')->limit(1)->get()->toArray();
        $last_sale_order_details = SaleOrderDetails::select('*')->where('sale_order_id',$last_order[0]['sale_order_id'])->get()->toArray();
        if(sizeof($last_sale_order_details) > 0) {
            $returnData = array('status' => 1, 'data' => $last_sale_order_details);
        }else {
            $returnData = array('status' => 0, 'msg' => "No record found.");
        }
        return response()->json($returnData);
    }
    
}