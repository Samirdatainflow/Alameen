<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Countries;
use App\State;
use App\Cities;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WarehouseManagementController extends Controller {

    public function all_warehouse() {
        return \View::make("backend/warehouse/all_warehouse")->with(array());
    }
    // Warehouse Modal
    public function warehouse_form(){
        $countries = $this->countryFunction();
    	return \View::make("backend/warehouse/warehouse_add_form")->with(array('countries'=>$countries));
    }
    
    function countryFunction() {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    
    // Warehouses Insert/Update
    public function save_warehouse(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = Warehouses::where([['name' ,'=', $request->name], ['city_id','=', $request->city], ['state_id','=', $request->state], ['country_id', '=', $request->country], ['warehouse_id', '!=', $request->hidden_id]])->get()->toArray();

            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData=Warehouses::where('warehouse_id', $request->hidden_id)->update(array('name'=>$request->name,'address'=>$request->address,'city_id'=>$request->city, 'state_id'=>$request->state, 'state_code'=>$request->state_code, 'country_id'=>$request->country, 'country_code'=>$request->country_code, 'manager'=>$request->manager_name, 'contact'=>$request->manager_c_number, 'surface'=>$request->warehouse_area, 'volume'=>$request->warehouse_volume, 'freezone'=>$request->free_zone_volume, 'total_area_of_warehouse'=>$request->total_area_of_warehouse,'ground_floor'=>$request->ground_floor,'mezzanine_floor'=>$request->mezzanine_floor, 'first_floor'=>$request->first_floor, 'racks_and_bins'=>$request->racks_and_bins, 'pallets'=>$request->pallets, 'inbound_check_area'=>$request->inbound_check_area, 'outbound_check_area'=>$request->outbound_check_area, 'work_area'=>$request->work_area, 'area_of_office'=>$request->area_of_office, 'accommodation'=>$request->accommodation, 'security_office'=>$request->security_office));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Warehouses Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Warehouses Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Warehouses::where([['name','=', $request->name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData=Warehouses::insert(array('name'=>$request->name,'address'=>$request->address,'city_id'=>$request->city, 'state_id'=>$request->state, 'state_code'=>$request->state_code, 'country_id'=>$request->country, 'country_code'=>$request->country_code, 'manager'=>$request->manager_name, 'contact'=>$request->manager_c_number, 'surface'=>$request->warehouse_area, 'volume'=>$request->warehouse_volume, 'freezone'=>$request->free_zone_volume, 'total_area_of_warehouse'=>$request->total_area_of_warehouse,'ground_floor'=>$request->ground_floor,'mezzanine_floor'=>$request->mezzanine_floor, 'first_floor'=>$request->first_floor, 'racks_and_bins'=>$request->racks_and_bins, 'pallets'=>$request->pallets, 'inbound_check_area'=>$request->inbound_check_area, 'outbound_check_area'=>$request->outbound_check_area, 'work_area'=>$request->work_area, 'area_of_office'=>$request->area_of_office, 'accommodation'=>$request->accommodation, 'security_office'=>$request->security_office, 'status' => '1'));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Warehouse added successfully."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Warehouse Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Warehouse DataTAble
    public function list_warehouse(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            //$city_name = '';
            //$tagtitle = DB::table('tags')->where('title', 'LIKE', '%$query%');
            $query = DB::table('warehouses');
            $query->select('warehouse_id', 'name','address', 'city_id', 'state_id', 'country_id', 'manager', 'contact', 'surface', 'volume', 'freezone');
            if($keyword) {
                //$city_name = DB::table('cities')->where('city_name', 'LIKE', '%'.$keyword.'%');
                $query->whereRaw("(name like '%$keyword%' or address like '%$keyword%')");
                //$query->union($city_name);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('name', 'asc');
                else
                    $query->orderBy('name', 'desc');
            }else {
                $query->orderBy('warehouse_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('country', function ($query) {
                $country = '';
                $country_array = Countries::where('country_id',$query->country_id)->get();
                if(sizeof($country_array)>0)
                {
                    $country = $country_array[0]['country_name'];
                }
                return $country;
            })
            ->addColumn('state', function ($query) {
                $state = '';
                $state_array = State::where('state_id',$query->state_id)->get();
                if(sizeof($state_array)>0)
                {
                    $state = $state_array[0]['state_name'];
                }
                return $state;
            })
            ->addColumn('city', function ($query) {
                $city = '';
                $city_array = Cities::where('city_id',$query->city_id)->get();
                if(sizeof($city_array)>0)
                {
                    $city = $city_array[0]['city_name'];
                }
                return $city;
            })
            ->addColumn('select_warehouse', function ($query) {
                $select_warehouse = '<a href="javascript:void(0)" class="select-warehouse" data-id="'.$query->warehouse_id.'"><button type="button" class="btn-shadow btn btn-info" title="Select Warehouse">Select Warehouse</button></a>';
                return $select_warehouse;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-warehouse" data-id="'.$query->warehouse_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Warehouse"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-warehouse" data-id="'.$query->warehouse_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Warehouse"><i class="fa fa-trash"></i></button></a>';
                return $action;
            })
            ->rawColumns(['select_warehouse','action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Warehouse Edit
    public function edit_warehouse(Request $request) {
        if ($request->ajax()) {
            
            $stateData = [];
            $cityData = [];
            
            $warehouses=Warehouses::where([['warehouse_id', '=', $request->id]])->get()->toArray();
            
            if(sizeof($warehouses) > 0) {
                
                if(!empty($warehouses[0]['country_code'])) {
                    $stateData = $this->stateFunction($warehouses[0]['country_code']);
                }
                
                if(!empty($warehouses[0]['state_code'])) {
                    $cityData = $this->cityFunction($warehouses[0]['country_code'], $warehouses[0]['state_code']);
                }
            }
            
            $warehouses=Warehouses::where([['warehouse_id', '=', $request->id]])->get()->toArray();
            $html = view('backend/warehouse/warehouse_add_form')->with([
                'warehouse_data' => $warehouses,
                'countries' => $this->countryFunction(),
                'states'  => $stateData,
                'cities'  => $cityData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    function stateFunction($country_id) {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries/'.$country_id.'/states',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    
    function cityFunction($country_id, $state_id) {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries/'.$country_id.'/states/'.$state_id.'/cities',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array(
            'X-CSCAPI-KEY: eUVMVVRvUW93bTkzWUZrd1YzYjJqQVRSdk9BeGg4S3hiRDVWSlVaVg=='
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);
    }
    
    // Warehouses Delete
    public function delete_warehouse(Request $request) {
        $returnData = [];
        $saveData=Warehouses::where('warehouse_id', $request->id)->update(array('status'=>"2"));
        if($saveData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed!"];
        }
        return response()->json($returnData);
    }
    // Select Warehouse
    public function select_warehouse(Request $request) {
        if ($request->ajax()) {
            if(!empty($request->id)) {
                Session::put('warehouse_id', $request->id);
                return response()->json(["status" => 1]);
            }else {
                return response()->json(["status" => 0]);
            }
        }
    }
    public function warehouse_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/warehouse/warehouse_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $name_exist = 0;
                    $Warehouses = Warehouses::where([['name', '=', $row[0]]])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $name_exist = 1;
                    }
                    
                    $country_exist = '';
                    $country_id = '';
                    $countryArray = Countries::where('country_name',$row[2])->get();
                    if(sizeof($countryArray)> 0 ) {
                        
                        $country_id = $countryArray[0]['country_id'];
                        $country_exist = '1';
                    }
                    
                    $state_exist = '';
                    $state_id = '';
                    $stateArray = State::where('state_name',$row[3])->get();
                    if(sizeof($stateArray)> 0 ) {
                        
                        $state_id = $stateArray[0]['state_id'];
                        $state_exist = '1';
                    }
                    
                    $city_exist = '';
                    $city_id = '';
                    $citiesArray = Cities::where('city_name',$row[4])->get();
                    if(sizeof($citiesArray)> 0 ) {
                        
                        $city_id = $citiesArray[0]['city_id'];
                        $city_exist = '1';
                    }
                    
                    array_push($data, array('name_exist' => $name_exist, 'name' => $row[0], 'address' => $row[1], 'country_name' => $row[2], 'state_name' => $row[3], 'city_name' => $row[4], 'manager' => $row[5], 'contact' => $row[6], 'surface' => $row[7], 'volume' => $row[8], 'freezone' => $row[9], 'country_exist' => $country_exist, 'state_exist' => $state_exist, 'city_exist' => $city_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_warehouse_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $WarehouseArr = $this->csvToArray($file);
        foreach($WarehouseArr['data'] as $data) {
            if($data['name_exist'] == "0" && $data['country_exist'] !== "" && $data['state_exist'] !== "" && $data['city_exist'] !== "") {
                $pdata = new Warehouses;
                $pdata->name = $data['name'];
                $pdata->address = $data['address'];
                $pdata->country_id = $data['city_id'];
                $pdata->state_id = $data['state_id'];
                $pdata->city_id = $data['country_id'];
                $pdata->manager = $data['manager'];
                $pdata->contact = $data['contact'];
                $pdata->surface = $data['surface'];
                $pdata->volume = $data['volume'];
                $pdata->freezone = $data['freezone'];
                $pdata->total_area_of_warehouse = $data['total_area_of_warehouse'];
                $pdata->ground_floor = $data['ground_floor'];
                $pdata->mezzanine_floor = $data['mezzanine_floor'];
                $pdata->first_floor = $data['first_floor'];
                $pdata->racks_and_bins = $data['racks_and_bins'];
                $pdata->pallets = $data['pallets'];
                $pdata->inbound_check_area = $data['inbound_check_area'];
                $pdata->outbound_check_area = $data['outbound_check_area'];
                $pdata->work_area = $data['work_area'];
                $pdata->area_of_office = $data['area_of_office'];
                $pdata->accommodation = $data['accommodation'];
                $pdata->security_office = $data['security_office'];
                $pdata->status = "1";
                $pdata->save();
            }
            $flag++;
        }
        if($flag == sizeof($WarehouseArr['data'])) {
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
                    $name_exist = 0;
                    $Warehouses = Warehouses::where([['name', '=', $row[0]]])->get()->toArray();
                    if(count($Warehouses) > 0) {
                        $name_exist = 1;
                    }
                    
                    $country_exist = '';
                    $country_id = '';
                    $countryArray = Countries::where('country_name',$row[2])->get();
                    if(sizeof($countryArray)> 0 ) {
                        
                        $country_id = $countryArray[0]['country_id'];
                        $country_exist = '1';
                    }
                    
                    $state_exist = '';
                    $state_id = '';
                    $stateArray = State::where('state_name',$row[3])->get();
                    if(sizeof($stateArray)> 0 ) {
                        
                        $state_id = $stateArray[0]['state_id'];
                        $state_exist = '1';
                    }
                    
                    $city_exist = '';
                    $city_id = '';
                    $citiesArray = Cities::where('city_name',$row[4])->get();
                    if(sizeof($citiesArray)> 0 ) {
                        
                        $city_id = $citiesArray[0]['city_id'];
                        $city_exist = '1';
                    }
                    
                    array_push($data, array('name_exist' => $name_exist, 'name' => $row[0], 'address' => $row[1], 'country_id' => $country_id, 'state_id' => $state_id, 'city_id' => $city_id, 'manager' => $row[5], 'contact' => $row[6], 'surface' => $row[7], 'volume' => $row[8], 'freezone' => $row[9], 'total_area_of_warehouse' => $row[10], 'ground_floor' => $row[11], 'mezzanine_floor' => $row[12], 'first_floor' => $row[13], 'racks_and_bins' => $row[14], 'pallets' => $row[15], 'inbound_check_area' => $row[16], 'outbound_check_area' => $row[17], 'work_area' => $row[18], 'area_of_office' => $row[19], 'accommodation' => $row[20], 'security_office' => $row[21], 'country_exist' => $country_exist, 'state_exist' => $state_exist, 'city_exist' => $city_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function warehouse_export(){
        $query = DB::table('warehouses')->select('*')->orderBy('warehouse_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Address');
        $sheet->setCellValue('C1', 'City');
        $sheet->setCellValue('D1', 'State');
        $sheet->setCellValue('E1', 'Country');
        $sheet->setCellValue('F1', 'Manager');
        $sheet->setCellValue('G1', 'Manager Contact');
        $sheet->setCellValue('H1', 'Surface Area');
        $sheet->setCellValue('I1', 'Free Zone');
        $sheet->setCellValue('J1', 'Volume');
        $rows = 2;
        foreach($query as $d2){
            $city_name = '';
            if(!empty($d2->city_id)) {
                $Cities = Cities::select('city_name')->where([['city_id', '=', $d2->city_id]])->get()->toArray();
                if(count($Cities) > 0) {
                    if(!empty($Cities[0]['city_name'])) $city_name = $Cities[0]['city_name'];
                }
            }
            $state_name = '';
            if(!empty($d2->state_id)) {
                $State = State::select('state_name')->where([['state_id', '=', $d2->state_id]])->get()->toArray();
                if(count($State) > 0) {
                    if(!empty($State[0]['state_name'])) $state_name = $State[0]['state_name'];
                }
            }
            $country_name = '';
            if(!empty($d2->country_id)) {
                $Countries = Countries::select('country_name')->where([['country_id', '=', $d2->country_id]])->get()->toArray();
                if(count($Countries) > 0) {
                    if(!empty($Countries[0]['country_name'])) $country_name = $Countries[0]['country_name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $d2->name);
            $sheet->setCellValue('B' . $rows, $d2->address);
            $sheet->setCellValue('C' . $rows, $city_name);
            $sheet->setCellValue('D' . $rows, $state_name);
            $sheet->setCellValue('E' . $rows, $country_name);
            $sheet->setCellValue('F' . $rows, $d2->manager);
            $sheet->setCellValue('G' . $rows, $d2->contact);
            $sheet->setCellValue('H' . $rows, $d2->surface);
            $sheet->setCellValue('I' . $rows, $d2->freezone);
            $sheet->setCellValue('J' . $rows, $d2->volume);
            $rows++;
        }
        $fileName = "warehouse.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}