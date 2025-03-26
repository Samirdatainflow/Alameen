<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Currency;
use App\Products;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConfigCurrencyController extends Controller {
    public function config_currency() {
        return \View::make("backend/config/config_currency")->with(array());
    }
    // Currency Modal 
    public function add_currency(){
        return \View::make("backend/config/currency_form")->with([
        ])->render();
    }
    // Currency dataTable
    public function list_config_currency(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('currency');
            $query->select('currency_id','currency_code', 'currency_description', 'status');
            if($keyword)
            {
                $sql = "currency_description like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('currency_description', 'asc');
                else
                    $query->orderBy('currency_id', 'desc');
            }
            else
            {
                $query->orderBy('currency_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                        $status .= '<a href="javascript:void(0)" class="currency-change-status" data-id="'.$query->currency_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="currency-change-status" data-id="'.$query->currency_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $Products = Products::select('supplier_currency')->where([['supplier_currency', '=', $query->currency_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-currency" data-id="'.$query->currency_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Currency"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-currency" data-id="'.$query->currency_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Currency"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-currency" data-id="'.$query->currency_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Currency"><i class="fa fa-trash"></i></button></a>';
                }
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
    // Currency Insert/Update
    public function save_config_currency(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Currency::where([['currency_code', '=', $request->currency_code], ['currency_description', '=', $request->currency_description], ['currency_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Currency Code already exist. Please try with another Currency Code."];
            }else {
                $saveData=Currency::where('currency_id', $request->hidden_id)->update(array('currency_code'=>$request->currency_code,'currency_description'=>$request->currency_description, 'status' => '1' ));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Currency Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Currency Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Currency::where([['currency_code', '=', $request->currency_code], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Currency Code already exist. Please try with another Currency Code."];
            }else {
            	$data = new Currency;
            	$data->currency_code = $request->currency_code;
            	$data->currency_description = $request->currency_description;
            	$data->status = "1";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Currency Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Currency Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Currency Change Status
    public function change_currency_status(Request $request){
        $res=Currency::where('currency_id',$request->id)->update(array('status'=> $request->status));
        if($res)
        {
            $returnData = ["status" => 1, "msg" => "Status change successful."];
        }
        else{
            $returnData = ["status" => 0, "msg" => "Status change faild."];
        }
        return response()->json($returnData);
    }
    // Currency Edit
    public function edit_config_currency(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.currency_form')->with([
                'currency_data' => Currency::where([['currency_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    } 
    // Currency Delete
    public function delete_config_currency(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Currency::where('currency_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function currency_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/currency_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $currency_code_exist = 0;
                    $Currency = Currency::where([['currency_code', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($Currency) > 0) {
                        $currency_code_exist = 1;
                    }
                    array_push($data, array('currency_code' => $row[1], 'currency_description' => $row[2], 'currency_code_exist' => $currency_code_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_currency_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['currency_code'] != "") {
                $pdata = new Currency;
                $pdata->currency_code = $data['currency_code'];
                $pdata->currency_description = $data['currency_description'];
                $pdata->status = "1";
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
                    $currency_code = "";
                    $selectData = Currency::where([['currency_code', '=', $row[1]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $currency_code = "";
                    }else {
                        $currency_code = $row[1];
                    }
                    array_push($data, array('currency_code' => $currency_code, 'currency_description' => $row[2]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function currency_export(){
        $query = DB::table('currency')
        ->select('currency_id','currency_code', 'currency_description', 'status')
        ->where([['status', '!=', '2']])
        ->orderBy('currency_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Currency Code');
        $sheet->setCellValue('B1', 'Currency Description');
        $sheet->setCellValue('C1', 'Status');
        $rows = 2;
        foreach($data as $empDetails){
            $status = ($empDetails->status == 1)?'Active':'Inactive'; 
            $sheet->setCellValue('A' . $rows, $empDetails->currency_code);
            $sheet->setCellValue('B' . $rows, $empDetails->currency_description);
            $sheet->setCellValue('C' . $rows, $status);
            $rows++;
        }
        $fileName = "currency_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
}