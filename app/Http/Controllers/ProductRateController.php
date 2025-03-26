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
use App\WmsProductRate;
use DB;
use DataTables;
use App\Helpers\Helper;
use App\PartName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductRateController extends Controller {

    public function product_rate() {
        return \View::make("backend/config/product_rate")->with(array());
    }
    // Product Rate Modal
    public function add_product_rate(){
    	return \View::make("backend/config/product_rate_form")->with([
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            // 'product_id' => Products::where('is_deleted',1)->get()->toArray()
        ])->render();
    }
    public function get_product_name(Request $request){
        $returnData = [];
        if ($request->ajax()) {
            $returnData = Helper::search_product_list($request->search_key);
            
        }
        return response()->json($returnData);
    }
    // Insert/ Update
    public function save_product_rate(Request $request){
        if(!empty($request->hidden_id)) {
            $saveData=WmsProductRate::where('rate_id', $request->hidden_id)->update(array('default_rate'=>$request->default_rate,'level_1'=>$request->level_1, 'level_2'=>$request->level_2, 'level_3'=>$request->level_3, 'level_4'=>$request->level_4, 'level_5'=>$request->level_5, 'warehouse_id'=>$request->warehouse_id, 'product_id'=>$request->product_id));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Product Rate Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => " Product Rate Update failed! Something is wrong."];
            }
        }else {
            $selectData=WmsProductRate::where([['default_rate', '=', $request->default_rate]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Default Rate already exist. Please try with another Default Rate."];
            }else {
            	$data = new WmsProductRate;
            	$data->default_rate = $request->default_rate;
            	$data->level_1 = $request->level_1;
            	$data->level_2 = $request->level_2;
            	$data->level_3 = $request->level_3;
            	$data->level_4 = $request->level_4;
            	$data->level_5 = $request->level_5;
            	$data->warehouse_id = $request->warehouse_id;
            	$data->product_id = $request->product_id;
                $data->status = "0";
                //print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " Product Rate Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Product Rate Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // dataTable
    public function list_product_rate(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_product_rate');
            $query->select('*');
            $query->where([['status', '=', '0']]);
            if($keyword)
            {
                $sql = "default_rate like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('default_rate', 'asc');
                else
                    $query->orderBy('rate_id', 'desc');
            }
            else
            {
                $query->orderBy('rate_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
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
            ->addColumn('product_id', function ($query) {
                $product_id = '';
                if(!empty($query->product_id)) {
                    $selectProduct = Products::select('part_name_id')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                    if(count($selectProduct) > 0) {
                        if(!empty($selectProduct[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $selectProduct[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                            if(!empty($PartName)) {
                                if(!empty($PartName[0]['part_name'])) $product_id = $PartName[0]['part_name'];
                            }
                        }
                    }
                }
                return $product_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-product-rate" data-id="'.$query->rate_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Product Rate"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-product-rate" data-id="'.$query->rate_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Product Rate"><i class="fa fa-trash"></i></button></a>';
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
    //Delete
    public function delete_product_rate(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = WmsProductRate::where('rate_id', $request->id)->update(['status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //Edit 
    public function edit_product_rate(Request $request) {
        if ($request->ajax()) {
        	$dataParoductRate = WmsProductRate::where([['rate_id', '=', $request->id]])->get()->toArray();
            //echo $dataParoductRate[0]['product_id']; exit();
            $query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->where([['p.product_id', '=', $dataParoductRate[0]['product_id']], ['p.is_deleted', '=', '0']]);
            $product_data = $query->get()->toArray();
            //print_r($product_data); exit();
        	//$product_data = Products::where('is_deleted',0)->where('product_id',$dataParoductRate[0]['product_id'])->select('part_name','pmpno','product_id')->get()->toArray();
            $html = view('backend/config/product_rate_form')->with([
                'product_rate_data' => $dataParoductRate,
                'warehouse_id' => Warehouses::where('status',1)->get()->toArray(),
            	'product_data' => $product_data
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function product_rate_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/product_rate_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
    }
    function csvToArrayWithAll($filename = '', $supplier = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $default_rate_exist = 0;
                    $WmsProductRate = WmsProductRate::where([['default_rate', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($WmsProductRate) > 0) {
                        $default_rate_exist = 1;
                    }
                    $warehouse_name = 0;
                    $Warehouses = Warehouses::where([['warehouse_id', '=', $row[7]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $warehouse_name = $Warehouses[0]['name'];
                    }
                    $product_name = "";
                    $product_id = "";
                    $Products = DB::table('products as p')->select('p.product_id', 'p.pmpno', 'pn.part_name')->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left')->where('p.pmpno', 'like', '%'.$row[2].'%')->orWhere('pn.part_name', 'like', '%'.$row[8].'%')->get()->toArray();
                    if(sizeof($Products) > 0) {
                        $product_id .= $Products[0]->product_id;
                        if(!empty($Products[0]->part_name)) {
                            $product_name .= $Products[0]->part_name;
                        }
                        if(!empty($Products[0]->pmpno)) {
                            $product_name .= " (".$Products[0]->pmpno.")";
                        }
                    }
                    array_push($data, array('default_rate_exist' => $default_rate_exist, 'default_rate' => $row[1], 'level_1' => $row[2], 'level_2' => $row[3], 'level_3' => $row[4], 'level_4' => $row[5], 'level_5' => $row[6], 'warehouse_name' => $warehouse_name, 'warehouse_id' => $row[7], 'product_name' => $product_name, 'product_id' => $product_id, 'part_no' => $row[8]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_product_rate_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['default_rate_exist'] == 0 && $data['warehouse_name'] != "" && $data['product_name'] != "") {
                $pdata = new WmsProductRate;
                $pdata->default_rate = $data['default_rate'];
                $pdata->level_1 = $data['level_1'];
                $pdata->level_2 = $data['level_2'];
                $pdata->level_3 = $data['level_3'];
                $pdata->level_4 = $data['level_4'];
                $pdata->level_5 = $data['level_5'];
                $pdata->warehouse_id = $data['warehouse_id'];
                $pdata->product_id = $data['product_id'];
                $pdata->status = 0;
                $pdata->save();
            }
            $flag++;
        }
        if($flag == sizeof($productArr['data'])) {
            $returnData = ["status" => 1, "msg" => "Save successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Something is wrong."];
        }
        return response()->json($returnData);
    }
    function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $default_rate_exist = 0;
                    $WmsProductRate = WmsProductRate::where([['default_rate', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($WmsProductRate) > 0) {
                        $default_rate_exist = 1;
                    }
                    $warehouse_name = 0;
                    $Warehouses = Warehouses::where([['warehouse_id', '=', $row[7]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $warehouse_name = $Warehouses[0]['name'];
                    }
                    $product_name = "";
                    $product_id = "";
                    $Products = DB::table('products as p')->select('p.product_id', 'p.pmpno', 'pn.part_name')->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left')->where('p.pmpno', 'like', '%'.$row[2].'%')->orWhere('pn.part_name', 'like', '%'.$row[8].'%')->get()->toArray();
                    if(sizeof($Products) > 0) {
                        $product_id .= $Products[0]->product_id;
                        if(!empty($Products[0]->part_name)) {
                            $product_name .= $Products[0]->part_name;
                        }
                        if(!empty($Products[0]->pmpno)) {
                            $product_name .= " (".$Products[0]->pmpno.")";
                        }
                    }
                    array_push($data, array('default_rate_exist' => $default_rate_exist, 'default_rate' => $row[1], 'level_1' => $row[2], 'level_2' => $row[3], 'level_3' => $row[4], 'level_4' => $row[5], 'level_5' => $row[6], 'warehouse_name' => $warehouse_name, 'warehouse_id' => $row[7], 'product_name' => $product_name, 'product_id' => $product_id, 'part_no' => $row[8]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function product_rate_export(){
        $query = WmsProductRate::OrderBy('rate_id', 'DESC')->where([['status', '!=', '1']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Default Rate');
        $sheet->setCellValue('B1', 'Lavel 1');
        $sheet->setCellValue('C1', 'Lavel 2');
        $sheet->setCellValue('D1', 'Lavel 3');
        $sheet->setCellValue('E1', 'Lavel 4');
        $sheet->setCellValue('F1', 'Lavel 5');
        $sheet->setCellValue('G1', 'Warehouse Name');
        $sheet->setCellValue('H1', 'Product Name');
        $rows = 2;
        foreach($query as $td){
            $warehouse_name = "";
            if(!empty($td['warehouse_id'])) {
                $Warehouses = Warehouses::select('name')->where([['warehouse_id', '=', $td['warehouse_id']]])->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    $warehouse_name = $Warehouses[0]['name'];
                }
            }
            $product_name = '';
            if(!empty($td['product_id'])) {
                $Products = Products::select('part_name_id')->where([['product_id', '=', $td['product_id']]])->get()->toArray();
                if(count($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $product_name = $PartName[0]['part_name'];
                        }
                    }
                }
            }
            $sheet->setCellValue('A' . $rows, $td['default_rate']);
            $sheet->setCellValue('B' . $rows, $td['level_1']);
            $sheet->setCellValue('C' . $rows, $td['level_2']);
            $sheet->setCellValue('D' . $rows, $td['level_3']);
            $sheet->setCellValue('E' . $rows, $td['level_4']);
            $sheet->setCellValue('F' . $rows, $td['level_5']);
            $sheet->setCellValue('G' . $rows, $warehouse_name);
            $sheet->setCellValue('H' . $rows, $product_name);
            $rows++;
        }
        $fileName = "product_rate.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}