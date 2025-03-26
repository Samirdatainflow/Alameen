<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\Warehouses;
use App\Location;
use App\FunctionalArea;
use App\ZoneMaster;
use App\WmsUnitLoads;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocationManagementController extends Controller {

    public function location_management() {
        return \View::make("backend/location/location_management")->with(array());
    }
    // Modal View
    public function location_form(){

    	$warehouses=Warehouses::where([['status', '=', '1']])->get()->toArray();
        $functional_areas=FunctionalArea::where([['status', '=', '0']])->get()->toArray();
        $location_zones=ZoneMaster::where([['status', '=', '0']])->get()->toArray();
        $location_loads=WmsUnitLoads::where([['status', '=', '1']])->get()->toArray();
    	return \View::make("backend/location/location_add_form")->with(array('warehouses'=>$warehouses,'functional_areas'=>$functional_areas,'location_zones'=>$location_zones,'location_loads'=>$location_loads));
    }
    // Warehouses Insert/Update
    public function save_location(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Location::where([['location_name', '=', $request->location_name], ['location_id', '!=', $request->hidden_id], ['is_deleted','=',0]])->get()->toArray();

            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Location already exists"];
            }else {
                $saveData=Location::where('location_id', $request->hidden_id)->update(array('location_name'=>$request->location_name,'location_type'=>$request->location_type,'location_functional'=>$request->location_functional, 'location_load_type'=>$request->location_load_type, 'location_capacity'=>$request->location_capacity, 'order_index'=>$request->order_index, 'warehouseid'=>$request->warehouse,'modified_date'=>date('Y-m-d')));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Location Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Location Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Location::where('location_name', '=', $request->location_name)->where('is_deleted',0)->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Location already exists ."];
            }else {
                $saveData=Location::insert(array('location_name'=>$request->location_name,'location_type'=>$request->location_type,'location_functional'=>$request->location_functional, 'location_load_type'=>$request->location_load_type, 'location_capacity'=>$request->location_capacity, 'order_index'=>$request->order_index, 'warehouseid'=>$request->warehouse,'created_date'=>date('Y-m-d'),'modified_date'=>date('Y-m-d')));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Location added successfully."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Location Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Warehouse DataTAble
    public function list_location(Request $request) {
        if ($request->ajax()) {
            $query = DB::table('location');
            $order = $request->input('order.0.dir');
            $query->select('location_id', 'location_name','location_type', 'location_functional', 'location_load_type', 'location_capacity', 'order_index', 'warehouseid');
            $keyword = $request->input('search.value');
            if($keyword)
            {
                $sql = "location_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('location_name', 'asc');
                else
                    $query->orderBy('location_name', 'desc');
            }
            else
            {
                $query->orderBy('location_id', 'DESC');
            }
            $query->where([['is_deleted', '!=', '1']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('location_functional', function ($query) {
                $location_functional="";
                $selectData=FunctionalArea::where([['functional_area_id', '=', $query->location_functional]])->get()->toArray();
                if(sizeof($selectData)>0)
                {
                    $location_functional = $selectData[0]['function_area_name'];
                }
                return $location_functional;
            })
            ->addColumn('location_load_type', function ($query) {
                $location_load_type="";
                $selectData=WmsUnitLoads::where([['unit_load_id', '=', $query->location_load_type]])->get()->toArray();
                if(sizeof($selectData)>0)
                {
                    $location_load_type = $selectData[0]['unit_load_type'];
                }
                return $location_load_type;
            })
            ->addColumn('warehouse', function ($query) {
            	$warehouse="";
                $selectData=Warehouses::where([['warehouse_id', '=', $query->warehouseid]])->get()->toArray();
                if(sizeof($selectData)>0)
                {
                	$warehouse = $selectData[0]['name'];
                }
                return $warehouse;
            })
            ->addColumn('action', function ($query) {
                $ZoneMaster = ZoneMaster::where([['location_id', '=', $query->location_id]])->get()->toArray();
                if(sizeof($ZoneMaster) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-location" data-id="'.$query->location_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Location"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-location" data-id="'.$query->location_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Location"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-location" data-id="'.$query->location_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Location"><i class="fa fa-trash"></i></button></a>';
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
    // Warehouse Edit
    public function edit_location(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/location/location_add_form')->with([
                'location_data' => Location::where([['location_id', '=', $request->id]])->get()->toArray(),
                'warehouses' => Warehouses::where([['status', '=', '1']])->get()->toArray(),
                'functional_areas' => FunctionalArea::where([['status', '=', '0']])->get()->toArray(),
                'location_zones' => ZoneMaster::where([['status', '=', '0']])->get()->toArray(),
                'location_loads' => WmsUnitLoads::where([['status', '=', '1']])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function delete_location(Request $request) {
        $returnData = [];
        $saveData=Location::where('location_id', $request->id)->update(array('is_deleted'=>"1"));
        if($saveData) {
            $returnData = ["status" => 1, "msg" => "Location is removed successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Location delete failed!"];
        }
        return response()->json($returnData);
    }
    public function location_export(){
        $query = Location::OrderBy('location_id', 'ASC')->where([['is_deleted', '!=', 1]])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Location Type');
        $sheet->setCellValue('C1', 'Location Funtional');
        $sheet->setCellValue('D1', 'Location Load Type');
        $sheet->setCellValue('E1', 'Location Capacity');
        $sheet->setCellValue('F1', 'Roder Index');
        $sheet->setCellValue('G1', 'Warehouse');
        $rows = 2;
        foreach($query as $td){
            $function_area_name = '';
            if(!empty($td['location_functional'])) {
                $FunctionalArea = FunctionalArea::where([['functional_area_id', '=', $td['location_functional']]])->get()->toArray();
                if(sizeof($FunctionalArea)>0) {
                    $function_area_name = $FunctionalArea[0]['function_area_name'];
                }
            }
            $unit_load_type = '';
            if(!empty($td['location_functional'])) {
                $WmsUnitLoads = WmsUnitLoads::where([['unit_load_id', '=', $td['location_load_type']]])->get()->toArray();
                if(sizeof($WmsUnitLoads)>0) {
                    $unit_load_type = $WmsUnitLoads[0]['unit_load_type'];
                }
            }
            $warehouse_name = '';
            if(!empty($td['location_functional'])) {
                $Warehouses = Warehouses::where([['warehouse_id', '=', $td['warehouseid']]])->get()->toArray();
                if(sizeof($Warehouses)>0) {
                    $warehouse_name = $Warehouses[0]['name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['location_name']);
            $sheet->setCellValue('B' . $rows, $td['location_type']);
            $sheet->setCellValue('C' . $rows, $function_area_name);
            $sheet->setCellValue('D' . $rows, $unit_load_type);
            $sheet->setCellValue('E' . $rows, $td['location_capacity']);
            $sheet->setCellValue('F' . $rows, $td['order_index']);
            $sheet->setCellValue('G' . $rows, $warehouse_name);
            $rows++;
        }
        $fileName = "location_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
}