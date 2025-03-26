<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\State;
use App\Countries;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StateController extends Controller {

    public function state() {
        return \View::make("backend/config/state")->with(array());
    }
    // State Modal
    public function state_form(){
        return \View::make("backend/config/state_form")->with([
            'country_id' => Countries::where('status',0)->get()->toArray()
        ])->render();
    }
    // Insert/ Update
    public function save_config_state(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=State::where([['state_code', '=', $request->state_code], ['country_id', '=', $request->country_id], ['state_name', '=', $request->state_name], ['state_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter State Code already exist. Please try with another State Code."];
            }else {
                $saveData=State::where('state_id', $request->hidden_id)->update(array('state_code'=>$request->state_code,'country_id'=>$request->country_id, 'state_name'=>$request->state_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "State Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => " State Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=State::where([['state_code', '=', $request->state_code], ['country_id', '=', $request->country_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter State Code already exist. Please try with another State Code."];
            }else {
            	$data = new State;
            	$data->state_code = $request->state_code;
            	$data->country_id = $request->country_id;
            	$data->state_name = $request->state_name;
            	$data->status = '0';
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " State Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "State Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // State dataTable
    public function list_config_state(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('state');
            $query->select('*');
            if($keyword)
            {
                $sql = "state_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('state_name', 'asc');
                else
                    $query->orderBy('state_id', 'desc');
            }
            else
            {
                $query->orderBy('state_id', 'DESC');
            }
            $query->where([['status', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('country_id', function ($query) {
                $country_id = '';
                if(!empty($query->country_id)) {
                    $selectCountry = Countries::select('country_name')->where([['country_id', '=', $query->country_id]])->get()->toArray();
                    if(count($selectCountry) > 0) {
                        if(!empty($selectCountry[0]['country_name'])) $country_id = $selectCountry[0]['country_name'];
                    }
                }
                return $country_id;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-state" data-id="'.$query->state_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit State"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-state" data-id="'.$query->state_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete State"><i class="fa fa-trash"></i></button></a>';
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
    //Edit State
    public function edit_config_State(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/state_form')->with([
                'state_data' => State::where([['state_id', '=', $request->id]])->get()->toArray(),
                'country_id' => Countries::where('status',0)->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Cities Delete
    public function delete_config_state(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = State::where('state_id', $request->id)->update(['status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }

    public function state_list_by_country(Request $request){

        $country_id = $request->country_id;
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
        echo $response;
        //return json_decode($response);
    }
    public function state_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/state_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $state_name_exist = 0;
                    $State = State::where([['state_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($State) > 0) {
                        $state_name_exist = 1;
                    }
                    $country_name = "";
                    $Countries = Countries::where([['country_name', '=', $row[2]], ['status', '=', '0']])->get()->toArray();
                    if(count($Countries) > 0) {
                        if(!empty($Countries[0]['country_name'])) $country_name = $Countries[0]['country_name'];
                    }
                    array_push($data, array('state_code' => $row[0], 'state_name' => $row[1], 'state_name_exist' => $state_name_exist, 'country_name' => $country_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_state_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['state_name'] != "") {
                $pdata = new State;
                $pdata->state_code = $data['state_code'];
                $pdata->country_id = $data['country_id'];
                $pdata->state_name = $data['state_name'];
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
                    $state_name = "";
                    $selectData = State::where([['state_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $state_name = "";
                    }else {
                        $state_name = $row[1];
                    }
                    
                    $country_id = "";
                    $Countries = Countries::where([['country_name', '=', $row[2]], ['status', '=', '0']])->get()->toArray();
                    if(count($Countries) > 0) {
                        if(!empty($Countries[0]['country_id'])) $country_id = $Countries[0]['country_id'];
                    }
                    
                    array_push($data, array('state_code' => $row[0], 'country_id' => $country_id, 'state_name' => $state_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function state_export(){
        $query = DB::table('state')->select('*')->orderBy('state_id', 'DESC')->where([['status', '!=', '1']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'State Code');
        $sheet->setCellValue('B1', 'State Name');
        $sheet->setCellValue('C1', 'Country');
        $rows = 2;
        foreach($query as $d2){
            $country_name = '';
            if(!empty($d2->country_id)) {
                $Countries = Countries::select('country_name')->where([['country_id', '=', $d2->country_id]])->get()->toArray();
                if(count($Countries) > 0) {
                    if(!empty($Countries[0]['country_name'])) $country_name = $Countries[0]['country_name'];
                }
            }
            $status = ($d2->status == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $d2->state_code);
            $sheet->setCellValue('B' . $rows, $d2->state_name);
            $sheet->setCellValue('C' . $rows, $country_name);
            $rows++;
        }
        $fileName = "state.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}