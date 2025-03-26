<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\WmsUnit;
use App\Products;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnitsController extends Controller {

    public function units() {
        return \View::make("backend/config/unit")->with(array());
    }
    // Units Modal
    public function add_units(){
    	return \View::make("backend/config/units_form")->with(array());
    }
    // Insert/ Update
    public function save_config_unit(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=WmsUnit::where([['unit_name', '=', $request->unit_name], ['unit_type', '=', $request->unit_type], ['base_factor', '=', $request->base_factor], ['base_measurement_unit', '=', $request->base_measurement_unit], ['unit_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Units name already exist. Please try with another Units name."];
            }else {
                $saveData=WmsUnit::where('unit_id', $request->hidden_id)->update(array('unit_name'=>$request->unit_name,'unit_type'=>$request->unit_type, 'base_factor'=>$request->base_factor, 'base_measurement_unit'=>$request->base_measurement_unit));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Units Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Units Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=WmsUnit::where([['unit_name', '=', $request->unit_name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Units name already exist. Please try with another Units name."];
            }else {
            	$data = new WmsUnit;
            	$data->unit_name = $request->unit_name;
            	$data->unit_type = $request->unit_type;
            	$data->base_factor = $request->base_factor;
            	$data->base_measurement_unit = $request->base_measurement_unit;
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Units Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Units Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Config Units dataTable
    public function list_config_units(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_units');
            $query->select('*');
            if($keyword)
            {
                $sql = "unit_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('unit_name', 'asc');
                else
                    $query->orderBy('unit_id', 'desc');
            }
            else
            {
                $query->orderBy('unit_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Products = Products::select('unit')->where([['unit', '=', $query->unit_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-units" data-id="'.$query->unit_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Units"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-units" data-id="'.$query->unit_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Units"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-units" data-id="'.$query->unit_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Units"><i class="fa fa-trash"></i></button></a>';
                }
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
    // Units Edit
    public function edit_config_units(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.units_form')->with([
                'units_data' => WmsUnit::where([['unit_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Units Delete
    public function delete_config_unit(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = WmsUnit::where('unit_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function unit_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/unit_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $unit_name_exist = 0;
                    $WmsUnit = WmsUnit::where([['unit_name', '=', $row[0]]])->get()->toArray();
                    if(count($WmsUnit) > 0) {
                        $unit_name_exist = 1;
                    }
                    array_push($data, array('unit_name_exist' => $unit_name_exist, 'unit_name' => $row[0], 'unit_type' => $row[1], 'base_factor' => $row[2], 'base_measurement_unit' => $row[3]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_unit_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['unit_name_exist'] == 0) {
                $pdata = new WmsUnit;
                $pdata->unit_name = $data['unit_name'];
                $pdata->unit_type = $data['unit_type'];
                $pdata->base_factor = $data['base_factor'];
                $pdata->base_measurement_unit = $data['base_measurement_unit'];
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
                    $unit_name_exist = 0;
                    $WmsUnit = WmsUnit::where([['unit_name', '=', $row[0]]])->get()->toArray();
                    if(count($WmsUnit) > 0) {
                        $unit_name_exist = 1;
                    }
                    array_push($data, array('unit_name_exist' => $unit_name_exist, 'unit_name' => $row[0], 'unit_type' => $row[1], 'base_factor' => $row[2], 'base_measurement_unit' => $row[3]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function units_export(){
        $query = WmsUnit::OrderBy('unit_id', 'DESC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Unit Name');
        $sheet->setCellValue('B1', 'Unit Type');
        $sheet->setCellValue('C1', 'Base Factor');
        $sheet->setCellValue('D1', 'Base Measurment Unit');
        $rows = 2;
        foreach($query as $td){
            $sheet->setCellValue('A' . $rows, $td['unit_name']);
            $sheet->setCellValue('B' . $rows, $td['unit_type']);
            $sheet->setCellValue('C' . $rows, $td['base_factor']);
            $sheet->setCellValue('D' . $rows, $td['base_measurement_unit']);
            $rows++;
        }
        $fileName = "units.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}