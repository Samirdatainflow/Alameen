<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\SmsApiKey;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmsApiKeyController extends Controller {

    public function sms_api_key() {
        return \View::make("backend/config/sms_api_key")->with(array());
    }
    // Sms Api Key Modal
    public function sms_api_key_form(){
        return \View::make("backend/config/sms_api_key_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_sms_api_key(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=SmsApiKey::where([['api_key', '=', $request->api_key], ['id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Api key already exist. Please try with another Api Key."];
            }else {
                $saveData=SmsApiKey::where('id', $request->hidden_id)->update(array('api_key'=>$request->api_key));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Api key Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => " Api key Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=SmsApiKey::where([['api_key', '=', $request->api_key]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Api key already exist. Please try with another Api key."];
            }else {
            	$data = new SmsApiKey;
            	$data->api_key = $request->api_key;
                $data->status = "1";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " Api key Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Api key Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // dataTable
    public function list_sms_api_key(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('sms_api_key');
            $query->select('id','api_key', 'status');
            if($keyword)
            {
                $sql = "api_key like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('api_key', 'asc');
                else
                    $query->orderBy('api_key', 'desc');
            }
            else
            {
                $query->orderBy('id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="api-key-change-status" data-id="'.$query->id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="api-key-change-status" data-id="'.$query->id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-api-key" data-id="'.$query->id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Api Key"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-api-key" data-id="'.$query->id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Api Key"><i class="fa fa-trash"></i></button></a>';
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
    //Edit Api Key
    public function edit_api_key(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/sms_api_key_form')->with([
                'api_data' => SmsApiKey::where([['id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    public function delete_api_key(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = SmsApiKey::where('id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // Change Status
    public function change_api_status(Request $request){
	 	$returnData = [];
        if ($request->ajax()) {
	        $res=SmsApiKey::where('id',$request->id)->update(array('status'=> $request->status));
	        if($res)
	        {
	            $returnData = ["status" => 1, "msg" => "Status change successful."];
	        }
	        else{
	            $returnData = ["status" => 0, "msg" => "Status change faild."];
	        }
	    }
        return response()->json($returnData);
    }
    public function sms_api_key_export(){
        $query = DB::table('sms_api_key')->where([['status', '!=', '2']])->orderBy('id', 'desc')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'SMS API Key');
        $sheet->setCellValue('B1', 'Status');
        $rows = 2;
        foreach($query as $d2){
            $status = ($d2->status == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $d2->api_key);
            $sheet->setCellValue('B' . $rows, $status);
            $rows++;
        }
        $fileName = "sms_api_key.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}