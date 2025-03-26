<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\ExchangeRate;
use App\Currency;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConfigExchangeRateController extends Controller {
    public function config_exchange_rate() {
        return \View::make("backend/config/config_exchange_rate")->with(array());
    }
    // Exchange Rate Modal  
    public function add_exchange_rate(){
        return \View::make("backend/config/exchange_rate_form")->with([
            'Currency' => Currency::select('currency_id', 'currency_code')->where([['status', '=', '1']])->orderBy('currency_id', 'desc')->get()->toArray()
        ])->render();
    }
    // Exchange Rate Insert/Update
    public function save_config_exchange_rate(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=ExchangeRate::where([['trading_date', '=', $request->trading_date], ['source_currency', '=', $request->source_currency], ['target_currency', '=', $request->target_currency], ['closing_rate', '=', $request->closing_rate], ['average_rate', '=', $request->average_rate], ['exchange_rate_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter Date already exist. Please try with another Date."];
            }else {
                $saveData=ExchangeRate::where('exchange_rate_id', $request->hidden_id)->update(array('trading_date'=>$request->trading_date,'source_currency'=>$request->source_currency,'target_currency'=>$request->target_currency, 'closing_rate' => $request->closing_rate, 'average_rate' => $request->average_rate));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Exchange Rate Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Exchange Rate Update failed! Something is wrong."];
                }
            }
            }else {
                $data = new ExchangeRate;
                $data->trading_date = $request->trading_date;
                $data->source_currency = $request->source_currency;
                $data->target_currency = $request->target_currency;
                $data->closing_rate = $request->closing_rate;
                $data->average_rate = $request->average_rate;
                $data->status = "0";
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => " Exchange Rate Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Exchange Rate Save failed! Something is wrong."];
                }
            }
        // }
        return response()->json($returnData);
    }
    // Exchange Rate dataTable
    public function list_config_exchange_rate(Request $request) {
        if ($request->ajax()) {
            $query = DB::table('exchange_rate');
            $query->select('*');
            $query->where([]);
            $query->orderBy('exchange_rate_id', 'DESC');
            $query->where([['status', '=', '0']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('trading_date', function ($query) {
                $trading_date = '';
                if(!empty($query->trading_date)) {
                    $trading_date .= date("d M Y",strtotime($query->trading_date));
                }
                return $trading_date;
            })
            ->addColumn('source_currency_code', function ($query) {
                $source_currency_code = '';
                if(!empty($query->source_currency)) {
                    $Currency = Currency::select('currency_code')->where([['currency_id', '=', $query->source_currency], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Currency)) {
                        if(!empty($Currency[0]['currency_code'])) $source_currency_code = $Currency[0]['currency_code'];
                    }
                }
                return $source_currency_code;
            })
            ->addColumn('target_currency_code', function ($query) {
                $target_currency_code = '';
                if(!empty($query->target_currency)) {
                    $Currency = Currency::select('currency_code')->where([['currency_id', '=', $query->target_currency], ['status', '=', '1']])->get()->toArray();
                    if(!empty($Currency)) {
                        if(!empty($Currency[0]['currency_code'])) $target_currency_code = $Currency[0]['currency_code'];
                    }
                }
                return $target_currency_code;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-exchange-rate" data-id="'.$query->exchange_rate_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Exchange Rate"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-exchange-rate" data-id="'.$query->exchange_rate_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Exchange Rate"><i class="fa fa-trash"></i></button></a>';
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
    // Exchange Rate Edit
    public function edit_config_exchange_rate(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.config.exchange_rate_form')->with([
                'exchange_rate_data' => ExchangeRate::where([['exchange_rate_id', '=', $request->id]])->get()->toArray(),
                'Currency' => Currency::select('currency_id', 'currency_code')->where([['status', '=', '1']])->orderBy('currency_id', 'desc')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Exchange Rate Delete
    public function delete_config_exhange_rate(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = ExchangeRate::where('exchange_rate_id', $request->id)->update(['status' => "1"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function exchange_rate_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/exchange_rate_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $source_currency = "";
                    $source_currency_exist = 0;
                    $sourceCurrency = Currency::where([['currency_id', '=', $row[2]], ['status', '!=', '2']])->get()->toArray();
                    if(count($sourceCurrency) > 0) {
                        $source_currency_exist = 1;
                        $source_currency = $sourceCurrency[0]['currency_code'];
                    }
                    $target_currency = "";
                    $target_currency_exist = 0;
                    $targetCurrency = Currency::where([['currency_id', '=', $row[3]], ['status', '!=', '2']])->get()->toArray();
                    if(count($targetCurrency) > 0) {
                        $target_currency_exist = 1;
                        $target_currency = $sourceCurrency[0]['currency_code'];
                    }
                    $currency_same = 0;
                    if($row[2] == $row[3]) {
                        $currency_same = 1;
                    }
                    $trading_date = date('Y-m-d', strtotime($row[1]));
                    array_push($data, array('source_currency_exist' => $source_currency_exist, 'source_currency' => $source_currency, 'target_currency' => $target_currency, 'target_currency_exist' => $target_currency_exist, 'currency_same' => $currency_same, 'trading_date' => $trading_date, 'source_currency_id' => $row[2], 'target_currency_id' => $row[3], 'closing_rate' => $row[4], 'average_rate' => $row[5]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_exchange_rate_bulk(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['source_currency_exist'] != 0 && $data['target_currency_exist'] != 0 && $data['currency_same'] != 1) {
                $pdata = new ExchangeRate;
                $pdata->trading_date = $data['trading_date'];
                $pdata->source_currency = $data['source_currency_id'];
                $pdata->target_currency = $data['target_currency_id'];
                $pdata->closing_rate = $data['closing_rate'];
                $pdata->average_rate = $data['average_rate'];
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
                    $source_currency = "";
                    $source_currency_exist = 0;
                    $sourceCurrency = Currency::where([['currency_id', '=', $row[2]], ['status', '!=', '2']])->get()->toArray();
                    if(count($sourceCurrency) > 0) {
                        $source_currency_exist = 1;
                        $source_currency = $sourceCurrency[0]['currency_code'];
                    }
                    $target_currency = "";
                    $target_currency_exist = 0;
                    $targetCurrency = Currency::where([['currency_id', '=', $row[3]], ['status', '!=', '2']])->get()->toArray();
                    if(count($targetCurrency) > 0) {
                        $target_currency_exist = 1;
                        $target_currency = $sourceCurrency[0]['currency_code'];
                    }
                    $currency_same = 0;
                    if($row[2] == $row[3]) {
                        $currency_same = 1;
                    }
                    $trading_date = date('Y-m-d', strtotime($row[1]));
                    array_push($data, array('source_currency_exist' => $source_currency_exist, 'source_currency' => $source_currency, 'target_currency' => $target_currency, 'target_currency_exist' => $target_currency_exist, 'currency_same' => $currency_same, 'trading_date' => $trading_date, 'source_currency_id' => $row[2], 'target_currency_id' => $row[3], 'closing_rate' => $row[4], 'average_rate' => $row[5]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function exchange_rate_export(){
        $query = DB::table('exchange_rate')
        ->select('*')
        ->where([['status', '=', '0']])
        ->orderBy('exchange_rate_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trading_date');
        $sheet->setCellValue('B1', 'Source_currency');
        $sheet->setCellValue('C1', 'Target_currency');
        $sheet->setCellValue('D1', 'Closing_rate');
        $sheet->setCellValue('E1', 'Average_rate');
        $rows = 2;
        foreach($data as $empDetails){
            $source_currency = "";
            $selectsource_currency = Currency::select('currency_code')->where('currency_id', $empDetails->source_currency)->get()->toArray();
            if(sizeof($selectsource_currency) > 0) {
                $source_currency = $selectsource_currency[0]['currency_code'];
            }
            $target_currency = "";
            $selecttarget_currency = Currency::select('currency_code')->where('currency_id', $empDetails->target_currency)->get()->toArray();
            if(sizeof($selecttarget_currency) > 0) {
                $target_currency = $selecttarget_currency[0]['currency_code'];
            }
            $sheet->setCellValue('A' . $rows, date('M d Y', strtotime($empDetails->trading_date)));
            $sheet->setCellValue('B' . $rows, $source_currency);
            $sheet->setCellValue('C' . $rows, $target_currency);
            $sheet->setCellValue('D' . $rows, $empDetails->closing_rate);
            $sheet->setCellValue('E' . $rows, $empDetails->average_rate);
            $rows++;
        }
        $fileName = "exchange_rate_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}