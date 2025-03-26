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
use App\Location;
use App\Place;
use App\Rack;
use App\Plate;
use App\BinningLocationDetails;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * 
 */
class PlaceController extends Controller
{
	
	public function index() {
		return \View::make("backend/config/place")->with(array());
	}
	// Add
    public function add_place(){
    	return \View::make("backend/config/place_form")->with([
            'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray()
        ])->render();
    }
    // Insert/ Update
    public function save_place(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = Place::where([['place_name', '=', $request->place_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '=', $request->rack_id], ['plate_id', '=', $request->plate_id], ['place_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Position/Position with location already exist. Please try with another name."];
            }else {
                $saveData = Place::where('place_id', $request->hidden_id)->update(array('place_name' => $request->place_name, 'location_id' => $request->location_id, 'zone_id' => $request->zone_id, 'row_id' => $request->row_id, 'rack_id' => $request->rack_id, 'plate_id' => $request->plate_id, 'max_capacity' => $request->max_capacity));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = Place::where([['place_name', '=', $request->place_name], ['location_id', '=', $request->location_id], ['zone_id', '=', $request->zone_id], ['row_id', '=', $request->row_id], ['rack_id', '=', $request->rack_id], ['plate_id', '=', $request->plate_id], ['status', '!=', '2']])->get()->toArray();
            //$selectData = Place::where([['place_name', '=', $request->place_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Position/Position with location already exist. Please try with another name."];
            }else {
            	$data = new Place;
            	$data->location_id = $request->location_id;
                $data->zone_id = $request->zone_id;
                $data->row_id = $request->row_id;
                $data->rack_id = $request->rack_id;
                $data->plate_id = $request->plate_id;
                $data->place_name = $request->place_name;
                $data->max_capacity = $request->max_capacity;
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
    // place Export
    public function place_export()
    {
        $query = DB::table('place as p')
        ->select('p.*', 'l.location_name as location', 'zm.zone_name', 'ro.row_name', 'ra.rack_name', 'pl.plate_name')
        ->join('location as l', 'l.location_id', '=', 'p.location_id', 'left')
        ->join('zone_master as zm', 'zm.zone_id', '=', 'p.zone_id', 'left')
        ->join('row as ro', 'ro.row_id', '=', 'p.row_id', 'left')
        ->join('rack as ra', 'ra.rack_id', '=', 'p.rack_id', 'left')
        ->join('plate as pl', 'pl.plate_id', '=', 'p.plate_id', 'left')
        ->where([['p.status', '!=', '2']])
        ->orderBy('p.place_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Position');
        $sheet->setCellValue('B1', 'Location');
        $sheet->setCellValue('C1', 'Zone');
        $sheet->setCellValue('D1', 'Row');
        $sheet->setCellValue('E1', 'Rack');
        $sheet->setCellValue('F1', 'Plate');
        
        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->place_name);
            $sheet->setCellValue('B' . $rows, $empDetails->location);
            $sheet->setCellValue('C' . $rows, $empDetails->zone_name);
            $sheet->setCellValue('D' . $rows, $empDetails->row_name);
            $sheet->setCellValue('E' . $rows, $empDetails->rack_name);
            $sheet->setCellValue('F' . $rows, $empDetails->plate_name);
            $rows++;
        }
        $fileName = "Position.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName); 
    }
    // place Export
    // DataTable
    public function list_place(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('place as p');
            $query->select('p.*', 'l.location_name as location', 'zm.zone_name', 'ro.row_name', 'ra.rack_name', 'pl.plate_name');
            $query->join('location as l', 'l.location_id', '=', 'p.location_id', 'left');
            $query->join('zone_master as zm', 'zm.zone_id', '=', 'p.zone_id', 'left');
            $query->join('row as ro', 'ro.row_id', '=', 'p.row_id', 'left');
            $query->join('rack as ra', 'ra.rack_id', '=', 'p.rack_id', 'left');
            $query->join('plate as pl', 'pl.plate_id', '=', 'p.plate_id', 'left');
            if($keyword) {
                $query->whereRaw("(p.place_name like '%$keyword%' or l.location_name like '%$keyword%' or zm.zone_name like '%$keyword%' or ro.row_name like '%$keyword%' or ra.rack_name like '%$keyword%' or pl.plate_name like '%$keyword%')");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('p.place_name', 'asc');
                else
                    $query->orderBy('p.place_id', 'desc');
            }else {
                $query->orderBy('p.place_id', 'DESC');
            }
            $query->where([['p.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            // ->addColumn('location', function ($query) {
            //     $location = "";
            //     if(!empty($query->location_id)) {
            //         $Location = Location::select('location_name')->where([['location_id', '=', $query->location_id], ['is_deleted', '=', '0']])->get()->toArray();
            //         if(!empty($Location)) {
            //             if(!empty($Location[0]['location_name'])) $location = $Location[0]['location_name'];
            //         }
            //     }
            //     return $location;
            // })
            // ->addColumn('zone_name', function ($query) {
            //     $zone_name = "";
            //     if(!empty($query->zone_id)) {
            //         $ZoneMaster = ZoneMaster::select('zone_name')->where([['zone_id', '=', $query->zone_id], ['status', '=', '1']])->get()->toArray();
            //         if(!empty($ZoneMaster)) {
            //             if(!empty($ZoneMaster[0]['zone_name'])) $zone_name = $ZoneMaster[0]['zone_name'];
            //         }
            //     }
            //     return $zone_name;
            // })
            // ->addColumn('row_name', function ($query) {
            //     $row_name = "";
            //     if(!empty($query->row_id)) {
            //         $Row = Row::select('row_name')->where([['row_id', '=', $query->row_id], ['status', '=', '1']])->get()->toArray();
            //         if(!empty($Row)) {
            //             if(!empty($Row[0]['row_name'])) $row_name = $Row[0]['row_name'];
            //         }
            //     }
            //     return $row_name;
            // })
            // ->addColumn('rack_name', function ($query) {
            //     $rack_name = "";
            //     if(!empty($query->rack_id)) {
            //         $Rack = Rack::select('rack_name')->where([['rack_id', '=', $query->rack_id], ['status', '=', '1']])->get()->toArray();
            //         if(!empty($Rack)) {
            //             if(!empty($Rack[0]['rack_name'])) $rack_name = $Rack[0]['rack_name'];
            //         }
            //     }
            //     return $rack_name;
            // })
            // ->addColumn('plate_name', function ($query) {
            //     $plate_name = "";
            //     if(!empty($query->plate_id)) {
            //         $Plate = Plate::select('plate_name')->where([['plate_id', '=', $query->plate_id], ['status', '=', '1']])->get()->toArray();
            //         if(!empty($Plate)) {
            //             if(!empty($Plate[0]['plate_name'])) $plate_name = $Plate[0]['plate_name'];
            //         }
            //     }
            //     return $plate_name;
            // })
            ->addColumn('action', function ($query) {
                $BinningLocationDetails = BinningLocationDetails::where([['place_id', '=', $query->place_id]])->get()->toArray();
                if(sizeof($BinningLocationDetails) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-place" data-id="'.$query->place_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Place"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-place" data-id="'.$query->place_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Place"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-place" data-id="'.$query->place_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Place"><i class="fa fa-trash"></i></button></a>';
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
    //Edit
    public function edit_place(Request $request) {
        if ($request->ajax()) {
            $ZoneMasterData = [];
            $RowData = [];
            $RackData = [];
            $PlateData = [];
            $Place = Place::where([['place_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(!empty($Place)) {
                if(!empty($Place[0]['location_id'])) {
                    $ZoneMaster = ZoneMaster::select('zone_id', 'zone_name')->where([['status', '=', '1'], ['location_id', '=', $Place[0]['location_id']]])->orderBy('zone_id', 'desc')->get()->toArray();
                    if(!empty($ZoneMaster)) {
                        $ZoneMasterData = $ZoneMaster;
                    }
                }
                if(!empty($Place[0]['zone_id'])) {
                    $Row = Row::select('row_id', 'row_name')->where([['status', '=', '1'], ['zone_id', '=', $Place[0]['zone_id']]])->orderBy('row_id', 'desc')->get()->toArray();
                    if(!empty($Row)) {
                        $RowData = $Row;
                    }
                }
                if(!empty($Place[0]['row_id'])) {
                    $Rack = Rack::select('rack_id', 'rack_name')->where([['status', '=', '1'], ['row_id', '=', $Place[0]['row_id']]])->orderBy('rack_id', 'desc')->get()->toArray();
                    if(!empty($Rack)) {
                        $RackData = $Rack;
                    }
                }
                if(!empty($Place[0]['row_id'])) {
                    $Plate = Plate::select('plate_id', 'plate_name')->where([['status', '=', '1'], ['rack_id', '=', $Place[0]['rack_id']]])->orderBy('plate_id', 'desc')->get()->toArray();
                    if(!empty($Plate)) {
                        $PlateData = $Plate;
                    }
                }
            }
            $html = view('backend/config/place_form')->with([
                'place_data' => $Place,
                'Location' => Location::select('location_id', 'location_name')->where([['is_deleted', '=', '0']])->orderBy('location_id', 'desc')->get()->toArray(),
                'ZoneMasterData' => $ZoneMasterData,
                'RowData' => $RowData,
                'RackData' => $RackData,
                'PlateData' => $PlateData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_place(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Place::where('place_id', $request->id)->update(['status' => '2']);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function place_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/place_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $Row = Row::select('row_id')->where([['row_name', '=', $row[3]], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_id = $Row[0]['row_id'];
                        $row_exist = "1";
                    }
                    
                    $rack_exist = "";
                    $rack_id = "";
                    $Rack = Rack::select('rack_id')->where([['rack_name', '=', $row[4]], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Rack) > 0) {
                        $rack_id = $Rack[0]['rack_id'];
                        $rack_exist = "1";
                    }
                    
                    $plate_exist = "";
                    $plate_id = "";
                    $Plate = Plate::select('plate_id')->where([['plate_name', '=', $row[5]], ['rack_id', '=', $rack_id], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Plate) > 0) {
                        $plate_id = $Plate[0]['plate_id'];
                        $plate_exist = "1";
                    }
                    
                    $place_name_exist = 0;
                    $Place = Place::where([['place_name', '=', $row[0]],['plate_id', '=', $plate_id], ['rack_id', '=', $rack_id], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Place) > 0) {
                        $place_name_exist = 1;
                    }
                    
                    array_push($data, array('place_name_exist' => $place_name_exist, 'place_name' => $row[0], 'location_id' => $location_id, 'location_exist' => $location_exist, 'location_name' => $row[1], 'zone_id' => $zone_id, 'zone_exist' => $zone_exist, 'zone_name' => $row[2], 'row_id' => $row_id, 'row_exist' => $row_exist, 'row_name' => $row[3], 'rack_id' => $rack_id, 'rack_exist' => $rack_exist, 'rack_name' => $row[4], 'plate_id' => $plate_id, 'plate_exist' => $plate_exist, 'plate_name' => $row[5], 'max_capacity' => $row[6]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_place_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['place_name_exist'] == 0 && $data['location_exist'] != "" && $data['zone_exist'] != "" && $data['row_exist'] != "" && $data['rack_exist'] != "" && $data['plate_exist'] != "") {
                $pdata = new Place;
                $pdata->place_name = $data['place_name'];
                $pdata->location_id = $data['location_id'];
                $pdata->zone_id = $data['zone_id'];
                $pdata->row_id = $data['row_id'];
                $pdata->rack_id = $data['rack_id'];
                $pdata->plate_id = $data['plate_id'];
                $pdata->max_capacity = $data['max_capacity'];
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
                    $Row = Row::select('row_id')->where([['row_name', '=', $row[3]], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Row) > 0) {
                        $row_id = $Row[0]['row_id'];
                        $row_exist = "1";
                    }
                    
                    $rack_exist = "";
                    $rack_id = "";
                    $Rack = Rack::select('rack_id')->where([['rack_name', '=', $row[4]], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Rack) > 0) {
                        $rack_id = $Rack[0]['rack_id'];
                        $rack_exist = "1";
                    }
                    
                    $plate_exist = "";
                    $plate_id = "";
                    $Plate = Plate::select('plate_id')->where([['plate_name', '=', $row[5]], ['rack_id', '=', $rack_id], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '!=', '2']])->get()->toArray();
                    if(count($Plate) > 0) {
                        $plate_id = $Plate[0]['plate_id'];
                        $plate_exist = "1";
                    }
                    
                    $place_name_exist = 0;
                    $Place = Place::where([['place_name', '=', $row[0]],['plate_id', '=', $plate_id], ['rack_id', '=', $rack_id], ['row_id', '=', $row_id], ['zone_id', '=', $zone_id], ['location_id', '=', $location_id], ['status', '=', '1']])->get()->toArray();
                    if(count($Place) > 0) {
                        $place_name_exist = 1;
                    }
                    
                    array_push($data, array('place_name_exist' => $place_name_exist, 'place_name' => $row[0], 'location_id' => $location_id, 'location_exist' => $location_exist, 'location_name' => $row[1], 'zone_id' => $zone_id, 'zone_exist' => $zone_exist, 'zone_name' => $row[2], 'row_id' => $row_id, 'row_exist' => $row_exist, 'row_name' => $row[3], 'rack_id' => $rack_id, 'rack_exist' => $rack_exist, 'rack_name' => $row[4], 'plate_id' => $plate_id, 'plate_exist' => $plate_exist, 'plate_name' => $row[5], 'max_capacity' => $row[6]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}


