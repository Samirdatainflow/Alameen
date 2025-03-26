<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\CourierCompany;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CourierCompanyController extends Controller {

    public function courier_company() {
        return \View::make("backend/config/courier_company")->with(array());
    }
    // Sms Api Key Modal
    public function courier_company_form(){

        return \View::make("backend/config/courier_company_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_courier_company(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=CourierCompany::where([['company_name', '=', $request->company_name], ['courier_company_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter company name already exist. Please enter new company name."];
            }else {
                $saveData=CourierCompany::where('courier_company_id', $request->hidden_id)->update(array('company_name'=>$request->company_name));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=CourierCompany::where([['company_name', '=', $request->company_name]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter company name already exist. Please enter new company name."];
            }else {
            	$data = new CourierCompany;
            	$data->company_name = $request->company_name;
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
    public function list_courier_company(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('courier_company');
            $query->select('*');
            if($keyword)
            {
                $sql = "company_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('company_name', 'asc');
                else
                    $query->orderBy('company_name', 'desc');
            }
            else
            {
                $query->orderBy('courier_company_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-courier-company" data-id="'.$query->courier_company_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Courier Company"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-courier-company" data-id="'.$query->courier_company_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Courier Company"><i class="fa fa-trash"></i></button></a>';
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
    public function edit_courier_company(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/courier_company_form')->with([
                'courier_company_data' => CourierCompany::where([['courier_company_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    public function delete_courier_company(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $deleteData = CourierCompany::where('courier_company_id', $request->id)->delete();
            if($deleteData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function courier_company_export(){
        $query = DB::table('courier_company')
        ->select('*')
        ->orderBy('courier_company_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Company Name');
        $rows = 2;
        foreach($data as $empDetails){
            
            $sheet->setCellValue('A' . $rows, $empDetails->company_name);
            $rows++;
        }
        $fileName = "courier_company_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}