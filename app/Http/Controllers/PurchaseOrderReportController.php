<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\OrderDetail;
use App\Suppliers;
use App\Warehouses;
use App\ConsignmentReceiptDetails;
use App\Products;
use App\PurchaseReceipt;
use DB;
use DataTables;

class PurchaseOrderReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/purchase_order_report")->with([
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('supplier_id', 'desc')->get()->toArray(),
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where('status', 1)->orderBy('warehouse_id', 'desc')->get()->toArray(),
        ]);
    }
    public function purchase_order_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1']]);
            if($keyword) {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }else {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->whereIn('o.supplier_id', $request->filter_supplier);
            }
            if(!empty($request->filter_warehouse)) {
                $query->whereIn('o.warehouse_id', $request->filter_warehouse);
            }
            if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                $from_date = date('Y-m-d', strtotime($request->filter_from_date));
                $to_date = date('Y-m-d', strtotime($request->filter_to_date));
                $query->where(DB::raw("(STR_TO_DATE(datetime,'%Y-%m-%d'))"), ">=", $from_date);
                $query->where(DB::raw("(STR_TO_DATE(datetime,'%Y-%m-%d'))"), "<=", $to_date);
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->datetime)) {
                    $order_date = date('d M Y', strtotime($query->datetime));
                }
                return $order_date;
            })
            ->addColumn('deliverydate', function ($query) {
                $delivery_date = '';
                if(!empty($query->deliverydate)) {
                    $delivery_date = date('d M Y', strtotime($query->deliverydate));
                }
                return $delivery_date;
            })
            ->addColumn('item', function ($query) {
                $selectQty = OrderDetail::where('order_id',$query->order_id)->sum('qty');
                return $selectQty;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    public function purchase_order_outstanding_report() {
        
        return \View::make("backend/reports/purchase_order_outstanding_report")->with([
            'Suppliers' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('full_name', 'ASC')->get()->toArray()
        ]);
    }
    public function list_purchase_order_outstanding_report(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            // $query = DB::table('sale_order as s');
            // $query->select('s.grand_total', 'c.customer_name');
            // $query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            
            $query = DB::table('consignment_receipt as cr');
            $query->select('cr.order_id', 'o.invoice_no', 'o.supplier_id', 'o.deliverydate', 's.full_name as supplier_name');
            $query->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
            $query->where([['cr.status', '!=', '2']]);
            
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('cr.order_id', 'desc');
                else
                    $query->orderBy('cr.order_id', 'desc');
            }else {
                $query->orderBy('cr.order_id', 'DESC');
            }
            //$query->where([['s.delete_status', '=', '0'], ['s.print_invoice', '=', '1']]);
            
            if(!empty($request->filter_date)) {
                
                $filter_date = date('Y-m-d', strtotime($request->filter_date));
                $query->where('o.deliverydate', '=', $filter_date);
            }
            if(!empty($request->filter_supplier)) {
                $query->where('o.supplier_id', 'like', '%' . $request->filter_supplier . '%');
            }
            
            $datatable_array=Datatables::of($query)
            
            ->addColumn('grand_total', function ($query) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProducts = Products::select('pmrprc')->where([['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProducts) > 0) {
                                $price = $selectProducts[0]['pmrprc'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $grand_total = round($grand_total,3);
                    return $grand_total;
                })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    // Ageing Report
    public function purchase_order_ageing_report() {
        return \View::make("backend/reports/purchase_order_ageing_report")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('full_name', 'ASC')->get()->toArray()
            ]);
    }
    
    public function list_purchase_order_ageing_report(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select("SELECT supplier_id, SUM(IF(DATEDIFF(CURDATE(), deliverydate) BETWEEN 0 AND 14, grand_total, 0)) as due_days1, SUM(IF(DATEDIFF(CURDATE(), deliverydate) BETWEEN 15 AND 29, grand_total, 0)) as due_days2, SUM(IF(DATEDIFF(CURDATE(), deliverydate) BETWEEN 30 AND 44, grand_total, 0)) as due_days3, SUM(IF(DATEDIFF(CURDATE(), deliverydate) > 44, grand_total, 0)) as due_days4 , SUM(grand_total) as totalamount FROM orders WHERE is_delete = '0' AND orders_status = '1' GROUP BY supplier_id");
            //$query->select('SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 0 AND 14, grand_total, 0)) as due_days1', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 15 AND 29, grand_total, 0)) as due_days2', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) BETWEEN 30 AND 44, grand_total, 0)) as due_days3', 'SUM(IF(DATEDIFF(CURDATE(), s.invoice_date) > 44, grand_total, 0)) as due_days4', 'c.customer_name');
            //$query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            
            
            $datatable_array=Datatables::of($query)
            ->addColumn('current_date', function ($query) {
                
                $current_date = date('d-m-Y');
                return $current_date;
            })
            ->addColumn('customer_name', function ($query) {
                $customer_name = "";
                
                $selectCustomer = Suppliers::select('full_name')->where([['supplier_id', '=', $query->supplier_id]])->get()->toArray();
                if(sizeof($selectCustomer) > 0) {
                    $customer_name = $selectCustomer[0]['full_name'];
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
    public function purchase_order_invoice_report() {
        return \View::make("backend/reports/purchase_order_invoice_report")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where('status', 1)->orderBy('full_name', 'ASC')->get()->toArray()
            ]);
    }
    
    public function list_purchase_order_invoice_report(Request $request) {
        
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('consignment_receipt as cr');
            $query->select('cr.order_id', 'o.deliverydate', 'o.grand_total', 's.full_name');
            $query->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
            if($keyword) {
                $sql = "c.customer_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('cr.order_id', 'desc');
                else
                    $query->orderBy('cr.order_id', 'desc');
            }else {
                $query->orderBy('cr.order_id', 'DESC');
            }
            $query->where([['cr.status', '=', '1']]);
            
            if(!empty($request->filter_month)) {
                $query->where(DB::raw("(DATE_FORMAT(o.deliverydate,'%m'))"), "=", $request->filter_month);
            }
            if(!empty($request->filter_supplier)) {
                $query->where('o.supplier_id', 'like', '%' . $request->filter_supplier . '%');
            }
            
            $datatable_array=Datatables::of($query)
            ->addColumn('invoice_date', function ($query) {
                
                $current_date = date('d-m-Y', strtotime($query->deliverydate));
                return $current_date;
            })
            ->addColumn('due_date', function ($query) {
                $due_date = "";
                return $due_date;
            })
            ->addColumn('due_days', function ($query) {
                $now = time();
                $invoice_date = date('Y-m-d', strtotime($query->deliverydate));
                $your_date = strtotime($invoice_date);
                $datediff = $now - $your_date;
                
                $due_days = round($datediff / (60 * 60 * 24));
                
                $due_amount = $query->grand_total;
                $SalesPay_amount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
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
                $SalesPay_amount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
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