<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\OrderDetail;
use App\CheckInDetails;

class ReOrderController extends Controller {

    public function re_order_view() {
        return \View::make("backend/re_order/re_order_view")->with([]);
    }
    
    public function list_re_order(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            //$stock_status = $request->input('stock_status');
            $keyword = $request->input('search.value');
            $query = DB::table('products as p');
            $query->select('p.*', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            if($keyword) {
                $query->whereRaw("(pn.part_name like '%$keyword%' or replace(p.pmpno, '-','') like '%$keyword%' or p.pmpno like '%$keyword%' or p.pmpno like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('product_id', 'asc');
                else
                    $query->orderBy('product_id', 'desc');
            }else {
                $query->orderBy('product_id', 'DESC');
            }
            
            if(!empty($request->filter_part_no)) {
                $query->whereRaw('(replace(p.pmpno, "-","") LIKE "%'.$request->filter_part_no.'%" or p.pmpno like "%'.$request->filter_part_no.'%")');
            }
            
            if(!empty($request->filter_part_name)) {
                $query->where([['p.part_name_id', '=', $request->filter_part_name]]);
            }
            $query->where([['is_deleted', '=', '0'], ['current_stock', '=', 0]]);
            $datatable_array=Datatables::of($query)
            ->addColumn('mad', function ($query) {
                $mad = $this->calculateMAD($query->product_id);
                return $mad;
            })
            ->addColumn('transit_quantity', function ($query) {
                $transit_quantity = OrderDetail::where([['product_id', '=', $query->product_id]])->sum('qty');
                $CheckInDetails = CheckInDetails::where([['product_id', '=', $query->product_id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($CheckInDetails) > 0) {
                    $transit_quantity = 0;
                }
                return $transit_quantity;
            })
            ->addColumn('reorder', function ($query) {
                $reorder = "";
                $LeadTime = "";
                $orderDate = "";
                $deliveryDate = "";
                $datediff = 0;
                $qry1 = DB::table('order_detail as od')->select('o.datetime');
                $qry1->join('orders as o', 'o.order_id', '=', 'od.order_id', 'left');
                $qry1->where([['od.product_id', '=', $query->product_id]]);
                $selectOrderDate = $qry1->get()->toArray();
                if(sizeof($selectOrderDate) > 0) {
                    $orderDate = $selectOrderDate[0]->datetime;
                }
                $selectOrderCheckin = DB::table('check_in_details')->select('created_at')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                if(sizeof($selectOrderCheckin) > 0) {
                    $deliveryDate = $selectOrderCheckin[0]->created_at;
                }
                if(!empty($deliveryDate) && !empty($orderDate)) {
                    $deliveryDate = date('Y-m-d', strtotime($deliveryDate));
                    $orderDate = date('Y-m-d', strtotime($orderDate));
                    $datediff = strtotime($deliveryDate) - strtotime($orderDate);
                    $datediff = round($datediff / (60 * 60 * 24));
                    $mad = $this->calculateMAD($query->product_id);
                    $transit_quantity = OrderDetail::where([['product_id', '=', $query->product_id]])->sum('qty');
                    $CheckInDetails = CheckInDetails::where([['product_id', '=', $query->product_id], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($CheckInDetails) > 0) {
                        $transit_quantity = 0;
                    }
                    $reorder = (($mad/30)*$datediff) - $transit_quantity;
                    if($reorder > 0 && $reorder < 1) {
                        $reorder = 1;
                    }else {
                        $reorder = round($reorder,1);
                    }
                }

                //$LeadTime = round($datediff / (60 * 60 * 24));
                //$reorder = $deliveryDate." - ".$orderDate;
                //$reorder = $datediff;
                return $reorder;
            })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    public function calculateMAD($product_id) {
        $mad = 0;
        
        $today = date('Y-m-d',strtotime("-1 days"));
        $fromday = date('Y-m-d',strtotime("-365 days"));
        
        $query = DB::table('sale_order_details as sod');
        $query->select(DB::raw("SUM(sod.qty_appr) as total_qty"));
        $query->where([['product_id', '=', $product_id]]);
        $query->join('sale_order as so', 'so.sale_order_id', '=', 'sod.sale_order_id', 'left');
        $query->whereRaw('DATE_FORMAT(so.created_at,"%Y-%m-%d") BETWEEN "'.$fromday.'" AND "'.$today.'"');
        $selectData = $query->get()->toArray();
        if(sizeof($selectData) > 0) {
            if($selectData[0]->total_qty > 0) $mad = $selectData[0]->total_qty;
        }
        $cMad = 0;
        if($mad > 0) {
            $cMad = $mad/12;
            if($cMad > 0 && $cMad < 1) {
                $cMad = 1;
            }else {
                $cMad = round($cMad,1);
            }
        }
        
        return $cMad;
    }
}