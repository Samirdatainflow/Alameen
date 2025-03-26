<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\ProductTaxes;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductTaxController extends Controller {

    public function product_tax() {
        return \View::make("backend/config/product_tax")->with(array());
    }
    // Product tax Modal
    public function add_product_tax(){
    	return \View::make("backend/config/product_tax_form")->with([
            'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
        ])->render();
    }
    // Insert/ Update
    public function save_product_tax(Request $request){
        if(!empty($request->hidden_id)) {
            $saveData=ProductTaxes::where('tax_id', $request->hidden_id)->update(array('tax_name'=>$request->tax_name,'tax_rate'=>$request->tax_rate, 'tax_type'=>$request->tax_type, 'tax_description'=>$request->tax_description, 'warehouse_id'=>$request->warehouse_id));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Product Tax Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => " Product Tax Update failed! Something is wrong."];
            }
        }else {
            $selectData=ProductTaxes::where([['tax_name', '=', $request->tax_name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Tax Name already exist. Please try with another Tax Name."];
            }else {
            	$data = new ProductTaxes;
            	$data->tax_name = $request->tax_name;
            	$data->tax_rate = $request->tax_rate;
            	$data->tax_type = $request->tax_type;
            	$data->tax_description = $request->tax_description;
            	$data->warehouse_id = $request->warehouse_id;
                $data->status = '0';
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " Product Tax Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Product Tax Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // dataTable
    public function list_Product_Tax(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_product_taxes');
            $query->select('*');
            if($keyword)
            {
                $sql = "tax_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('tax_name', 'asc');
                else
                    $query->orderBy('tax_id', 'desc');
            }
            else
            {
                $query->orderBy('tax_id', 'DESC');
            }
            $query->where([['status', '=', '0']]);
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
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-product-tax" data-id="'.$query->tax_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Product Tax"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-product-tax" data-id="'.$query->tax_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Product Tax"><i class="fa fa-trash"></i></button></a>';
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
    public function delete_product_tax(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ProductTaxes::where('tax_id', $request->id)->update(['status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //Edit 
    public function edit_product_tax(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/product_tax_form')->with([
                'product_tax_data' => ProductTaxes::where([['tax_id', '=', $request->id]])->get()->toArray(),
                'warehouse_id' => Warehouses::where('status',1)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function product_tax_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/product_tax_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $tax_name_exist = 0;
                    $ProductTaxes = ProductTaxes::where([['tax_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($ProductTaxes) > 0) {
                        $tax_name_exist = 1;
                    }
                    $warehouse_name = 0;
                    $Warehouses = Warehouses::where([['warehouse_id', '=', $row[5]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $warehouse_name = $Warehouses[0]['name'];
                    }
                    array_push($data, array('tax_name_exist' => $tax_name_exist, 'tax_name' => $row[1], 'tax_rate' => $row[2], 'tax_type' => $row[3], 'tax_description' => $row[4], 'warehouse_name' => $warehouse_name, 'warehouse_id' => $row[5]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_product_tax_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['tax_name_exist'] == 0 && $data['warehouse_name'] != "") {
                $pdata = new ProductTaxes;
                $pdata->tax_name = $data['tax_name'];
                $pdata->tax_rate = $data['tax_rate'];
                $pdata->tax_type = $data['tax_type'];
                $pdata->tax_description = $data['tax_description'];
                $pdata->warehouse_id = $data['warehouse_id'];
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
                    $tax_name_exist = 0;
                    $ProductTaxes = ProductTaxes::where([['tax_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($ProductTaxes) > 0) {
                        $tax_name_exist = 1;
                    }
                    $warehouse_name = 0;
                    $Warehouses = Warehouses::where([['warehouse_id', '=', $row[5]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $warehouse_name = $Warehouses[0]['name'];
                    }
                    array_push($data, array('tax_name_exist' => $tax_name_exist, 'tax_name' => $row[1], 'tax_rate' => $row[2], 'tax_type' => $row[3], 'tax_description' => $row[4], 'warehouse_name' => $warehouse_name, 'warehouse_id' => $row[5]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function product_tax_export(){
        $query = ProductTaxes::OrderBy('tax_id', 'ASC')->where([['status', '!=', '1']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Tax Name');
        $sheet->setCellValue('B1', 'Tax Rate');
        $sheet->setCellValue('C1', 'Tax Type');
        $sheet->setCellValue('D1', 'Tax Description');
        $sheet->setCellValue('E1', 'Warehouse Name');
        $rows = 2;
        foreach($query as $td){
            $warehouse_name = "";
            if(!empty($td['warehouse_id'])) {
                $Warehouses = Warehouses::select('name')->where([['warehouse_id', '=', $td['warehouse_id']]])->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    $warehouse_name = $Warehouses[0]['name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['tax_name']);
            $sheet->setCellValue('B' . $rows, $td['tax_rate']);
            $sheet->setCellValue('C' . $rows, $td['tax_type']);
            $sheet->setCellValue('D' . $rows, $td['tax_description']);
            $sheet->setCellValue('E' . $rows, $warehouse_name);
            $rows++;
        }
        $fileName = "product_taxes.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}