<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Payment;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class PaymentController extends Controller {

    public function payment() {
        return \View::make("backend/config/payment")->with(array());
    }
    // Payment Modal
    public function Payment_form(){
    	return \View::make("backend/config/Payment_form")->with(array());
    }
     // Insert/ Update
     public function save_config_payment(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Payment::where([['payment_method', '=', $request->payment_method], ['payment_description', '=', $request->payment_description], ['payment_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Payment Method already exist. Please try with another Payment Method."];
            }else {
                $saveData=Payment::where('payment_id', $request->hidden_id)->update(array('payment_method'=>$request->payment_method, 'payment_description'=>$request->payment_description));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Payment Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Payment Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Payment::where([['payment_method', '=', $request->payment_method]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Payment Method already exist. Please try with another Payment Method."];
            }else {
            	$data = new Payment;
            	$data->payment_method = $request->payment_method;
            	$data->payment_description = $request->payment_description;
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Payment Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Payment Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }

    // export
    public function payment_export_excel()
    {
        $query = Payment::OrderBy('payment_id', 'ASC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Payment_ID');
        $sheet->setCellValue('B1', 'Payment_Method');
        $sheet->setCellValue('C1', 'Payment_Description');
        
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['payment_id']);
            $sheet->setCellValue('B' . $rows, $empDetails['payment_method']);
            $sheet->setCellValue('C' . $rows, $empDetails['payment_description']);
            $rows++;
        }
        $fileName = "Payemnt.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // export
     // Payment dataTable
    public function list_config_payment(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('payment');
            $query->select('*');
            if($keyword)
            {
                $sql = "payment_method like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('payment_method', 'asc');
                else
                    $query->orderBy('payment_method', 'desc');
            }
            else
            {
                $query->orderBy('payment_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-payment" data-id="'.$query->payment_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Payment"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-payment" data-id="'.$query->payment_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Payment"><i class="fa fa-trash"></i></button></a>';
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
     //Edit Payment
    public function edit_config_payment(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/Payment_form')->with([
                'payment_data' => Payment::where([['payment_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
     // Payment Delete
    public function delete_config_payment(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Payment::where('payment_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function payment_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/payment_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $payment_method_exist = 0;
                    $Payment = Payment::where([['payment_method', '=', $row[1]]])->get()->toArray();
                    if(count($Payment) > 0) {
                        $payment_method_exist = 1;
                    }
                    array_push($data, array('payment_method_exist' => $payment_method_exist, 'payment_method' => $row[1], 'payment_description' => $row[2]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_payment_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['payment_method_exist'] == 0) {
                $pdata = new Payment;
                $pdata->payment_method = $data['payment_method'];
                $pdata->payment_description = $data['payment_description'];
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
                    $payment_method_exist = 0;
                    $Payment = Payment::where([['payment_method', '=', $row[1]]])->get()->toArray();
                    if(count($Payment) > 0) {
                        $payment_method_exist = 1;
                    }
                    array_push($data, array('payment_method_exist' => $payment_method_exist, 'payment_method' => $row[1], 'payment_description' => $row[2]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
}