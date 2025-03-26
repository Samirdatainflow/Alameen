<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\WmsClass;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ConfigClassController extends Controller {
    public function config_class() {
        return \View::make("backend/config/config_class")->with(array());
    }
    // Modal View
    public function add_config_class(){
        return \View::make("backend/config/class_form")->with([
        ])->render();
    }
    // Insert/Update
    public function save_config_class(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=WmsClass::where([['value', '=', $request->value], ['description', '=', $request->description], ['class_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Value already exist. Please try with another Value."];
            }else {
                $saveData=WmsClass::where('class_id', $request->hidden_id)->update(array('value'=>$request->value,'description'=>$request->description));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Class Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Class Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=WmsClass::where([['value', '=', $request->value]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Value already exist. Please try with another Value."];
            }else {
            	$data = new WmsClass;
            	$data->value = $request->value;
            	$data->description = $request->description;
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Class Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Class Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
     // Class dataTable
    public function list_config_class(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_class');
            $query->select('*');
            if($keyword)
            {
                $sql = "description like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('description', 'asc');
                else
                    $query->orderBy('description', 'desc');
            }
            else
            {
                $query->orderBy('class_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-class" data-id="'.$query->class_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Class"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-class" data-id="'.$query->class_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Class"><i class="fa fa-trash"></i></button></a>';
                return $action;
            })
            ->rawColumns(['status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Export
    public function config_class_export()
    {
        
        $query = DB::table('wms_class')
        ->select('*')
        ->orderBy('class_id', 'DESC');
        $data = $query->get()->toArray();
           
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Class_value');
        $sheet->setCellValue('B1', 'Class_description');

        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->value);
            $sheet->setCellValue('B' . $rows, $empDetails->description);
            $rows++;
        }
        $fileName = "Class.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Class Edit
    public function edit_config_class(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.class_form')->with([
                'class_data' => WmsClass::where([['class_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Class Delete
    public function delete_config_class(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = WmsClass::where('class_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function class_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/class_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $class_value_exist = 0;
                    $WmsClass = WmsClass::where([['value', '=', $row[1]]])->get()->toArray();
                    if(count($WmsClass) > 0) {
                        $class_value_exist = 1;
                    }
                    array_push($data, array('class_value_exist' => $class_value_exist, 'class_value' => $row[1], 'description' => $row[2]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_class_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['class_value_exist'] == 0) {
                $pdata = new WmsClass;
                $pdata->value = $data['class_value'];
                $pdata->description = $data['description'];
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
                    $class_value_exist = 0;
                    $WmsClass = WmsClass::where([['value', '=', $row[1]]])->get()->toArray();
                    if(count($WmsClass) > 0) {
                        $class_value_exist = 1;
                    }
                    array_push($data, array('class_value_exist' => $class_value_exist, 'class_value' => $row[1], 'description' => $row[2]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}