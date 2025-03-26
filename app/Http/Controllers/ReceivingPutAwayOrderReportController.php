<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\CheckInDetails;
use DB;
use DataTables;

class ReceivingPutAwayOrderReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/receiving_put_away_report")->with([]);
    }
    public function receiving_put_away_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('gate_entry');
            $query->select('*');
            if($keyword) {
                $sql = "transaction_type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('transaction_type', 'asc');
                else
                    $query->orderBy('transaction_type', 'desc');
            }else {
                $query->orderBy('gate_entry_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date_show', function ($query) {
                $order_date_show = '';
                if(!empty($query->order_date)) {
                    $order_date_show = date('M d Y', strtotime($query->order_date));
                }
                return $order_date_show;
            })
            ->addColumn('vehicle_in_out_date_show', function ($query) {
                $vehicle_in_out_date_show = '';
                if(!empty($query->vehicle_in_out_date)) {
                    $vehicle_in_out_date_show = date('M d Y', strtotime($query->vehicle_in_out_date));
                }
                return $vehicle_in_out_date_show;
            })
            ->addColumn('courier_date_show', function ($query) {
                $courier_date_show = '';
                if(!empty($query->courier_date)) {
                    $courier_date_show = date('M d Y', strtotime($query->courier_date));
                }
                return $courier_date_show;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function number_of_picked_order_in_a_date() {
        return \View::make("backend/reports/number_of_picked_order_in_a_date")->with([]);
    }
    public function number_of_picked_order_in_a_date_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('packing');
            $query->select('*');
            if($keyword) {
                $sql = "transaction_type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('packing_id', 'asc');
                else
                    $query->orderBy('packing_id', 'desc');
            }else {
                $query->orderBy('packing_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date_show', function ($query) {
                $order_date_show = '';
                if(!empty($query->order_date)) {
                    $order_date_show = date('M d Y', strtotime($query->order_date));
                }
                return $order_date_show;
            })
            ->addColumn('vehicle_in_out_date_show', function ($query) {
                $vehicle_in_out_date_show = '';
                if(!empty($query->vehicle_in_out_date)) {
                    $vehicle_in_out_date_show = date('M d Y', strtotime($query->vehicle_in_out_date));
                }
                return $vehicle_in_out_date_show;
            })
            ->addColumn('courier_date_show', function ($query) {
                $courier_date_show = '';
                if(!empty($query->courier_date)) {
                    $courier_date_show = date('M d Y', strtotime($query->courier_date));
                }
                return $courier_date_show;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Good/Bad quantity in Order
    public function good_or_bad_quantity_order() {
        return \View::make("backend/reports/good_or_bad_quantity_order")->with([]);
    }
    public function good_or_bad_quantity_order_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('check_in as cn');
            $query->select('cn.check_in_id', 'cn.order_id', 'o.datetime', 'w.name as warehouse_name', 's.full_name as supplier_name');
            $query->join('orders as o', 'o.order_id', '=', 'cn.order_id', 'left');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
            $query->where([['cn.status', '!=', '2']]);
            //$query->groupBy('order_id');
            if($keyword) {
                $sql = "order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('order_id', 'asc');
                else
                    $query->orderBy('order_id', 'desc');
            }
            else
            {
                $query->orderBy('order_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = date('M, d Y', strtotime($query->datetime));
                return $order_date;
            })
            ->addColumn('good_quantity', function ($query) {
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('good_quantity');
                return $selectQty;
            })
            ->addColumn('bad_quantity', function ($query) {
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('bad_quantity');
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
    // Binning Report
    public function binning_report() {
        return \View::make("backend/reports/binning_report")->with([
            'Users' => Users::select('user_id', 'first_name', 'last_name')->where([['user_id', '!=', Session::get('user_id')], ['status', '=', 'Active']])->orderBy('user_id', 'desc')->get()->toArray(),
        ]);
    }
    public function binning_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('bining_task as b');
            $query->join('users as u', 'u.user_id', '=', 'b.user_id', 'left');
            $query->select('b.binning_task_id', 'b.order_id','b.status', 'b.close_status','b.created_at','u.first_name','u.last_name');
            //$query->where([['o.is_delete', '!=', '1']]);
            if($keyword) {
                $sql = "u.first_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order){
                if($order == "asc")
                    $query->orderBy('u.first_name', 'asc');
                else
                    $query->orderBy('u.first_name', 'desc');
            }else {
                $query->orderBy('binning_task_id', 'DESC');
            }
            if(!empty($request->filter_user)) {
                $query->where('u.user_id', $request->filter_user);
            }
            if(isset($request->filter_status)) {
                $query->where('b.status', $request->filter_status);
            }
            $query->where([['b.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('user_name', function ($query) {
                $user_name = '';
                if(!empty($query->first_name)) {
                    $user_name .= $query->first_name;
                }
                if(!empty($query->last_name)) {
                    $user_name .= " ".$query->last_name;
                }
                return $user_name;
            })
            ->addColumn('binning_date', function ($query) {
                $binning_date = '';
                if(!empty($query->created_at)) {
                    $binning_date = date('d M Y', strtotime($query->created_at));
                }
                return $binning_date;
            })
            ->addColumn('items', function ($query) {
                $items = '';
                return CheckInDetails::where([['order_id', '=', $query->order_id]])->sum('good_quantity');
            })
            ->rawColumns(['close_status_data', 'status_data', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}