<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\CarManufacture;
use App\Brand;
use DB;
use DataTables;
use App\CarName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CarNameController extends Controller {
    // View
    public function car_name(){
        return \View::make("backend/item/car_name")->with([]);
    }
    // List
    public function list_car_name(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('car_name as c');
            $query->select('c.*', 'cm.car_manufacture', 'b.brand_name');
            $query->join('car_manufacture as cm', 'cm.car_manufacture_id', '=', 'c.car_manufacture_id', 'left');
            $query->join('car_model as b', 'b.brand_id', '=', 'c.brand_id', 'left');
            if($keyword) {
                $sql = "car_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('c.car_name', 'asc');
                else
                    $query->orderBy('c.car_name_id', 'desc');
            }else {
                $query->orderBy('c.car_name_id', 'DESC');
            }
            $query->where([['c.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-car-name" data-id="'.$query->car_name_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-car-name" data-id="'.$query->car_name_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    // Add
    public function add_car_name(Request $request){
        return \View::make("backend/item/car_name_form")->with([
            'CarManufacture' => CarManufacture::where([['status', '!=', '2']])->orderBy('car_manufacture', 'asc')->get()->toArray()
        ])->render();
    }
    // Save
    public function save_car_name(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData = CarName::where([['car_manufacture_id', '=', $request->car_manufacture_id], ['brand_id', '=', $request->brand_id], ['car_name', '=', $request->car_name], ['car_name_id', '!=', $request->hidden_id], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
                $saveData = CarName::where('car_name_id', $request->hidden_id)->update(array('car_manufacture_id'=>$request->car_manufacture_id, 'brand_id'=>$request->brand_id, 'car_name'=>$request->car_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData = CarName::where([['car_manufacture_id', '=', $request->car_manufacture_id], ['brand_id', '=', $request->brand_id], ['car_name', '=', $request->car_name], ['status', '!=', '2']])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please try with another name."];
            }else {
            	$data = new CarName;
                $data->car_manufacture_id = $request->car_manufacture_id;
                $data->brand_id = $request->brand_id;
            	$data->car_name = $request->car_name;
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
    // Edit
    public function edit_car_name(Request $request) {
        if ($request->ajax()) {
            $CarModel = [];
            $CarName = CarName::where([['car_name_id', '=', $request->id]])->get()->toArray();
            if(sizeof($CarName) > 0) {
                if(!empty($CarName[0]['car_manufacture_id'])) {
                    $CarModel = Brand::where([['car_manufacture_id', '=', $CarName[0]['car_manufacture_id']]])->get()->toArray();
                }
            }
            $html = view('backend/item/car_name_form')->with([
                'CarManufacture' => CarManufacture::where([['status', '!=', '2']])->orderBy('car_manufacture', 'asc')->get()->toArray(),
                'CarName' =>  $CarName,
                'CarModel' =>  $CarModel,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_car_name(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $saveData = CarName::where('car_name_id', $request->id)->update(['status' => "2"]);
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    //
    public function get_car_model_by_car_manufacture(Request $request) {
        $returnData = [];
        $CarModel = Brand::select('brand_id', 'brand_name')->where([['car_manufacture_id', '=', $request->id]])->get()->toArray();
        if(sizeof($CarModel) > 0) {
            $returnData = ["status" => 1, "data" => $CarModel];
        }else {
            $returnData = ["status" => 0, "msg" => "No record found"];
        }
        return response()->json($returnData);
    }
    public function car_name_export(){
        $query = CarName::OrderBy('car_name_id', 'DESC')->where([['status', '!=', '2']])->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Car Name');
        $sheet->setCellValue('B1', 'Car Model');
        $sheet->setCellValue('C1', 'Car Manufacture');
        $rows = 2;
        foreach($query as $td){
            $car_manufacture = '';
            if(!empty($td['car_manufacture_id'])) {
                $CarManufacture = CarManufacture::select('car_manufacture')->where([['car_manufacture_id', '=', $td['car_manufacture_id']]])->get()->toArray();
                if(sizeof($CarManufacture) > 0) {
                    $car_manufacture = $CarManufacture[0]['car_manufacture'];
                }
            }
            $car_model = '';
            if(!empty($td['car_manufacture_id'])) {
                $Brand = Brand::select('brand_name')->where([['brand_id', '=', $td['brand_id']]])->get()->toArray();
                if(sizeof($Brand) > 0) {
                    $car_model = $Brand[0]['brand_name'];
                }
            }
            $sheet->setCellValue('A' . $rows, $td['car_name']);
            $sheet->setCellValue('B' . $rows, $car_model);
            $sheet->setCellValue('C' . $rows, $car_manufacture);
            $rows++;
        }
        $fileName = "car_name.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}