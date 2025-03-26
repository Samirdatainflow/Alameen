<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Location;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * 
 */
class RowController extends Controller
{
	
	public function config_row()
	{
		return \View::make("backend/config/row")->with(array());
	}
	public function add_row()
	{
		return \View::make("backend/config/row_from")->with([
            'location_id' => Location::where('is_deleted',0)->orderBy('location_id', 'desc')->get()->toArray(),
            // 'zone_id' => ZoneMaster::where('status',1)->get()->toArray()
        ])->render();

	}
	public function get_zone_by_locaton(Request $request)
	{
		$location_id = $request->location_id;
        $zone =  Location::where([['location_id', '=',$location_id], ['is_deleted','=','0']])->get()->toArray();
        $zone =  ZoneMaster::where([['location_id', '=',$zone]])->get()->toArray();
        return response()->json($zone);
	}
	public function save_row(Request $request) {
        if(!empty($request->hidden_id)) {
            //echo $request->location_id." - ".$request->zone_id." - ".$request->hidden_id; exit();
            $selectData = Row::where([['row_name', '=', $request->row_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter row/row with location already exist. Please try with another Zone name."];
            }else {
                $saveData = Row::where('row_id', $request->hidden_id)->update(array('row_name'=>$request->row_name,'location_id'=>$request->location_id,'zone_id'=>$request->zone_id));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else{
            $selectData = Row::where([['row_name', '=', $request->row_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['status', '!=', '2']])->get()->toArray();
            //$selectData = Row::where([['row_name', '=', $request->row_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter row/row with location already exist. Please try with another Zone name."];
            }else {
                $data = new Row;
            	$data->location_id = $request->location_id;
            	$data->zone_id = $request->zone_id;
            	$data->row_name = $request->row_name; 
            	$data->status = 1;
                // print_r($data); exit();
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
	public function list_config_row(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('row as r');
            $query->select('r.*', 'l.location_name', 'zm.zone_name');
            $query->join('location as l', 'l.location_id', '=', 'r.location_id', 'left');
            $query->join('zone_master as zm', 'zm.zone_id', '=', 'r.zone_id', 'left');
            if($keyword) {
                $query->whereRaw("(r.row_name like '%$keyword%' or l.location_name like '%$keyword%' or zm.zone_name like '%$keyword%')");
                // $sql = "row_name like ?";
                // $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('r.row_name', 'asc');
                else
                    $query->orderBy('r.row_id', 'desc');
            }else {
                $query->orderBy('r.row_id', 'DESC');
            }
          $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Rack = Rack::where([['row_id', '=', $query->row_id]])->get()->toArray();
                if(sizeof($Rack) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-row" data-id="'.$query->row_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Row"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-row" data-id="'.$query->row_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Row"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-row" data-id="'.$query->row_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Row"><i class="fa fa-trash"></i></button></a>';
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
    public function row_export()
    {
        
        $query = DB::table('row as r')
        ->select('r.*', 'l.location_name', 'zm.zone_name')
        ->join('location as l', 'l.location_id', '=', 'r.location_id', 'left')
        ->join('zone_master as zm', 'zm.zone_id', '=', 'r.zone_id', 'left')
        ->orderBy('r.row_id', 'DESC');
        $data = $query->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Row_name');
        $sheet->setCellValue('B1', 'Location');
        $sheet->setCellValue('C1', 'Zone');
        
        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->row_name);
            $sheet->setCellValue('B' . $rows, $empDetails->location_name);
            $sheet->setCellValue('C' . $rows, $empDetails->zone_name);
            $rows++;
        }
        $fileName = "Row.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function edit_row(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.row_from')->with([
                'row_data' => Row::where([['row_id', '=', $request->id]])->get()->toArray(),
                'location_id' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray(),
                // 'location_id' => Location::where('is_deleted',0)->orderBy('location_id', 'desc')->get()->toArray(),
                 'zone_id' => ZoneMaster::select('zone_id', 'zone_name')->where([['status', '=', '1']])->orderBy('zone_id', 'desc')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function delete_row(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Row::where('row_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function row_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/row_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $Location = Location::select('location_id')->where([['location_name', '=', $row[0]], ['is_deleted', '=', '0']])->get()->toArray();
                    if(count($Location) > 0) {
                        $location_id = $Location[0]['location_id'];
                        $location_exist = "1";
                    }
                    
                    $zone_exist = "";
                    $zone_id = "";
                    $ZoneMaster = ZoneMaster::select('zone_id')->where([['zone_name', '=', $row[1]], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_id = $ZoneMaster[0]['zone_id'];
                        $zone_exist = "1";
                    }
                    
                    $row_name_exist = 0;
                    $Row = Row::where([['row_name', '=', $row[2]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_name_exist = 1;
                    }
                    
                    array_push($data, array('row_name_exist' => $row_name_exist, 'row_name' => $row[2], 'location_name' => $row['0'], 'zone_name' => $row['1'], 'location_exist' => $location_exist, 'zone_exist' => $zone_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_row_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['row_name_exist'] == 0 && $data['location_exist'] != "" && $data['zone_exist'] != "") {
                $pdata = new Row;
                $pdata->zone_id = $data['zone_id'];
                $pdata->location_id = $data['location_id'];
                $pdata->row_name = $data['row_name'];
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
                    $Location = Location::select('location_id')->where([['location_name', '=', $row[0]], ['is_deleted', '=', '0']])->get()->toArray();
                    if(count($Location) > 0) {
                        $location_id = $Location[0]['location_id'];
                        $location_exist = "1";
                    }
                    
                    $zone_exist = "";
                    $zone_id = "";
                    $ZoneMaster = ZoneMaster::select('zone_id')->where([['zone_name', '=', $row[1]], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_id = $ZoneMaster[0]['zone_id'];
                        $zone_exist = "1";
                    }
                    
                    $row_name_exist = 0;
                    $Row = Row::where([['row_name', '=', $row[2]], ['location_id', '=', $location_id], ['zone_id', '=', $zone_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_name_exist = 1;
                    }
                    
                    array_push($data, array('row_name_exist' => $row_name_exist, 'row_name' => $row[2], 'location_name' => $row['0'], 'zone_name' => $row['1'], 'location_id' => $location_id, 'zone_id' => $zone_id, 'location_exist' => $location_exist, 'zone_exist' => $zone_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}