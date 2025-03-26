<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Products;
use App\WmsStock;
use App\WmsUnit;
use App\WmsLots;
use App\WmsUnitLoads;
use App\Location;
use DB;
use DataTables;
use App\PartName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockController extends Controller {
    // Stock 
    public function stock() {
        return \View::make("backend/stock/stock")->with(array());
    }
    // Stock Modal
    public function add_stock(){
        return \View::make("backend/stock/stock_form")->with([
            // 'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            'PartName' => PartName::where('status',1)->get()->toArray(),
            'lot_id' => WmsLots::where('status',1)->get()->toArray(),
            'unit_load_id' => WmsUnitLoads::where('status',1)->get()->toArray(),
            'unit_id' => WmsUnit::get()->toArray(),
            'location_id' => Location::where('is_deleted',0)->get()->toArray()
        ])->render();
    }
    public function get_product_details(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $productData = Products::where([['pmpno', '=', $request->pmpno]])->whereRaw('FIND_IN_SET(",",warehouse_id) = 0')->get()->toArray();
            // print_r($prodectData); exit();
            if(sizeof($productData) > 0) {
                $returnData = ["status" => 1, "msg" => "success.","data"=>$productData];
            }else {
                $returnData = ["status" => 0, "msg" => "Part No/Product is not found"];
            }
        }
        return response()->json($returnData);
    }
    public function warehouse_list_by_location(Request $request){
        $location_id = $request->location_id;
        $warehouse =  Location::where([['location_id', '=',$location_id], ['is_deleted','=','0']])->get()->toArray();
        $warehouse =  Warehouses::where([['warehouse_id', '=',$warehouse]])->get()->toArray();
        return response()->json($warehouse);
    }
    // Insert/ Update
     public function save_stock(Request $request){
        	$data = new WmsStock;
        	$data->stock_units = $request->stock_units;
        	// $data->part_no = $request->part_no;
        	// $data->part_name = $request->part_name;
        	$data->lot_name = $request->lot_name;
        	$data->unit_load = $request->unit_load;
        	$data->location_id = $request->location_id;
        	$data->qty = $request->qty;
        	$data->warehouse_id = $request->warehouse_id;
        	$data->product_id = $request->product_id;
        	$data->reserved_qty = $request->reserved_qty;
            $data->status = '1';
            // print_r($data); exit();
            $saveData= $data->save();
            $saveData = Products::where('product_id',$request->product_id)->update(array(
            'current_stock' => DB::raw('current_stock + '.$request->qty), 'part_name_id' => $request->product_name,
            ));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Stock Save successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Stock Save failed! Something is wrong."];
            }
            
            // if($saveData) {
            //     $returnData = ["status" => 1, "msg" => " Stock Save successful."];
            // }else {
            //     $returnData = ["status" => 0, "msg" => "Stock Save failed! Something is wrong."];
            // }
            return response()->json($returnData);
        }
    // DataTable
    public function list_stock(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_stock');
            $query->select(DB::raw('SUM(qty) AS total_qty'), 'product_id', 'lot_name', 'warehouse_id');
            if($keyword) {
                $sql = "lot_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('lot_name', 'asc');
                else
                    $query->orderBy('lot_name', 'desc');
            }else {
                $query->orderBy('product_id', 'DESC');
            }
            $query->groupBy('product_id', 'lot_name', 'warehouse_id');
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('product_id', function ($query) {
                return $query->product_id;
            })
            ->addColumn('part_no', function ($query) {
                $part_no = '';
                if(!empty($query->product_id)) {
                    $selectWarehouse = Products::select('pmpno')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                    if(count($selectWarehouse) > 0) {
                        if(!empty($selectWarehouse[0]['pmpno'])) $part_no = $selectWarehouse[0]['pmpno'];
                    }
                }
                return $part_no;
            })
            ->addColumn('product_name', function ($query) {
                $product_name = '';
                if(!empty($query->product_id)) {
                    $Products = Products::select('part_name_id')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                    if(count($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(!empty($PartName[0]['part_name'])) $product_name = $PartName[0]['part_name'];
                        }
                    }
                }
                return $product_name;
            })
            
            ->addColumn('lot_name', function ($query) {
                $lot_name = '';
                if(!empty($query->lot_name)) {
                    $selectWarehouse = WmsLots::select('lot_name')->where([['lot_id', '=', $query->lot_name]])->get()->toArray();
                    if(count($selectWarehouse) > 0) {
                        if(!empty($selectWarehouse[0]['lot_name'])) $lot_name = $selectWarehouse[0]['lot_name'];
                    }
                }
                return $lot_name;
            })
            ->addColumn('warehouse_id', function ($query) {
                $warehouse_id = '';
                if(!empty($query->warehouse_id)) {
                    $selectWarehouse = Warehouses::select('name')->where([['warehouse_id', '=', $query->warehouse_id]])->get()->toArray();
                    if(count($selectWarehouse) > 0) {
                        if(!empty($selectWarehouse[0]['name'])) $warehouse_id = $selectWarehouse[0]['name'];
                    }
                }
                return $warehouse_id;
            })
            // ->addColumn('created_date', function ($query) {
            //     $created_date = '';
            //     if(!empty($query->created_date)) {
            //         $created_date .= date("d M Y",strtotime($query->created_date));
            //     }
            //     return $created_date;
            // })
            ->addColumn('action', function ($query) {
                $action = '';
                //$action = '<a href="javascript:void(0)" class="delete-stock" data-id="'.$query->stock_id.'"><button type="button" class="btn btn-danger btn-sm" title="Edit Stock"><i class="fa fa-trash"></i></button></a>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Stock Delete
    public function delete_stock(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $total=0;
            $saveData = WmsStock::where('stock_id', $request->id)->update(['status' => "2"]);
            $total_array  = WmsStock::where('stock_id', $request->id)->get()->toArray();
            if(sizeof($total_array))
            {
                $total = $total_array[0]['qty'];
                $saveData = Products::where('product_id',$total_array[0]['product_id'])->update(array(
                'current_stock' => DB::raw('current_stock - '.$total),
                ));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Stock Delete successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
                }
            }
            else
            {
                 $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
            
        }
        return response()->json($returnData);
    }
    public function stock_export(){
        $query = DB::table('wms_stock')->select(DB::raw('SUM(qty) AS total_qty'), 'product_id', 'lot_name', 'warehouse_id')->groupBy('product_id', 'lot_name', 'warehouse_id')->orderBy('product_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Product ID');
        $sheet->setCellValue('B1', 'Part No');
        $sheet->setCellValue('C1', 'Product Name');
        $sheet->setCellValue('D1', 'Warehouse Name');
        $sheet->setCellValue('E1', 'Lot Name');
        $sheet->setCellValue('F1', 'Quantity');
        $rows = 2;
        foreach($query as $d2){
            $part_no = '';
            $product_name = '';
            if(!empty($d2->product_id)) {
                $Products = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $d2->product_id]])->get()->toArray();
                if(count($Products) > 0) {
                    if(!empty($Products[0]['pmpno'])) $part_no = $Products[0]['pmpno'];
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(!empty($PartName[0]['part_name'])) $product_name = $PartName[0]['part_name'];
                    }
                }
            }
            $warehouse_name = "";
            $Warehouses = Warehouses::select('name')->where('warehouse_id', $d2->warehouse_id)->get()->toArray();
            if(sizeof($Warehouses) > 0) {
                $warehouse_name = $Warehouses[0]['name'];
            }
            $lot_name = '';
            if(!empty($d2->lot_name)) {
                $WmsLots = WmsLots::select('lot_name')->where([['lot_id', '=', $d2->lot_name]])->get()->toArray();
                if(count($WmsLots) > 0) {
                    if(!empty($WmsLots[0]['lot_name'])) $lot_name = $WmsLots[0]['lot_name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $d2->product_id);
            $sheet->setCellValue('B' . $rows, $part_no);
            $sheet->setCellValue('C' . $rows, $product_name);
            $sheet->setCellValue('D' . $rows, $warehouse_name);
            $sheet->setCellValue('E' . $rows, $lot_name);
            $sheet->setCellValue('F' . $rows, $d2->total_qty);
            $rows++;
        }
        $fileName = "stock.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}