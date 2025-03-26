<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Cities;
use App\State;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CitiesController extends Controller {

    public function cities() {
        return \View::make("backend/config/cities")->with(array());
    }
    // Cities Modal
    public function cities_form(){
        return \View::make("backend/config/cities_form")->with([
            'state_id' => State::where('status',0)->get()->toArray()
        ])->render();
    }
    // Insert/ Update
    public function save_config_cities(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Cities::where([['city_code', '=', $request->city_code], ['city_name', '=', $request->city_name], ['state_id', '=', $request->state_id], ['city_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter City Code already exist. Please try with another City Code."];
            }else {
                $saveData=Cities::where('city_id', $request->hidden_id)->update(array('city_code'=>$request->city_code,'city_name'=>$request->city_name, 'state_id'=>$request->state_id));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "City Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => " City Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Cities::where([['city_code', '=', $request->city_code]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter City Code already exist. Please try with another City Code."];
            }else {
            	$data = new Cities;
            	$data->city_code = $request->city_code;
            	$data->city_name = $request->city_name;
            	$data->state_id = $request->state_id;
                $data->status = "0";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " City Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "City Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Cities dataTable
    public function list_config_cities(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('cities');
            $query->select('*');
            if($keyword)
            {
                $sql = "city_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('city_name', 'asc');
                else
                    $query->orderBy('city_id', 'desc');
            }
            else
            {
                $query->orderBy('city_id', 'DESC');
            }
            $query->where([['status', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('state_id', function ($query) {
                $state_id = '';
                if(!empty($query->state_id)) {
                    $selectState = State::select('state_name')->where([['state_id', '=', $query->state_id]])->get()->toArray();
                    if(count($selectState) > 0) {
                        if(!empty($selectState[0]['state_name'])) $state_id = $selectState[0]['state_name'];
                    }
                }
                return $state_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-city" data-id="'.$query->city_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit City"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-city" data-id="'.$query->city_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete City"><i class="fa fa-trash"></i></button></a>';
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
    //Edit Cities
    public function edit_config_city(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/cities_form')->with([
                'city_data' => Cities::where([['city_id', '=', $request->id]])->get()->toArray(),
                'state_id' => State::where('status',0)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Cities Delete
    public function delete_config_city(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Cities::where('city_id', $request->id)->update(['status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }

    public function city_list_by_state(Request $request){

        $state_id = $request->state_id;
        $country_id = $request->country_id;
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

        echo $response;
    }
    public function cities_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/cities_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $city_name_exist = 0;
                    $Cities = Cities::where([['city_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($Cities) > 0) {
                        $city_name_exist = 1;
                    }
                    $state_name = "";
                    $State = State::where([['state_name', '=', $row[2]], ['status', '=', '0']])->get()->toArray();
                    if(count($State) > 0) {
                        if(!empty($State[0]['state_name'])) $state_name = $State[0]['state_name'];
                    }
                    array_push($data, array('city_code' => $row[0], 'city_name' => $row[1], 'city_name_exist' => $city_name_exist, 'state_name' => $state_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_cities_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['city_name'] != "") {
                $pdata = new Cities;
                $pdata->state_id = $data['state_id'];
                $pdata->city_code = $data['city_code'];
                $pdata->city_name = $data['city_name'];
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
                    $city_name = "";
                    $selectData = Cities::where([['city_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $city_name = "";
                    }else {
                        $city_name = $row[1];
                    }
                    
                    $state_id = "";
                    $State = State::where([['state_name', '=', $row[2]], ['status', '=', '0']])->get()->toArray();
                    if(count($State) > 0) {
                        if(!empty($State[0]['state_id'])) $state_id = $State[0]['state_id'];
                    }
                    
                    array_push($data, array('state_id' => $state_id, 'city_code' => $row[0], 'city_name' => $city_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function cities_export(){
        $query = DB::table('cities')
        ->select('*')
        ->where([['status', '=', '0']])
        ->orderBy('city_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'City_code');
        $sheet->setCellValue('B1', 'City_name');
        $sheet->setCellValue('C1', 'State_name');
        $rows = 2;
        foreach($data as $empDetails){
            $state_id = '';
            if(!empty($empDetails->state_id)) {
                $selectState = State::select('state_name')->where([['state_id', '=', $empDetails->state_id]])->get()->toArray();
                if(count($selectState) > 0) {
                    if(!empty($selectState[0]['state_name'])) $state_id = $selectState[0]['state_name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $empDetails->city_code);
            $sheet->setCellValue('B' . $rows, $empDetails->city_name);
            $sheet->setCellValue('C' . $rows, $state_id);
            
            $rows++;
        }
        $fileName = "citys_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}