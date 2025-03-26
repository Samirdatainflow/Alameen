<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
// use App\Users;
use App\Warehouses;
use App\DeliveryMethod;
// use App\Products;
// use App\Orders;
// use App\OrderDetail;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DeliveryMethodController extends Controller {

	// Delivery Method View
    public function index() {
        return \View::make("backend/config/delivery_method")->with(array());
    }
    // List Delivery Method
    public function list_delivery_method(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('delivery_method as d');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'd.warehouseid');
            $query->select('d.delivery_method_id', 'd.delivery_method','d.delivery_description', 'w.name as warehouse_name');
            $query->where([['d.status', '!=', '1']]);
            if($keyword)
            {
                $sql = "w.name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('d.delivery_method_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-delivery-method" data-id="'.$query->delivery_method_id.'"><button type="button" class="btn btn-success btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-delivery-method" data-id="'.$query->delivery_method_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    // Delivery Method Form
    public function add_delivery_method(Request $request){
        if ($request->ajax()) {
            $html = view('backend.config.delivery_method_form')->with([
                'warehouses_data' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Save Delivery Method
    public function save_delivery_method(Request $request) {
        $returnData = [];
        if(!empty($request->hidden_id)) {
        	$saveData = DeliveryMethod::where([['delivery_method_id', '=', $request->hidden_id]])->update(['delivery_method' => $request->delivery_method, 'delivery_description' => $request->delivery_description, 'warehouseid' => $request->warehouseid, 'modified_date' => date('Y-m-d')]);
        	if($saveData) {
	           $returnData = ["status" => 1, "msg" => "Update successful."];
	        }else {
	            $returnData = ["status" => 0, "msg" => "Update faild. Something is wrong, please try again."];
	        }
        }else {
	        $data = new DeliveryMethod;
	        $data->delivery_method = $request->delivery_method;
	        $data->delivery_description = $request->delivery_description;
	        $data->status = "0";
	        $data->warehouseid = $request->warehouseid;
	        $saveData = $data->save();
	        if($saveData) {
	           $returnData = ["status" => 1, "msg" => "Save successful."];
	        }else {
	            $returnData = ["status" => 0, "msg" => "Save faild."];
	        }
	    }
        return response()->json($returnData);
    }
    // Edit Delivery Method Form
    public function edit_delivery_method(Request $request){
        if ($request->ajax()) {
            $html = view('backend.config.delivery_method_form')->with([
                'delivery_method_data' => DeliveryMethod::where([['delivery_method_id', '=', $request->id]])->get()->toArray(),
                'warehouses_data' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Delivery Method Form
    public function delete_delivery_method(Request $request) {
        $returnData = [];
        $upData = DeliveryMethod::where('delivery_method_id', $request->id)->update(['status' => "1"]);
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    public function delivery_method_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/delivery_method_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $delivery_method_exist = 0;
                    $DeliveryMethod = DeliveryMethod::where([['delivery_method', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($DeliveryMethod) > 0) {
                        $delivery_method_exist = 1;
                    }
                    $warehouse_name = "";
                    $Warehouses = Warehouses::where([['warehouse_id', '=', $row[3]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        if(!empty($Warehouses[0]['name'])) $warehouse_name = $Warehouses[0]['name'];
                    }
                    array_push($data, array('delivery_method' => $row[1], 'delivery_description' => $row[2], 'delivery_method_exist' => $delivery_method_exist, 'warehouse_name' => $warehouse_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_delivery_method_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['delivery_method'] != "") {
                $pdata = new DeliveryMethod;
                $pdata->delivery_method = $data['delivery_method'];
                $pdata->delivery_description = $data['delivery_description'];
                $pdata->warehouseid = $data['warehouseid'];
                $pdata->status = "0";
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
                    $delivery_method = "";
                    $selectData = DeliveryMethod::where([['delivery_method', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $delivery_method = "";
                    }else {
                        $delivery_method = $row[1];
                    }
                    array_push($data, array('delivery_method' => $delivery_method, 'delivery_description' => $row[2], 'warehouseid' => $row[3]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function delivery_method_export(){
        $query = DeliveryMethod::where([['status', '!=', 1]])->OrderBy('delivery_method_id', 'ASC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Delivery Method');
        $sheet->setCellValue('B1', 'Delivery Description');
        $sheet->setCellValue('C1', 'Warehouse');
        $rows = 2;
        foreach($query as $td){
            $warehouse_name = "";
            if(!empty($td['warehouseid'])) {
                $Warehouses = Warehouses::select('name')->where([['warehouse_id', '=', $td['warehouseid']]])->get()->toArray();
                if(sizeof($Warehouses) > 0) {
                    $warehouse_name = $Warehouses[0]['name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['delivery_method']);
            $sheet->setCellValue('B' . $rows, $td['delivery_description']);
            $sheet->setCellValue('C' . $rows, $warehouse_name);
            $rows++;
        }
        $fileName = "delivery_method_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}