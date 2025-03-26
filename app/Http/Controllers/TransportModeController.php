<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\TransportMode;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransportModeController extends Controller {

    public function transport_mode() {
        return \View::make("backend/config/transport_mode")->with(array());
    }
    // Sms Api Key Modal
    public function transport_mode_form(){

        return \View::make("backend/config/transport_mode_form")->with([
        ])->render();
    }
    // Insert/ Update
    public function save_transport_mode(Request $request){
        if(!empty($request->hidden_id)) {
            $selectData=TransportMode::where([['transport_mode', '=', $request->transport_mode], ['transport_mode_id', '!=', $request->hidden_id]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter mod already exist. Please enter new mode."];
            }else {
                $saveData=TransportMode::where('transport_mode_id', $request->hidden_id)->update(array('transport_mode'=>$request->transport_mode));
                if($saveData) {
                    $returnData = ["status" => 1, "msg" => "Update successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
                }
            }
        }else {
            $selectData=TransportMode::where([['transport_mode', '=', $request->transport_mode]])->get()->toArray();
            if(count($selectData) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter mod already exist. Please enter new mode."];
            }else {
            	$data = new TransportMode;
            	$data->transport_mode = $request->transport_mode;
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
    public function list_transport_mode(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('transport_mode');
            $query->select('*');
            if($keyword)
            {
                $sql = "transport_mode like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('transport_mode', 'asc');
                else
                    $query->orderBy('transport_mode', 'desc');
            }
            else
            {
                $query->orderBy('transport_mode_id', 'DESC');
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-transport-mode" data-id="'.$query->transport_mode_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Transport Mode"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-transport-mode" data-id="'.$query->transport_mode_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete Transport Mode"><i class="fa fa-trash"></i></button></a>';
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
    public function edit_transport_mode(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/config/transport_mode_form')->with([
                'transport_mode_data' => TransportMode::where([['transport_mode_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete Api Key
    public function delete_transport_mode(Request $request) {
        $returnData = [];
        if ($request->ajax()) {
            $deleteData = TransportMode::where('transport_mode_id', $request->id)->delete();
            if($deleteData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    public function transport_mode_export(){
        $query = DB::table('transport_mode')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $rows = 2;
        foreach($query as $d2){
            $sheet->setCellValue('A' . $rows, $d2->transport_mode);
            $rows++;
        }
        $fileName = "transport_mode.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}