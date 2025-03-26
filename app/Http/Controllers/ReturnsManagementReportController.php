<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\Clients;

class ReturnsManagementReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/returns_management_report")->with([
            'customerData' => Clients::select('client_id', 'customer_name')->where([['delete_status', '=', 0]])->orderBy('customer_name', 'ASC')->get()->toArray()
            ]);
    }
    public function returns_management_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('returns as r');
            $query->join('sale_order as s', 's.sale_order_id', '=', 'r.sale_order_id', 'left');
            $query->join('clients as c', 'c.client_id', '=', 's.client_id', 'left');
            $query->select('r.delivery_id', 'r.return_type', 'r.return_date', 'r.sale_order_id', 'c.customer_name');
            if($keyword)
            {
                $sql = "return_date like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('r.return_date', 'asc');
                else
                    $query->orderBy('r.return_id', 'desc');
            }
            else
            {
                $query->orderBy('r.return_id', 'DESC');
            }
            
            if(!empty($request->filter_order_id)) {
                $query->where([['r.sale_order_id', 'like', '%'.$request->filter_order_id.'%']]);
            }
            
            if(!empty($request->filter_customer)) {
                $query->where([['s.client_id', '=', $request->filter_customer]]);
            }
            
            $query->where([['r.status', '=', '1']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('return_type', function ($query) {
                $return_type = '';
                if(!empty($query->sale_order_id)) {
                    $return_type = "Customer Return";
                }else {
                    $return_type = "Supplier Return";
                }
                return $return_type;
            })
            ->addColumn('delivery_id', function ($query) {
                $delivery_id = '';
                if(!empty($query->sale_order_id)) {
                    $delivery_id = "#".$query->sale_order_id;
                }else {
                    $delivery_id = "#".$query->purchase_order_id;
                }
                return $delivery_id;
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