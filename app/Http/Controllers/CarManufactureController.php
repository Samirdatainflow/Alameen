<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\CarManufacture;
use App\Products;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CarManufactureController extends Controller {

    public function car_manufacture(){
        return \View::make("backend/item/car_manufacture")->with([]);
    }
    public function list_car_manufacture(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('car_manufacture');
            $query->select('*');
            if($keyword) {
                $sql = "car_manufacture like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('car_manufacture', 'asc');
                else
                    $query->orderBy('car_manufacture_id', 'desc');
            }else {
                $query->orderBy('car_manufacture_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $Products = Products::select('car_manufacture_id')->where([['car_manufacture_id', '=', $query->car_manufacture_id]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-car-manufacture" data-id="'.$query->car_manufacture_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-car-manufacture" data-id="'.$query->car_manufacture_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-car-manufacture" data-id="'.$query->car_manufacture_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    public function add_car_manufacture(Request $request){
        return \View::make("backend/item/car_manufacture_form")->with([])->render();
    }
    public function save_car_manufacture(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = CarManufacture::where([['car_manufacture', '=', $request->car_manufacture], ['car_manufacture_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData = CarManufacture::where('car_manufacture_id', $request->hidden_id)->update(array('car_manufacture'=>$request->car_manufacture));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = CarManufacture::where([['car_manufacture', '=', $request->car_manufacture], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new CarManufacture;
            	$data->car_manufacture = $request->car_manufacture;
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
    public function edit_car_manufacture(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/item/car_manufacture_form')->with([
          	'CarManufacture' =>  CarManufacture::where([['car_manufacture_id', '=', $request->id]])->get()->toArray(),
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_car_manufacture(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = CarManufacture::where('car_manufacture_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function car_manufacture_bulk_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $dataArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/item/car_manufacture_bulk_preview")->with(array('dataArr'=>$dataArr['data']));
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
                    $car_manufacture_exist = 0;
                    $selectData = CarManufacture::where([['car_manufacture', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $car_manufacture_exist = 1;
                    }else {
                        $car_manufacture_exist = 0;
                    }
                    array_push($data, array('car_manufacture' => $row[0], 'car_manufacture_exist' => $car_manufacture_exist));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function save_car_manufacture_bulk_csv(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $flag=0;
        $productArr = $this->csvToArray($file);
        foreach($productArr['data'] as $data) {
            if($data['car_manufacture'] != "") {
                $pdata = new CarManufacture;
                $pdata->car_manufacture = $data['car_manufacture'];
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
                    $car_manufacture = "";
                    $selectData = CarManufacture::where([['car_manufacture', '=', $row[0]], ['status', '!=', '2']])->get()->toArray();
                    if(count($selectData) > 0) {
                        $car_manufacture = "";
                    }else {
                        $car_manufacture = $row[0];
                    }
                    array_push($data, array('car_manufacture' => $car_manufacture));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    public function car_manufacture_export(){
        $query = CarManufacture::OrderBy('car_manufacture_id', 'ASC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Car Manufacture');
        $rows = 2;
        foreach($query as $empDetails){
            $sheet->setCellValue('A' . $rows, $empDetails['car_manufacture']);
            $rows++;
        }
        $fileName = "car_manufacture.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}