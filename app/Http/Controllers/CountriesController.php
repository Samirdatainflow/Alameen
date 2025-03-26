<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Countries;
use App\Products;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CountriesController extends Controller {

    public function countries() {
        return \View::make("backend/config/countries")->with(array());
    }
    // Countries Modal
    public function countries_form(){
    	return \View::make("backend/config/countries_form")->with(array());
    }
    // Insert/ Update
    public function save_config_countries(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Countries::where([['country_code', '=', $request->country_code], ['country_name', '=', $request->country_name], ['country_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData=Countries::where('country_id', $request->hidden_id)->update(array('country_code'=>$request->country_code, 'country_name'=>$request->country_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Countries Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Countries Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Countries::where([['country_code', '=', $request->country_code]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new Countries;
            	$data->country_code = $request->country_code;
            	$data->country_name = $request->country_name;
                // print_r($data); exit();
                $saveData= $data->save();
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Countries Save successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Countries Save failed! Something is wrong."];
                }
            }
        }
        return response()->json($returnData);
    }
    // Countries dataTable
    public function list_config_country(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('countries');
            $query->select('*');
            if($keyword)
            {
                $sql = "country_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('country_name', 'asc');
                else
                    $query->orderBy('country_id', 'desc');
            }
            else
            {
                $query->orderBy('country_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Products = Products::select('country_of_origin')->where([['country_of_origin', '=', $query->country_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-country" data-id="'.$query->country_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Country"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-country" data-id="'.$query->country_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Country"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-country" data-id="'.$query->country_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Country"><i class="fa fa-trash"></i></button></a>';
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
    //Edit Country
    public function edit_config_country(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/countries_form')->with([
                'country_data' => Countries::where([['country_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Country Delete
    public function delete_config_country(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = Countries::where('country_id', $request->id)->delete();
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function countries_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/config/countries_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $country_name_exist = 0;
                    $Countries = Countries::where([['country_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($Countries) > 0) {
                        $country_name_exist = 1;
                    }
                    array_push($data, array('country_code' => $row[0], 'country_name' => $row[1], 'country_name_exist' => $country_name_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_countries_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['country_name'] != "") {
                $pdata = new Countries;
                $pdata->country_code = $data['country_code'];
                $pdata->country_name = $data['country_name'];
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
                    $country_name = "";
                    $selectData = Countries::where([['country_name', '=', $row[1]], ['status', '=', '0']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $country_name = "";
                    }else {
                        $country_name = $row[1];
                    }
                    array_push($data, array('country_code' => $row[0], 'country_name' => $country_name));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function countries_export(){
        $query = DB::table('countries')
        ->select('*')
        ->orderBy('country_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Country Code');
        $sheet->setCellValue('B1', 'Country Name');
        $rows = 2;
        foreach($data as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails->country_code);
            $sheet->setCellValue('B' . $rows, $empDetails->country_name);
            $rows++;
        }
        $fileName = "countries_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}