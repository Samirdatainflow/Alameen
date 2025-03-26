<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Products;
use App\WmsLots;
use App\Warehouses;
use App\PartName;
use DB;
use DataTables;

class StockManagementReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/stock_management_report")->with([
            'Warehouses' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->orderBy('warehouse_id', 'desc')->get()->toArray()
        ]);
    }
    public function stock_management_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            // $query = DB::table('wms_stock');
            // $query->select('*');
            $query = DB::table('wms_stock as ws');
            $query->select('ws.*', 'p.pmpno', 'p.part_name_id', 'pn.part_name', 'w.name as warehouse_name');
            $query->join('products as p', 'p.product_id', '=', 'ws.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'ws.warehouse_id', 'left');
            if($keyword) {
                $query->whereRaw("(replace(p.pmpno, '-','') like '%$keyword%' or pn.part_name like '%$keyword%' or w.name like '%$keyword%' or ws.product_id like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('ws.lot_name', 'asc');
                else
                    $query->orderBy('ws.lot_name', 'desc');
            }else {
                $query->orderBy('ws.stock_id', 'DESC');
            }
            if(!empty($request->filter_part_no)) {
                $query->whereRaw('(replace(p.pmpno, "-","") LIKE "%'.$request->filter_part_no.'%")');
            }
            if(!empty($request->filter_warehouse)) {
                $query->where('w.warehouse_id', $request->filter_warehouse);
            }
            if(!empty($request->filter_from_date) && !empty($request->filter_to_date)) {
                $from_date = date('Y-m-d', strtotime($request->filter_from_date));
                $to_date = date('Y-m-d', strtotime($request->filter_to_date));
                $query->where(DB::raw("(STR_TO_DATE(ws.created_date,'%Y-%m-%d'))"), ">=", $from_date);
                $query->where(DB::raw("(STR_TO_DATE(ws.created_date,'%Y-%m-%d'))"), "<=", $to_date);
            }
            $query->where([['ws.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('created_date', function ($query) {
                $created_date = '';
                if(!empty($query->created_date)) {
                    $created_date .= date("d M Y",strtotime($query->created_date));
                }
                return $created_date;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function top_5_stocks_in_warehouse() {
        return \View::make("backend/reports/top_5_stocks_in_warehouse")->with([]);
    }
    public function top_5_stocks_in_warehouse_list(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::select('SELECT wms_stock.product_id, wms_stock.warehouse_id,sum(wms_stock.qty) AS product_qtys FROM wms_stock GROUP BY product_id, warehouse_id ORDER By product_qtys desc limit 5 offset 0');
            if(sizeof($query) > 0) {
                foreach($query as $val) {
                    $pmpno = "";
                    $warehouse_id = "";
                    $part_name = "";
                    $Products = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $val->product_id]])->get()->toArray();
                    if(sizeof($Products)>0){
                        $pmpno = $Products[0]['pmpno'];
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName)>0){
                            $part_name = $PartName[0]['part_name'];
                        }
                    }
                    array_push($arrayData, array('product_id' => $val->product_id, 'pmpno' => $pmpno, 'warehouse_id' => $val->warehouse_id, 'part_name' => $part_name, 'product_qtys' => $val->product_qtys));
                }
            }
            //print_r($arrayData); exit();
            $datatable_array=Datatables::of($arrayData)
            ->addColumn('warehouse_name', function ($query) {
                $warehouse_name = '';
                $Warehouses = Warehouses::select('name')->where('warehouse_id', $query['warehouse_id'])->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    $warehouse_name .= $Warehouses[0]['name'];
                }
                return $warehouse_name;
            })
            ->rawColumns(['warehouse_name'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}