<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\WmsUnit;
use App\WmsUnitLoads;
use App\Location;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnitLoadController extends Controller {

    public function unit_load() {
        return \View::make("backend/config/unit_load")->with(array());
    }
    // Unit Load Modal
    public function add_unit_load(){
        return \View::make("backend/config/unit_load_form")->with([
            'unit_id' => WmsUnit::get()->toArray(),
            'location_id' => Location::where('is_deleted',0)->get()->toArray()
        ])->render();
    }
    // Unit Load DataTAble
    public function list_unit_load(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('wms_unit_loads');
            $query->select('*');
            if($keyword)
            {
                $sql = "unit_load_type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('unit_load_type', 'asc');
                else
                    $query->orderBy('unit_load_id', 'desc');
            }
            else
            {
                $query->orderBy('unit_load_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            // ->addColumn('location_id', function ($query) {
            //     $location_id = '';
            //     if(!empty($query->location_id)) {
            //         $selectLocation = Location::select('location_name')->where([['location_id', '=', $query->location_id]])->get()->toArray();
            //         if(count($selectLocation) > 0) {
            //             if(!empty($selectLocation[0]['location_name'])) $location_id = $selectLocation[0]['location_name'];
            //         }
            //     }
            //     return $location_id;
            // })
            ->addColumn('stock_unit', function ($query) {
                $unit_id = '';
                if(!empty($query->stock_unit)) {
                    $selectUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $query->stock_unit]])->get()->toArray();
                    if(count($selectUnit) > 0) {
                        if(!empty($selectUnit[0]['unit_name'])) $unit_id = $selectUnit[0]['unit_name'];
                    }
                }
                return $unit_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-unit-load" data-id="'.$query->unit_load_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Unit Load"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-unit-load" data-id="'.$query->unit_load_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Unit Load"><i class="fa fa-trash"></i></button></a>';
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
    // Insert/Update
    public function save_unit_load(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=WmsUnitLoads::where([['unit_load_type', '=', $request->unit_load_type], ['unit_load_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Unit Load Type already exist. Please try with another Unit Load Type."];
            }else {
                $saveData=WmsUnitLoads::where('unit_load_id', $request->hidden_id)->update(['unit_load_type' => $request->unit_load_type, 'location_id' => $request->location_id, 'stock_unit' => $request->stock_unit]);
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Unit Load  Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Unit Load Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=WmsUnitLoads::where(['unit_load_type' => $request->unit_load_type])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Unit Load Type already exist. Please try with another Unit Load Type."];
            }else {
                $data = new WmsUnitLoads;
                $data->unit_load_type = $request->unit_load_type;
                $data->location_id = $request->location_id;
                $data->stock_unit = $request->stock_unit;
                $data->status = "1";
                $saveData = $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Unit Load Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Unit Load Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    //Edit
    public function edit_unit_load(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/unit_load_form')->with([
                'unit_load_data' => WmsUnitLoads::where([['unit_load_id', '=', $request->id]])->get()->toArray(),
                'unit_id' => WmsUnit::get()->toArray(),
                'location_id' => Location::where('is_deleted',0)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Unit Load Delete
    public function delete_unit_load(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = WmsUnitLoads::where('unit_load_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function unit_load_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/unit_load_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $unit_load_type_exist = 0;
                    $WmsUnitLoads = WmsUnitLoads::where([['unit_load_type', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($WmsUnitLoads) > 0) {
                        $unit_load_type_exist = 1;
                    }
                    
                    $unit_name_exist = "";
                    $WmsUnit = WmsUnit::where([['unit_name', '=', $row[1]]])->get()->toArray();
                    if(count($WmsUnit) > 0) {
                        $unit_name_exist = 1;
                    }
                    array_push($data, array('unit_load_type_exist' => $unit_load_type_exist, 'unit_load_type' => $row[0], 'unit_name' => $row[1], 'unit_name_exist' => $unit_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_unit_load_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['unit_load_type_exist'] == 0 && $data['unit_name_exist'] != "") {
                $pdata = new WmsUnitLoads;
                $pdata->unit_load_type = $data['unit_load_type'];
                $pdata->stock_unit = $data['unit_id'];
                $pdata->status = 1;
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
                    $unit_load_type_exist = 0;
                    $WmsUnitLoads = WmsUnitLoads::where([['unit_load_type', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($WmsUnitLoads) > 0) {
                        $unit_load_type_exist = 1;
                    }
                    
                    $unit_name_exist = "";
                    $unit_id = "";
                    $WmsUnit = WmsUnit::where([['unit_name', '=', $row[1]]])->get()->toArray();
                    if(count($WmsUnit) > 0) {
                        $unit_name_exist = 1;
                        $unit_id = $WmsUnit[0]['unit_id'];
                    }
                    
                    array_push($data, array('unit_load_type_exist' => $unit_load_type_exist, 'unit_load_type' => $row[0],  'unit_name_exist' => $unit_name_exist, 'unit_id' => $unit_id));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function unit_load_export(){
        $query = WmsUnitLoads::OrderBy('unit_load_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Load Type');
        $sheet->setCellValue('B1', 'Stock Unit');
        $rows = 2;
        foreach($query as $td){
            $unit_name = '';
            if(!empty($td['stock_unit'])) {
                $selectUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $td['stock_unit']]])->get()->toArray();
                if(count($selectUnit) > 0) {
                    if(!empty($selectUnit[0]['unit_name'])) $unit_name = $selectUnit[0]['unit_name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['unit_load_type']);
            $sheet->setCellValue('B' . $rows, $unit_name);
            $rows++;
        }
        $fileName = "unit_load.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}
