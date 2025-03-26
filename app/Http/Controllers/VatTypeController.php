<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\VatType;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VatTypeController extends Controller {

    public function index() {
        return \View::make("backend/config/vat_type")->with(array());
    }
    // Sms Api Key Modal
    public function vat_type_form(){

        return \View::make("backend/config/vat_type_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_vat_type(Request $request){
        
        if(!empty($request->hidden_id)) {
            
            $selectData = VatType::where([['description', '=', $request->description], ['vat_type_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter type already exist. Please enter new type."];
            }else {
                $saveData = VatType::where('vat_type_id', $request->hidden_id)->update(array('description'=>$request->vat_type, 'percentage'=>$request->percentage));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            
            $selectData=VatType::where([['description', '=', $request->vat_type]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter type already exist. Please enter new type."];
            }else {
            	$data = new VatType;
            	$data->description = $request->vat_type;
            	$data->percentage = $request->percentage;
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
    public function list_vat_type(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('vat_type');
            $query->select('*');
            if($keyword)
            {
                $sql = "type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('vat_type_id', 'asc');
                else
                    $query->orderBy('vat_type_id', 'desc');
            }
            else
            {
                $query->orderBy('vat_type_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-vat-type" data-id="'.$query->vat_type_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Vat Type"><i class="fa fa-pencil"></i></button></a>';
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
    //Edit Api Key
    public function edit_vat_type(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/vat_type_form')->with([
                'VatTypeData' => VatType::where([['vat_type_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    // public function delete_additional_charges(Request $request) {
    //     $returnData = [];
    //     if ($request->ajax()) {
    //         $deleteData = AdditionalCharges::where('additional_charges_id', $request->id)->delete();
    //         if($deleteData) {
    //             $returnData = ["status" => 1, "msg" => "Delete successful."];
    //         }else {
    //             $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
    //         }
    //     }
    //     return response()->json($returnData);
    // }
    public function vat_type_export(){
        $query = DB::table('vat_type')
        ->select('*')
        ->orderBy('vat_type_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Vat Type');
        $sheet->setCellValue('B1', 'Percentage');
        $rows = 2;
        foreach($data as $d2){
            $sheet->setCellValue('A' . $rows, $d2->description);
            $sheet->setCellValue('B' . $rows, $d2->percentage);
            $rows++;
        }
        $fileName = "VaType.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}