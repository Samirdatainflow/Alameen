<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Suppliers;
use App\Products;
use App\Orders;
use App\OrderDetail;
use App\ProductCategories;
use App\OrderRequest;
use App\OrderRequestDetails;
use App\ManufacturingNo;
use App\OrderQuotation;
use DB;
use DataTables;
use PDF;

class OrderConfirmationController extends Controller {

    public function order_confirmation() {
        return \View::make("backend/order_confirmation/order_confirmation")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->orderBy('supplier_id', 'desc')->get()->toArray()
        ]);
    }
    // List
    public function list_quotation_order(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('order_quotation as oq');
            $query->select('oq.order_quotation_id', 'oq.order_request_id', 'oq.is_confirm', 's.full_name as supplier_name', 'or.created_at');
            $query->join('order_request as or', 'or.order_request_id', '=', 'oq.order_request_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'or.supplier_id', 'left');
            $query->where([['oq.status', '!=', '2']]);
            if($keyword)
            {
                $sql = "s.full_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.supplier_name', 'asc');
                else
                    $query->orderBy('oq.order_quotation_id', 'desc');
            }
            else
            {
                $query->orderBy('oq.order_quotation_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['or.supplier_id', '=', $request->filter_supplier]]);
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->created_at)) {
                    $order_date = date('d M Y', strtotime($query->created_at));
                }
                return $order_date;
            })
            ->addColumn('confirm_details', function ($query) {
                $confirm_details = "";
                if($query->is_confirm == "1") {
                    $confirm_details = '<a href="javascript:void(0)" class="confirm-quotation-order" data-id="'.$query->order_quotation_id.'" data-status="0" title="Click for Unconfirmed"><span class="badge badge-success" data-id="15">Confirm</span></a>';
                }else {
                    $confirm_details = '<a href="javascript:void(0)" class="confirm-quotation-order" data-id="'.$query->order_quotation_id.'" data-status="1" title="Click for Confirm"><span class="badge badge-danger" data-id="15">Not Confirm</span></a>';
                }
                return $confirm_details;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-quotation-order-details" data-id="'.$query->order_quotation_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->rawColumns(['confirm_details', 'details'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Add
    public function add_quotation(Request $request){
        if ($request->ajax()) {
            $html = view('backend.quotation_order.quotation_form')->with([
                'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray(),
                //'product_data' => Products::select('product_id', 'part_name', 'pmpno')->where([['is_deleted', '!=', '1']])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Save
    public function save_order_request(Request $request) {
        $returnData = [];
        if(!empty($request->supplier)) {
            foreach($request->supplier as $v=>$k) {
            	$data = new OrderRequest;
		        $data->supplier_id = $k;
                $data->mail_status = "0";
		        $data->status = "1";
		        $saveData = $data->save();
		        if($saveData) {
		            $flag = 0;
		            $last_id = $data->id;
		            if(sizeof($request->entry_product) > 0) {
		                for($i = 0; $i<sizeof($request->entry_product); $i++) {
		                    $data2 = new OrderRequestDetails;
		                    $data2->order_request_id = $last_id;
		                    $data2->product_id = $request->entry_product[$i];
		                    $data2->qty = $request->entry_product_quantity[$i];
		        			$data2->status = "1";
		                    $data2->save();
		                    $flag++;
		                }
		            }
		            if($flag == sizeof($request->entry_product)) {
		                $returnData = ["status" => 1, "msg" => "Save successful."];
		            }else {
		                $returnData = ["status" => 0, "msg" => "Something is wrong."];
		            }
		        }else {
		            $returnData = ["status" => 0, "msg" => "Save faild."];
		        }
            }
        }else {
            $returnData = ["status" => 0, "msg" => "Save faild. No record found"];
        }
        return response()->json($returnData);
    }
    public function view_quotation_order_details(Request $request){
        if ($request->ajax()) {
            $returnData = [];
            $OrderQuotation = OrderQuotation::select('order_request_id', 'quotation', 'created_at')->where([['order_quotation_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($OrderQuotation) > 0) {
                $order_request_date = "";
                $OrderRequest = OrderRequest::select('created_at')->where([['order_request_id', '=', $OrderQuotation[0]['order_request_id']]])->get()->toArray();
                if(!empty($OrderRequest)) {
                    $order_request_date = $OrderRequest[0]['created_at'];
                }
                $orderData = [];
                $query = DB::table('order_request_details as o');
                $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
                $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
                $query->select('o.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'o.order_request_details_id', 'o.order_request_id','o.qty');
                $query->where([['o.order_request_id', '=', $OrderQuotation[0]['order_request_id']]]);
                $orderDetails = $query->get()->toArray();
                if(sizeof($orderDetails) > 0) {
                    foreach($orderDetails as $data) {
                        $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id]])->get()->toArray();
                        array_push($orderData, array('part_brand_name' => $data->part_brand_name, 'pmpno' => $data->pmpno, 'part_name' => $data->part_name, 'unit_name' => $data->unit_name, 'order_request_details_id' => $data->order_request_details_id, 'order_request_id' => $data->order_request_id, 'qty' => $data->qty, 'manufacturing_no' => $ManufacturingNo));
                    }
                }
                $url = url('public/backend/images/quotation_file/');
                array_push($returnData, array('order_request_id' => $OrderQuotation[0]['order_request_id'], 'quotation' => $url."/".$OrderQuotation[0]['quotation'], 'created_at' => $order_request_date, 'order_data' => $orderData));
            }
            $html = view('backend.order_confirmation.quotation_order_details')->with([
                'quotation_data' => $returnData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Confiramation
    public function chnage_confirmation(Request $request) {
        $returnData = [];
        $upData = OrderQuotation::where('order_quotation_id', $request->id)->update(['is_confirm' => $request->status]);
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Successful Done"];
        }else {
            $returnData = ["status" => 0, "msg" => "Failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
}