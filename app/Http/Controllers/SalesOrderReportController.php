<?php

namespace App\Http\Controllers;

use App\Clients;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\SaleOrderDetails;
use File;
use Session;
use App\WmsStock;
use App\Products;
use App\PartName;
use App\SalesReceipt;
use DB;
use DataTables;
use App\Returns;
use App\VatType;
use App\ReturnDetail;

class SalesOrderReportController extends Controller {
    
    public function index() {
        return \View::make("backend/reports/sales_order_report")->with([]);
    }
    
    public function sales_order_report_list(Request $request) {
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
    
    public function customer_order_report_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $from_date = date('Y-m-d', strtotime('today - 40 days'));
            $current_date = date('Y-m-d');
            if(!empty($request->filter_from_date_by_days) && !empty($request->filter_to_date_by_days)) {
                $from_date = date('Y-m-d', strtotime($request->filter_from_date_by_days));
                $current_date = date('Y-m-d', strtotime($request->filter_to_date_by_days));
            }
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT sale_order_id,client_id,created_at FROM sale_order where DATE_FORMAT(created_at,"%Y-%m-%d") BETWEEN "'.$from_date.'" AND "'.$current_date.'"');
            //print_r($query); exit();
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $reg_no = "";
                    $customer_name = "";
                    $customer_area = "";
                    $customer_teritory = "";
                    $customer_region = "";
                    $Clients = Clients::select('reg_no', 'customer_name', 'customer_area', 'customer_teritory', 'customer_region')->where([['client_id', '=', $val->client_id]])->get()->toArray();
                    if(sizeof($Clients) > 0) {
                        $reg_no = $Clients[0]['reg_no'];
                        $customer_name = $Clients[0]['customer_name'];
                        $customer_area = $Clients[0]['customer_area'];
                        $customer_teritory = $Clients[0]['customer_teritory'];
                        $customer_region = $Clients[0]['customer_region'];
                    }
                    $SaleOrderDetails = SaleOrderDetails::select('product_id')->where([['sale_order_id', '=', $val->sale_order_id]])->get()->toArray();
                    if(sizeof($SaleOrderDetails) > 0) {
                        foreach($SaleOrderDetails as $sale) {
                            $pmpno = "";
                            $part_name = "";
                            $Products = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $sale['product_id']]])->get()->toArray();
                            if(sizeof($Products)>0){
                                $pmpno = $Products[0]['pmpno'];
                                $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                                if(sizeof($PartName)>0){
                                    $part_name = $PartName[0]['part_name'];
                                }
                            }
                            array_push($arrayData, array('created_at' => date('M, d Y', strtotime($val->created_at)), 'product_id' => $sale['product_id'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'reg_no' => $reg_no, 'customer_name' => $customer_name, 'customer_area' => $customer_area, 'customer_teritory' => $customer_teritory, 'customer_region' => $customer_region));
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
    
    // Approved Order
    public function approved_orders() {
        return \View::make("backend/reports/sales_approved_order_report")->with([]);
    }
    
    public function approved_orders_list(Request $request) {
        if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');
            //$keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.created_at', 'clients.customer_name', 'clients.reg_no', 'clients.customer_area', 'clients.customer_teritory', 'clients.customer_region');
            $query->where('is_approved', 1);
            // if($keyword)
            // {
            //     $query->whereRaw("(`clients`.`sponsor_name` like '%$keyword%')");
            //     //$sql = "customer_name like ?";
            //     //$query->whereRaw($sql, ["%{$keyword}%"]);
            // }
            if(!empty($request->filter_area)) {
                $query->where('clients.customer_area', 'like', '%' . $request->filter_area . '%');
            }
            if(!empty($request->filter_territory)) {
                $query->where('clients.customer_teritory', 'like', '%' . $request->filter_territory . '%');
            }
            if(!empty($request->filter_region)) {
                $query->where('clients.customer_region', 'like', '%' . $request->filter_region . '%');
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('sale_order.sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order.sale_order_id', 'desc');
            }else {
                $query->orderBy('sale_order.sale_order_id', 'DESC');
            }
            $data_sale=$query->get()->toArray();
            $sale= new Collection;
            foreach($data_sale as $data_array){
                $product_status = $this->chcekProductStock($data_array->sale_order_id);
                if($product_status == 1) {
                    $sale->push(['order_id' => $data_array->sale_order_id, 'customer_name' => $data_array->customer_name, 'reg_no' => $data_array->reg_no, 'customer_area'=> $data_array->customer_area, 'customer_teritory'=> $data_array->customer_teritory, 'customer_region'=> $data_array->customer_region, 'created_at' => date('d M Y',strtotime($data_array->created_at))]);
                }
                
            }
            $datatable_array=Datatables::of($sale)
                ->filter(function ($instance) use ($request) {

                    if (!empty($request->input('search.value'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['company_name']), Str::lower($request->input('search.value')))){
                                return true;
                            }
                            else if (Str::contains(Str::lower($row['customer_name']), Str::lower($request->input('search.value')))) {
                                return true;
                            }

                            return false;
                        });
                    }

                })
            ->rawColumns(['created_at', 'reg_no', 'customer_name', 'customer_area', 'customer_teritory', 'customer_region'])
            ->toJson();
            return $datatable_array;
            
        }
    }
    
    // Not Approved Order
    public function not_approved_orders() {
        return \View::make("backend/reports/sales_not_approved_order_report")->with([]);
    }
    
    public function not_approved_orders_list(Request $request) {
        if ($request->ajax()) {
            $data=[];
            $order = $request->input('order.0.dir');
            //$keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id', 'left');
            $query->join('sale_order_reject_reason', 'sale_order_reject_reason.sale_order_id', '=', 'sale_order.sale_order_id', 'left');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.created_at', 'clients.customer_name', 'clients.reg_no', 'clients.customer_area', 'clients.customer_teritory', 'clients.customer_region', 'sale_order_reject_reason.reason');
            $query->where('is_rejected', 1);
            if(!empty($request->not_approved_filter_area)) {
                $query->where('clients.customer_area', 'like', '%' . $request->not_approved_filter_area . '%');
            }
            if(!empty($request->not_approved_filter_territory)) {
                $query->where('clients.customer_teritory', 'like', '%' . $request->not_approved_filter_territory . '%');
            }
            if(!empty($request->not_approved_filter_region)) {
                $query->where('clients.customer_region', 'like', '%' . $request->not_approved_filter_region . '%');
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('sale_order.sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order.sale_order_id', 'desc');
            }else {
                $query->orderBy('sale_order.sale_order_id', 'DESC');
            }
            $data_sale=$query->get()->toArray();
            $sale= new Collection;
            foreach($data_sale as $data_array){
                $product_status = $this->chcekProductStock($data_array->sale_order_id);
                if($product_status == 1) {
                    $sale->push(['order_id' => $data_array->sale_order_id, 'customer_name' => $data_array->customer_name, 'reg_no' => $data_array->reg_no, 'customer_area'=> $data_array->customer_area, 'customer_teritory'=> $data_array->customer_teritory, 'customer_region'=> $data_array->customer_region, 'created_at' => date('d M Y',strtotime($data_array->created_at)), 'reason' => $data_array->reason]);
                }
                
            }
            $datatable_array=Datatables::of($sale)
                ->filter(function ($instance) use ($request) {

                    if (!empty($request->input('search.value'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['company_name']), Str::lower($request->input('search.value')))){
                                return true;
                            }
                            else if (Str::contains(Str::lower($row['customer_name']), Str::lower($request->input('search.value')))) {
                                return true;
                            }

                            return false;
                        });
                    }

                })
            ->rawColumns(['created_at', 'reg_no', 'customer_name', 'customer_area', 'customer_teritory', 'customer_region'])
            ->toJson();
            return $datatable_array;
            
        }
    }
    
    // Not Approved Order
    public function no_of_orders_by_dates() {
        return \View::make("backend/reports/sales_report_orders_by_dates")->with([]);
    }
    
    public function no_of_orders_by_dates_list(Request $request) {
        if ($request->ajax()) {
            $from_date = date('Y-m-d', strtotime('today - 40 days'));
            $current_date = date('Y-m-d');
            if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                $from_date = date('Y-m-d', strtotime($request->filter_from_date));
                $current_date = date('Y-m-d', strtotime($request->filter_to_date));
            }
            //where DATE_FORMAT(created_at,"%Y-%m-%d") BETWEEN "'.$from_date.'" AND "'.$current_date.'"
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order');
            $query->join('clients', 'sale_order.client_id', '=', 'clients.client_id');
            $query->select('sale_order.sale_order_id','sale_order.client_id as c_id', 'sale_order.created_at', 'clients.customer_name');
            $query->whereRaw('DATE_FORMAT(sale_order.created_at,"%Y-%m-%d") BETWEEN "'.$from_date.'" AND "'.$current_date.'"');
            if(!empty($request->client_id)) {
                $query->where('sale_order.client_id', '=', $request->client_id);
            }
            if($keyword) {
                $sql = "customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('sale_order_id', 'asc');
                else
                    $query->orderBy('sale_order_id', 'desc');
            }else {
                $query->orderBy('sale_order_id', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
            ->addColumn('order_id', function ($query) {
                $sale_order_id = '';
                if(!empty($query->sale_order_id)) {
                    $sale_order_id .= $query->sale_order_id;
                }
                return $sale_order_id;
            })
            ->addColumn('client_name', function ($query) {
                $customer_name = '';
                if(!empty($query->customer_name)) {
                    $customer_name .= $query->customer_name;
                }
                return $customer_name;
            })
            ->addColumn('created_at', function ($query) {
                $created_at = '';
                if(!empty($query->created_at)) {
                    $created_at .= date('d M Y',strtotime($query->created_at));
                }
                return $created_at;
            })
            ->rawColumns(['order_id', 'client_name', 'company_name', 'grand_total', 'created_at', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    public static function chcekProductStock($sale_order_id) {
        $flag = 1;
        $SaleOrderDetails = SaleOrderDetails::select('product_id')->where([['sale_order_id', '=', $sale_order_id]])->get()->toArray();
        if(sizeof($SaleOrderDetails) >0) {
            foreach($SaleOrderDetails as $data) {
                $WmsStock = WmsStock::select('qty')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($WmsStock) >0) {
                    if($WmsStock[0]['qty'] < 1) {
                        $flag = 0;
                        break;
                    }
                }else {
                    $flag = 0;
                    break;
                }
            }
        }
        return $flag;
    }
    
    //Outstanding Report
    public function sales_order_outstanding_report() {
        return \View::make("backend/reports/sales_order_outstanding_report")->with([
            'ClientData' => Clients::select('client_id', 'customer_name')->where([['delete_status', '=', '0']])->orderBy('customer_name', 'ASC')->get()->toArray()
            ]);
    }
    
    public function list_sales_order_outstanding_report(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order as s');
            $query->select('s.sale_order_id', 's.grand_total', 'c.customer_name', 's.gst', 's.vat_type_id');
            $query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('s.sale_order_id', 'desc');
                else
                    $query->orderBy('s.sale_order_id', 'desc');
            }else {
                $query->orderBy('s.sale_order_id', 'DESC');
            }
            $query->where([['s.delete_status', '=', '0'], ['s.print_invoice', '=', '1']]);
            
            if(!empty($request->filter_date)) {
                
                $filter_date = date('Y-m-d', strtotime($request->filter_date));
                $query->where('s.invoice_date', '=', $filter_date);
            }
            if(!empty($request->filter_customer)) {
                $query->where('c.client_id', 'like', '%' . $request->filter_customer . '%');
            }
            
            $datatable_array=Datatables::of($query)
            ->addColumn('invoice_amount', function ($query) {
                    
                    $TotalGrandTotal = 0;
                    
                    if($query->grand_total > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->grand_total;
                    }
                    
                    if($query->gst > 0)
                    {
                        $TotalGrandTotal = $TotalGrandTotal + $query->gst;
                    }
                    
                    $TotalGrandTotal = round($TotalGrandTotal, 3);
                    $returnPrice = 0;
                    $selectReturns = Returns::select('return_id')->where([['sale_order_id', '=', $query->sale_order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        $selectReturnDetails = ReturnDetail::select('product_id', 'received_quantity')->where([['return_id', '=', $selectReturns[0]['return_id']]])->get()->toArray();
                        if(sizeof($selectReturnDetails) > 0)
                        {
                            foreach($selectReturnDetails as $rddata)
                            {
                                $qty = $rddata['received_quantity'];
                                $returnProductPrice = 0;
                                
                                $selectOrderDetails = SaleOrderDetails::select('product_price')->where([['sale_order_id', '=', $query->sale_order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                if(sizeof($selectOrderDetails) > 0)
                                {
                                    $returnProductPrice = $selectOrderDetails[0]['product_price'];
                                }
                                $qty *
                                $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                            }
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $TotalGrandTotal = $TotalGrandTotal - $returnPrice;
                    return $TotalGrandTotal;
                })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    //Outstanding Report
    public function sales_order_ageing_report() {
        return \View::make("backend/reports/sales_order_ageing_report")->with([
            'ClientData' => Clients::select('client_id', 'customer_name')->where([['delete_status', '=', '0']])->orderBy('customer_name', 'ASC')->get()->toArray()
            ]);
    }
    
    public function list_sales_order_ageing_report(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select("SELECT client_id, SUM(IF(DATEDIFF(CURDATE(), invoice_date) BETWEEN 0 AND 14, grand_total, 0)) as due_days1, SUM(IF(DATEDIFF(CURDATE(), invoice_date) BETWEEN 15 AND 29, grand_total, 0)) as due_days2, SUM(IF(DATEDIFF(CURDATE(), invoice_date) BETWEEN 30 AND 44, grand_total, 0)) as due_days3, SUM(IF(DATEDIFF(CURDATE(), invoice_date) > 44, grand_total, 0)) as due_days4 , SUM(grand_total) as totalamount FROM sale_order WHERE print_invoice = '1' AND order_status = '1' GROUP BY client_id");
            //$query->select('SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 0 AND 14, grand_total, 0)) as due_days1', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 15 AND 29, grand_total, 0)) as due_days2', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 30 AND 44, grand_total, 0)) as due_days3', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) > 44, grand_total, 0)) as due_days4', 'c.customer_name');
            //$query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            // if($order) {
            //     if($order == "asc")
            //         $query->orderBy('s.sale_order_id', 'desc');
            //     else
            //         $query->orderBy('s.sale_order_id', 'desc');
            // }else {
            //     $query->orderBy('s.sale_order_id', 'DESC');
            // }
            // $query->where([['s.delete_status', '=', '0'], ['s.print_invoice', '=', '1']]);
            
            // if(!empty($request->filter_date)) {
                
            //     $filter_date = date('Y-m-d', strtotime($request->filter_date));
            //     $query->where('s.invoice_date', '=', $filter_date);
            // }
            // if(!empty($request->filter_customer)) {
            //     $query->where('c.client_id', 'like', '%' . $request->filter_customer . '%');
            // }
            
            $datatable_array=Datatables::of($query)
            ->addColumn('current_date', function ($query) {
                
                $current_date = date('d-m-Y');
                return $current_date;
            })
            ->addColumn('customer_name', function ($query) {
                $customer_name = "";
                
                $selectCustomer = Clients::select('customer_name')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                if(sizeof($selectCustomer) > 0) {
                    $customer_name = $selectCustomer[0]['customer_name'];
                }
                return $customer_name;
            })
            ->addColumn('duedays1', function ($query) {
                $duedays1 = round($query->due_days1, 3);
                return $duedays1;
            })
            ->addColumn('duedays2', function ($query) {
                $duedays2 = round($query->due_days2, 3);
                return $duedays2;
            })
            ->addColumn('duedays3', function ($query) {
                $duedays3 = round($query->due_days3, 3);
                return $duedays3;
            })
            ->addColumn('duedays4', function ($query) {
                $duedays4 = round($query->due_days4, 3);
                return $duedays4;
            })
            ->addColumn('total_amount', function ($query) {
                $total_amount= round($query->totalamount, 3);
                return $total_amount;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    // Invoice Report
    public function sales_order_invoice_report() {
        return \View::make("backend/reports/sales_order_invoice_report")->with([
            'ClientData' => Clients::select('client_id', 'customer_name')->where([['delete_status', '=', '0']])->orderBy('customer_name', 'ASC')->get()->toArray()
            ]);
    }
    
    public function list_sales_order_invoice_report(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sale_order as s');
            $query->select('s.grand_total', 's.invoice_date', 's.invoice_no', 'c.customer_name');
            $query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('s.sale_order_id', 'desc');
                else
                    $query->orderBy('s.sale_order_id', 'desc');
            }else {
                $query->orderBy('s.sale_order_id', 'DESC');
            }
            $query->where([['s.delete_status', '=', '0'], ['s.print_invoice', '=', '1']]);
            
            if(!empty($request->filter_month)) {
                
                //$filter_date = date('Y-m-d', strtotime($request->filter_month));
                $query->where(DB::raw("(DATE_FORMAT(s.invoice_date,'%m'))"), "=", $request->filter_month);
                //$query->where('DATE_FORMAT(s.invoice_date,,"%m")', '=', $request->filter_month);
            }
            if(!empty($request->filter_customer)) {
                $query->where('c.client_id', 'like', '%' . $request->filter_customer . '%');
            }
            
            $datatable_array=Datatables::of($query)
            ->addColumn('current_date', function ($query) {
                
                $current_date = date('d-m-Y');
                return $current_date;
            })
            ->addColumn('due_date', function ($query) {
                $due_date = "";
                return $due_date;
            })
            ->addColumn('due_days', function ($query) {
                $now = time();
                $your_date = strtotime($query->invoice_date);
                $datediff = $now - $your_date;
                
                $due_days = round($datediff / (60 * 60 * 24));
                
                $due_amount = $query->grand_total;
                $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_no]])->sum('pay_amount');
                if(!empty($SalesPay_amount)) {
                    
                    $payamount = round($SalesPay_amount,3);
                    $due_amount = $query->grand_total - $payamount;
                }
                
                if($due_amount > 0) {
                    $rdue_days = $due_days;
                }else {
                    $rdue_days = "";
                }
                return $rdue_days;
            })
            ->addColumn('due_amount', function ($query) {
                    
                $due_amount = $query->grand_total;
                $SalesPay_amount = SalesReceipt::where([['invoice_number', '=', $query->invoice_no]])->sum('pay_amount');
                if(!empty($SalesPay_amount)) {
                    
                    $payamount = round($SalesPay_amount,3);
                    $due_amount = $query->grand_total - $payamount;
                }
                return $due_amount;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    
    
}