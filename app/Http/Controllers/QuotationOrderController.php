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

class QuotationOrderController extends Controller {

    public function quotation_order() {
        return \View::make("backend/quotation_order/quotation_order")->with([
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
            // ->addColumn('item', function ($query) {
            //     $selectQty = OrderRequestDetails::where('order_request_id',$query->order_request_id)->sum('qty');
            //     return $selectQty;
            // })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-order-quotation-details" data-id="'.$query->order_quotation_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('action', function ($query) {
                if($query->is_confirm == "1") {
                    $action = "";
                }else {
                    $action = '<a href="javascript:void(0)" class="delete-order-quotation" data-id="'.$query->order_quotation_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                }
                return $action;
            })
            ->rawColumns(['details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Get Order request
    public function get_order_request_by_id(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $OrderRequest = OrderRequest::select('order_request_id')->where([['order_request_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($OrderRequest) > 0) {
                $query = DB::table('order_request_details as o');
                $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
                $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
                $query->select('o.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'o.order_request_details_id', 'o.order_request_id','o.qty');
                $query->where([['o.order_request_id', '=', $request->id]]);
                $orderDetails = $query->get()->toArray();
                if(sizeof($orderDetails) > 0) {
                    foreach($orderDetails as $data) {
                        $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id]])->get()->toArray();
                        array_push($returnData, array('part_brand_name' => $data->part_brand_name, 'pmpno' => $data->pmpno, 'part_name' => $data->part_name, 'unit_name' => $data->unit_name, 'order_request_details_id' => $data->order_request_details_id, 'order_request_id' => $data->order_request_id, 'qty' => $data->qty, 'manufacturing_no' => $ManufacturingNo));
                    }
                }
                $html = view('backend.quotation_order.order_details')->with([
                    'order_data' => $returnData
                ])->render();
                return response()->json(["status" => 1, "message" => $html]);
            }else {
                return response()->json(["status" => 0, "msg" => "No order has found by this ID. Please enter correct Order Request ID."]);
            }
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
    public function save_quotation_order(Request $request) {
        $returnData = [];
        $OrderRequest = OrderRequest::select('order_request_id')->where([['order_request_id', '=', $request->order_request_id], ['status', '=', '1']])->get()->toArray();
        $upimages = $request->quotation_file;
        $new_name = rand() . '.' . $upimages->getClientOriginalExtension();
        $upimages->move(public_path('backend/images/quotation_file/'), $new_name);
        if(sizeof($OrderRequest) > 0) {
            $OrderQuotation = OrderQuotation::where([['order_request_id', '=', $request->order_request_id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($OrderQuotation) > 0) {
                $upOrderQuotation = OrderQuotation::where([['order_request_id', '=', $request->order_request_id]])->update(array('quotation' => $new_name));
                if($upOrderQuotation) {
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save faild."];
                }
            }else {
                $data = new OrderQuotation;
                $data->order_request_id = $request->order_request_id;
                $data->quotation = $new_name;
                $data->is_confirm = "0";
                $data->status = "1";
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save faild."];
                }
            }
        }else {
            $returnData = ["status" => 0, "msg" => "No order found."];
        }
        return response()->json($returnData);
    }
    // Delete
    public function delete_quotation_order(Request $request) {
        $returnData = [];
        $upData = OrderQuotation::where('order_quotation_id', $request->id)->update(['status' => "2"]);
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    // View
    public function view_quotation_order(Request $request){
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
            $html = view('backend.quotation_order.quotation_order_details')->with([
                'quotation_data' => $returnData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Order Details
    public function delete_order_request_details(Request $request) {
        $returnData = [];
        $upData = OrderRequestDetails::where('order_request_details_id', $request->id)->delete();
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
}