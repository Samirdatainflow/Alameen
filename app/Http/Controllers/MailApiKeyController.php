<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\MailApiKey;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MailApiKeyController extends Controller {

    public function mail_api_key() {
        return \View::make("backend/config/mail_api_key")->with(array());
    }
    // Sms Api Key Modal
    public function mail_api_key_form(){

        return \View::make("backend/config/mail_api_key_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_mail_api_key(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=MailApiKey::where([['smtp_user', '=', $request->smtp_user], ['smtp_pass', '=', $request->smtp_pass],['smtp_host', '=', $request->smtp_host], ['smtp_port', '=', $request->smtp_port], ['from_mail', '=', $request->from_mail], ['from_name', '=', $request->from_name], ['id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter details already exist. Please try with another."];
            }else {
                $saveData=MailApiKey::where('id', $request->hidden_id)->update(array('smtp_user'=>$request->smtp_user,'smtp_pass'=>$request->smtp_pass,'smtp_port'=>$request->smtp_port,'smtp_host'=>$request->smtp_host,'from_mail'=>$request->from_mail,'from_name'=>$request->from_name ));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Mail SMTP details Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Mail SMTP details Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=MailApiKey::where([['smtp_user', '=', $request->smtp_user]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter smtp user already exist. Please try with another smtp user."];
            }else {
            	$data = new MailApiKey;
            	$data->smtp_user = $request->smtp_user;
                $data->smtp_pass = $request->smtp_pass;
                $data->smtp_host = $request->smtp_host;
                $data->smtp_port = $request->smtp_port;
                $data->from_mail = $request->from_mail;
                $data->from_name = $request->from_name;
                $data->status = "0";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Mail SMTP details Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Mail SMTP details Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // dataTable
    public function list_mail_api_key(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('mail_api_key');
            $query->select('*');
            if($keyword)
            {
                $sql = "smtp_host like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('smtp_host', 'asc');
                else
                    $query->orderBy('smtp_host', 'desc');
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
                        $status .= '<a href="javascript:void(0)" class="mail-api-key-change-status" data-id="'.$query->id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="mail-api-key-change-status" data-id="'.$query->id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-mail-api-key" data-id="'.$query->id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Mail Api Key"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-mail-api-key" data-id="'.$query->id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Mail Api Key"><i class="fa fa-trash"></i></button></a>';
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
    public function edit_mail_api_key(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/mail_api_key_form')->with([
                'mail_data' => MailApiKey::where([['id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    public function delete_mail_api_key(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = MailApiKey::where('id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    // Change Status
    public function change_mail_api_status(Request $request){
	 	$returnData = [];
        if ($request->ajax()) {
            if($request->status == "1")
            {
                $res=MailApiKey::where('id',$request->id)->update(array('status'=> $request->status));
                if($res)
                {
                    MailApiKey::where('id','!=',$request->id)->where('status','!=','2')->update(array('status'=> '0'));
                    $returnData = ["status" => 1, "msg" => "Status change successful."];
                }
                else{
                    $returnData = ["status" => 0, "msg" => "Status change faild."];
                }
            }
            else
            {
                $returnData = ["status" => 0, "msg" => "You need to active atleast 1 record"];
            }
	        
	    }
        return response()->json($returnData);
    }
    public function mail_config_export(){
        $query = DB::table('mail_api_key')->where([['status', '!=', '2']])->orderBy('id', 'desc')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Host Name');
        $sheet->setCellValue('B1', 'User Name');
        $sheet->setCellValue('C1', 'SMTP Port');
        $sheet->setCellValue('D1', 'From Mail');
        $sheet->setCellValue('E1', 'From Name');
        $sheet->setCellValue('F1', 'Status');
        $rows = 2;
        foreach($query as $d2){
            $status = ($d2->status == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $d2->smtp_host);
            $sheet->setCellValue('B' . $rows, $d2->smtp_user);
            $sheet->setCellValue('C' . $rows, $d2->smtp_port);
            $sheet->setCellValue('D' . $rows, $d2->from_mail);
            $sheet->setCellValue('E' . $rows, $d2->from_name);
            $sheet->setCellValue('F' . $rows, $status);
            $rows++;
        }
        $fileName = "mail_config.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}