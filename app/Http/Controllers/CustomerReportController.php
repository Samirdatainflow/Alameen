<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Clients;
use App\Products;
use App\SaleOrder;
use App\SaleOrderDetails;
use DB;
use DataTables;

class CustomerReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/customer_report")->with([]);
    }
    public function customer_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('clients');
            $query->select('*');
            if($keyword) {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('client_id', 'desc');
                else
                    $query->orderBy('client_id', 'desc');
            }else {
                $query->orderBy('client_id', 'DESC');
            }
            $query->where([['delete_status', '=', '0']]);
            if(!empty($request->filter_reg_no)) {
                $query->where('reg_no', 'like', '%' . $request->filter_reg_no . '%');
            }
            if(!empty($request->filter_customer_area)) {
                $query->where('customer_area', 'like', '%' . $request->filter_customer_area . '%');
            }
            if(!empty($request->filter_customer_region)) {
                $query->where('customer_region', 'like', '%' . $request->filter_customer_region . '%');
            }
            if(!empty($request->filter_customer_teritory)) {
                $query->where('customer_teritory', 'like', '%' . $request->filter_customer_teritory . '%');
            }
            $datatable_array=Datatables::of($query)
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 High Ordered Customer in Number
    public function top_5_high_ordered_customer_number() {
        return \View::make("backend/reports/top_5_high_ordered_customer_number")->with([]);
    }
    public function top_5_high_ordered_customer_number_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT sale_order.client_id,sum(sale_order_details.qty) AS product_qty FROM sale_order INNER JOIN sale_order_details ON sale_order.sale_order_id=sale_order_details.sale_order_id GROUP BY client_id ORDER By product_qty desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $reg_no = "";
                    $customer_name = "";
                    $Clients = Clients::select('customer_name', 'reg_no')->where([['client_id', '=', $val->client_id]])->get()->toArray();
                    if(sizeof($Clients)>0){
                        $reg_no = $Clients[0]['reg_no'];
                        $customer_name = $Clients[0]['customer_name'];
                    }
                    array_push($arrayData, array('client_id' => $val->client_id, 'product_qty' => $val->product_qty, 'customer_name' => $customer_name, 'reg_no' => $reg_no));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Top 5 High Ordered Customer in Value
    public function top_5_high_ordered_customer_value() {
        return \View::make("backend/reports/top_5_high_ordered_customer_value")->with([]);
    }
    public function top_5_high_ordered_customer_value_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT sale_order.client_id,sum(sale_order_details.product_price) AS productAmount FROM sale_order INNER JOIN sale_order_details ON sale_order.sale_order_id=sale_order_details.sale_order_id GROUP BY client_id ORDER By productAmount desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $reg_no = "";
                    $customer_name = "";
                    $Clients = Clients::select('customer_name', 'reg_no')->where([['client_id', '=', $val->client_id]])->get()->toArray();
                    if(sizeof($Clients)>0){
                        $reg_no = $Clients[0]['reg_no'];
                        $customer_name = $Clients[0]['customer_name'];
                    }
                    array_push($arrayData, array('client_id' => $val->client_id, 'productAmount' => $val->productAmount, 'customer_name' => $customer_name, 'reg_no' => $reg_no));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Customer Report By Inventory
    public function customer_report_by_inventory() {
        return \View::make("backend/reports/customer_report_by_inventory")->with([]);
    }
    public function customer_report_by_inventory_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = SaleOrder::select('sale_order_id', 'client_id')->get()->toArray();
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $reg_no = "";
                    $customer_name = "";
                    $Clients = Clients::select('customer_name', 'reg_no')->where([['client_id', '=', $val['client_id']]])->get()->toArray();
                    if(sizeof($Clients)>0){
                        $reg_no = $Clients[0]['reg_no'];
                        $customer_name = $Clients[0]['customer_name'];
                    }
                    $SaleOrderDetails = SaleOrderDetails::select('product_id')->where([['sale_order_id', '=', $val['sale_order_id']]])->get()->toArray();
                    if(sizeof($SaleOrderDetails)>0){
                        foreach($SaleOrderDetails as $sale) {
                            $product = DB::table('products as p');
                            $product->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                            $product->select('p.product_id', 'p.pmpno', 'pn.part_name');
                            $product->where([['product_id', '=', $sale['product_id']]]);
                            $productData = $product->get()->toArray();
                            array_push($arrayData, array('client_id' => $val['client_id'], 'customer_name' => $customer_name, 'reg_no' => $reg_no, 'product_id' => $sale['product_id'], 'pmpno' => $productData[0]->pmpno, 'part_name' => $productData[0]->part_name));
                        }
                    }
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}