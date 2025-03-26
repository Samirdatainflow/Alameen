<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\AdditionalCharges;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class AdditionalChargesController extends Controller {

    public function additional_charges() {
        return \View::make("backend/config/additional_charges")->with(array());
    }
    // Sms Api Key Modal
    public function additional_charges_form(){

        return \View::make("backend/config/additional_charges_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_additional_charges(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=AdditionalCharges::where([['name', '=', $request->name], ['additional_charges_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please enter new name."];
            }else {
                $saveData=AdditionalCharges::where('additional_charges_id', $request->hidden_id)->update(array('name'=>$request->name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=AdditionalCharges::where([['name', '=', $request->name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter name already exist. Please enter new name."];
            }else {
            	$data = new AdditionalCharges;
            	$data->name = $request->name;
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
    // dataTable
    public function list_additional_charges(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('additional_charges');
            $query->select('*');
            if($keyword)
            {
                $sql = "name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('name', 'asc');
                else
                    $query->orderBy('name', 'desc');
            }
            else
            {
                $query->orderBy('additional_charges_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-additional-charges" data-id="'.$query->additional_charges_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Additional Charges"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-additional-charges" data-id="'.$query->additional_charges_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Additional Charges"><i class="fa fa-trash"></i></button></a>';
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
    public function edit_additional_charges(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/additional_charges_form')->with([
                'additional_Charges_data' => AdditionalCharges::where([['additional_charges_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    public function delete_additional_charges(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $deleteData = AdditionalCharges::where('additional_charges_id', $request->id)->delete();
            if($deleteData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function additional_charges_export(){
        $query = DB::table('additional_charges')
        ->select('*')
        ->orderBy('additional_charges_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $rows = 2;
        foreach($data as $d2){
            $sheet->setCellValue('A' . $rows, $d2->name);
            $rows++;
        }
        $fileName = "additional_charges.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}