<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\ZoneMaster;
use DB;
use DataTables;
use App\Location;
use App\Row;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ZoneMasterController extends Controller {
    public function zone_master() {
        return \View::make("backend/config/zone_master")->with(array());
    }
    // Zone Master Modal  
    public function add_zone_master(){
        return \View::make("backend/config/zone_master_form")->with([
            'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray()
        ])->render();
    }
    // Zone Master Insert/Update
    public function save_zome_master(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = ZoneMaster::where([['zone_name', '=', $request->zone_name], ['location_id', '=', $request->location_id], ['zone_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Zone/Zone with location already exist. Please try with another Zone name."];
            }else {
                $saveData = ZoneMaster::where('zone_id', $request->hidden_id)->update(array('zone_name'=>$request->zone_name,'location_id'=>$request->location_id));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = ZoneMaster::where([['zone_name', '=', $request->zone_name], ['location_id', '=', $request->location_id]])->get()->toArray();
            //$selectData=ZoneMaster::where([['zone_name', '=', $request->zone_name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Zone/Zone with location already exist. Please try with another Zone name."];
            }else {
            	$data = new ZoneMaster;
            	$data->zone_name = $request->zone_name;
                $data->location_id = $request->location_id;
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
     // Zone Master dataTable
    public function list_config_zone_master(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('zone_master as zm');
            $query->select('zm.*', 'l.location_name');
            $query->join('location as l', 'l.location_id', '=', 'zm.location_id', 'left');
            if($keyword) {
                $query->whereRaw("(zm.zone_name like '%$keyword%' or l.location_name like '%$keyword%')");
                // $sql = "zone_name like ?";
                // $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('zm.zone_name', 'asc');
                else
                    $query->orderBy('zm.zone_id', 'desc');
            }else {
                $query->orderBy('zm.zone_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            // ->addColumn('location_name', function ($query) {
            //     $location_name = '';
            //     if(!empty($query->location_id)) {
            //         $Location = Location::select('location_name')->where([['location_id', '=', $query->location_id], ['is_deleted', '=', '0']])->get()->toArray();
            //         if(count($Location) > 0) {
            //             if(!empty($Location[0]['location_name'])) $location_name = $Location[0]['location_name'];
            //         }
            //     }
            //     return $location_name;
            // })
            ->addColumn('action', function ($query) {
                $Row = Row::where([['zone_id', '=', $query->zone_id]])->get()->toArray();
                if(sizeof($Row) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-zone-master" data-id="'.$query->zone_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Zone Master"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-zone-master" data-id="'.$query->zone_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Zone Master"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-zone-master" data-id="'.$query->zone_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Zone Master"><i class="fa fa-trash"></i></button></a>';
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
    // Export
    public function zone_master_export()
    {
        
        $query = DB::table('zone_master as zm')
        ->select('zm.*', 'l.location_name')
        ->join('location as l', 'l.location_id', '=', 'zm.location_id', 'left')
        ->orderBy('zm.zone_id', 'DESC');
        $data = $query->get()->toArray();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Zone_Name');
        $sheet->setCellValue('B1', 'Location_Name');

        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->zone_name);
            $sheet->setCellValue('B' . $rows, $empDetails->location_name);
            $rows++;
        }
        $fileName = "ZoneMaster.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Zone Master Edit
    public function edit_zone_master(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.zone_master_form')->with([
                'zone_data' => ZoneMaster::where([['zone_id', '=', $request->id]])->get()->toArray(),
                'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Zone Master Delete
    public function delete_zone_master(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ZoneMaster::where('zone_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function zone_master_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/zone_master_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $zone_name_exist = 0;
                    $ZoneMaster = ZoneMaster::where([['zone_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_name_exist = 1;
                    }
                    
                    array_push($data, array('zone_name_exist' => $zone_name_exist, 'zone_name' => $row[0], 'location_name' => $row[1]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_zone_master_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['zone_name_exist'] == 0 && $data['location_id'] != "") {
                $pdata = new ZoneMaster;
                $pdata->location_id = $data['location_id'];
                $pdata->zone_name = $data['zone_name'];
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
                    $zone_name_exist = 0;
                    $ZoneMaster = ZoneMaster::where([['zone_name', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($ZoneMaster) > 0) {
                        $zone_name_exist = 1;
                    }
                    $location_id = "";
                    $Location = Location::select('location_id')->where([['location_name', '=', $row[1]], ['is_deleted', '=', '0']])->get()->toArray();
                    if(count($Location) > 0) {
                        $location_id = $Location[0]['location_id'];
                    }
                    array_push($data, array('zone_name_exist' => $zone_name_exist, 'zone_name' => $row[0], 'location_id' => $location_id));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}