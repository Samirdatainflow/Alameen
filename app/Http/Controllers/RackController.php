<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Countries;
use DB;
use DataTables;
use App\Location;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Plate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RackController extends Controller {

    public function index() {
        return \View::make("backend/config/rack")->with(array());
    }
    // Add
    public function add_rack(){
    	return \View::make("backend/config/rack_form")->with([
            'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray()
        ])->render();
    }
    // Insert/ Update
    public function save_rack(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = Rack::where([['rack_name', '=', $request->rack_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter rack/rack with location already exist. Please try with another name."];
            }else {
                $saveData = Rack::where('rack_id', $request->hidden_id)->update(array('rack_name' => $request->rack_name, 'location_id' => $request->location_id, 'zone_id' => $request->zone_id, 'row_id' => $request->row_id));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = Rack::where([['rack_name', '=', $request->rack_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['status', '!=', '2']])->get()->toArray();
            //$selectData=Rack::where([['rack_name', '=', $request->rack_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter rack/rack with location already exist. Please try with another name."];
            }else {
            	$data = new Rack;
            	$data->location_id = $request->location_id;
                $data->zone_id = $request->zone_id;
                $data->row_id = $request->row_id;
                $data->rack_name = $request->rack_name;
            	$data->status = "1";
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // DataTable
    public function list_rack(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('rack as r');
            $query->select('r.*', 'l.location_name as location', 'zm.zone_name', 'ro.row_name');
            $query->join('location as l', 'l.location_id', '=', 'r.location_id', 'left');
            $query->join('zone_master as zm', 'zm.zone_id', '=', 'r.zone_id', 'left');
            $query->join('row as ro', 'ro.row_id', '=', 'r.row_id', 'left');
            if($keyword) {
                $query->whereRaw("(r.rack_name like '%$keyword%' or l.location_name like '%$keyword%' or zm.zone_name like '%$keyword%' or ro.row_name like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('r.rack_name', 'asc');
                else
                    $query->orderBy('r.rack_id', 'desc');
            }else {
                $query->orderBy('r.rack_id', 'DESC');
            }
            $query->where([['r.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Plate = Plate::where([['rack_id', '=', $query->rack_id]])->get()->toArray();
                if(sizeof($Plate) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-rack" data-id="'.$query->rack_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Rack"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-rack" data-id="'.$query->rack_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Rack"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-rack" data-id="'.$query->rack_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Rack"><i class="fa fa-trash"></i></button></a>';
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

    // export
    public function rack_export_table()
    {
        $query = DB::table('rack as r')
        ->select('r.*', 'l.location_name as location', 'zm.zone_name', 'ro.row_name')
        ->join('location as l', 'l.location_id', '=', 'r.location_id', 'left')
        ->join('zone_master as zm', 'zm.zone_id', '=', 'r.zone_id', 'left')
        ->join('row as ro', 'ro.row_id', '=', 'r.row_id', 'left')
        ->where([['r.status', '!=', '2']])
        ->orderBy('r.rack_id', 'DESC');
        $data = $query->get()->toArray();

        
          
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Rack_name');
        $sheet->setCellValue('B1', 'Location');
        $sheet->setCellValue('C1', 'Zone');
        $sheet->setCellValue('D1', 'Row');

        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->rack_name);
            $sheet->setCellValue('B' . $rows, $empDetails->location);
            $sheet->setCellValue('C' . $rows,  $empDetails->zone_name);
            $sheet->setCellValue('D' . $rows,  $empDetails->row_name);
            
            $rows++;
        }
        $fileName = "Rack.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    //Edit
    public function edit_rack(Request $request) {
        if ($request->ajax()) {
            $ZoneMasterData = [];
            $RowData = [];
            $Rack = Rack::where([['rack_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(!empty($Rack)) {
                if(!empty($Rack[0]['location_id'])) {
                    $ZoneMaster = ZoneMaster::select('zone_id', 'zone_name')->where([['status', '=', '1'], ['location_id', '=', $Rack[0]['location_id']]])->orderBy('zone_id', 'desc')->get()->toArray();
                    if(!empty($ZoneMaster)) {
                        $ZoneMasterData = $ZoneMaster;
                    }
                }
                if(!empty($Rack[0]['zone_id'])) {
                    $Row = Row::select('row_id', 'row_name')->where([['status', '=', '1'], ['zone_id', '=', $Rack[0]['zone_id']]])->orderBy('row_id', 'desc')->get()->toArray();
                    if(!empty($Row)) {
                        $RowData = $Row;
                    }
                }
            }
            $html = view('backend/config/rack_form')->with([
                'rack_data' => $Rack,
                'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray(),
                'ZoneMasterData' => $ZoneMasterData,
                'RowData' => $RowData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_rack(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Rack::where('rack_id', $request->id)->update(['status' => '2']);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function rack_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/rack_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    
                    $location_exist = "";
                    $location_id = "";
                    $Location = Location::select('location_id')->where([['location_name', '=', $row[1]], ['is_deleted', '=', '0']])->get()->toArray();
                    if(count($Location) > 0) {
                        $location_id = $Location[0]['location_id'];
                        $location_exist = "1";
                    }
                    
                    $zone_exist = "";
                    $zone_id = "";
                    $ZoneMaster = ZoneMaster::select('zone_id')->where([['zone_name', '=', $row[2]], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_id = $ZoneMaster[0]['zone_id'];
                        $zone_exist = "1";
                    }
                    
                    $row_exist = "";
                    $row_id = "";
                    $Row = Row::select('row_id')->where([['row_name', '=', $row[3]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_id = $Row[0]['row_id'];
                        $row_exist = "1";
                    }
                    
                    $rack_name_exist = 0;
                    $Rack = Rack::where([['rack_name', '=', $row[0]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Rack) > 0) {
                        $rack_name_exist = 1;
                    }
                    
                    array_push($data, array('rack_name_exist' => $rack_name_exist, 'rack_name' => $row[0], 'location_name' => $row[1], 'location_exist' => $location_exist, 'location_id' => $location_id, 'zone_name' => $row[2], 'zone_exist' => $zone_exist, 'zone_id' => $zone_id, 'row_name' => $row[3], 'row_exist' => $row_exist, 'row_id' => $row_id));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_rack_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['rack_name_exist'] == 0 && $data['location_exist'] != "" && $data['zone_exist'] != "" && $data['row_exist'] != "") {
                $pdata = new Rack;
                $pdata->rack_name = $data['rack_name'];
                $pdata->location_id = $data['location_id'];
                $pdata->zone_id = $data['zone_id'];
                $pdata->row_id = $data['row_id'];
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
                    
                    $location_exist = "";
                    $location_id = "";
                    $Location = Location::select('location_id')->where([['location_name', '=', $row[1]], ['is_deleted', '=', '0']])->get()->toArray();
                    if(count($Location) > 0) {
                        $location_id = $Location[0]['location_id'];
                        $location_exist = "1";
                    }
                    
                    $zone_exist = "";
                    $zone_id = "";
                    $ZoneMaster = ZoneMaster::select('zone_id')->where([['zone_name', '=', $row[2]], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_id = $ZoneMaster[0]['zone_id'];
                        $zone_exist = "1";
                    }
                    
                    $row_exist = "";
                    $row_id = "";
                    $Row = Row::select('row_id')->where([['row_name', '=', $row[3]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_id = $Row[0]['row_id'];
                        $row_exist = "1";
                    }
                    
                    $rack_name_exist = 0;
                    $Rack = Rack::where([['rack_name', '=', $row[0]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Rack) > 0) {
                        $rack_name_exist = 1;
                    }
                    
                    array_push($data, array('rack_name_exist' => $rack_name_exist, 'rack_name' => $row[0], 'location_name' => $row[1], 'location_exist' => $location_exist, 'location_id' => $location_id, 'zone_name' => $row[2], 'zone_exist' => $zone_exist, 'zone_id' => $zone_id, 'row_name' => $row[3], 'row_exist' => $row_exist, 'row_id' => $row_id));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}