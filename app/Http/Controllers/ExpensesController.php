<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Expenses;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ExpensesController extends Controller {

    public function index() {
        return \View::make("backend/config/expenses")->with(array());
    }
    // Sms Api Key Modal
    public function expenses_form(){

        return \View::make("backend/config/expenses_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_expenses(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=Expenses::where([['expenses_description', '=', $request->expenses_description], ['expenses_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter expense already exist. Please enter new name."];
            }else {
                $saveData=Expenses::where('expenses_id', $request->hidden_id)->update(array('expenses_description'=>$request->expenses_description));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=Expenses::where([['expenses_description', '=', $request->expenses_description]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter expense already exist. Please enter new name."];
            }else {
            	$data = new Expenses;
            	$data->expenses_description = $request->expenses_description;
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
    public function list_expenses(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('expenses');
            $query->select('*');
            if($keyword)
            {
                $sql = "expenses_description like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('expenses_description', 'asc');
                else
                    $query->orderBy('expenses_id', 'desc');
            }
            else
            {
                $query->orderBy('expenses_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-expenses" data-id="'.$query->expenses_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Expenses"><i class="fa fa-pencil"></i></button></a>';
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
    public function edit_expenses(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/expenses_form')->with([
                'expenses_data' => Expenses::where([['expenses_id', '=', $request->id]])->get()->toArray()
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
    public function expenses_export(){
        $query = DB::table('expenses')
        ->select('*')
        ->orderBy('expenses_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Expense Description');
        $rows = 2;
        foreach($data as $d2){
            $sheet->setCellValue('A' . $rows, $d2->expenses_description);
            $rows++;
        }
        $fileName = "Expense.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}