<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\GateEntry;
use App\Orders;
use App\ConsignmentReceipt;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GateEntryController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/gate_entry")->with(array());
    }
    // Add Form
    public function add_gate_entry(Request $request){
        if ($request->ajax()) {
            $order_id = [];
            //DB::enableQueryLog();
            $query = DB::table('orders as o');
            $query->select('o.order_id');
            // $query->join('gate_entry as g', 'o.order_id', '=', 'g.order_number', 'left');
            // $query->where([['o.is_delete', '=', 0], ['o.orders_status', '=', '1']]);
            // $query->whereRaw("(`g`.`order_number` is null or `g`.`status`=2)");
            $query->where([['orders_status', '=', '1']]);
            $listOrderIds = $query->get()->toArray();
           
            foreach($listOrderIds as $key=>$listOrderId)
            {
                $query1 = DB::table('gate_entry as g');
                $query1->select('*');
                $query1->where([['g.order_number', '=', $listOrderId->order_id], ['g.status','!=',2]]);
                $qry = $query1->get()->toArray();
                if(sizeof($qry)>0)
                {
                    unset($listOrderIds[$key]);
                }
            }
            // print_r(array_values($listOrderIds));
             //die;
            //dd(DB::getQueryLog());
            $html = view('backend.receiving_and_putaway.gate_entry_form')->with([
                'listOrderIds' =>array_values($listOrderIds)
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Check Order No
    public function check_order_number(Request $request) {
        if ($request->ajax()) {
            $Orders = Orders::where([['order_id', '=', $request->order_number], ['is_delete', '=', '0']])->get()->toArray();
            if(sizeof($Orders) > 0) {
                return response()->json(["status" => 1]);
            }else {
                return response()->json(["status" => 0, "msg" => "You have enter incorrect Order No!"]);
            }
        }
    }
    public function save_gate_entry(Request $request){
        if(!empty($request->hidden_id)) {
            $order_date = str_replace('/', '-', $request->order_date);
            $vehicle_in_out_date = str_replace('/', '-', $request->vehicle_in_out_date);
            $courier_date = str_replace('/', '-', $request->courier_date);
            $saveData=GateEntry::where('gate_entry_id', $request->hidden_id)->update(array('transaction_type' => $request->transaction_type, 'order_date' => date('Y-m-d', strtotime($order_date)), 'vehicle_no' => $request->vehicle_no, 'driver_name' => $request->driver_name, 'contact_no' => $request->contact_no, 'vehicle_in_out_date' => date('Y-m-d', strtotime($vehicle_in_out_date)), 'courier_date' => date('Y-m-d', strtotime($courier_date)), 'courier_number' => $request->courier_number, 'no_of_box' => $request->no_of_box));
            if($saveData) {
                $returnData = ["status" => 1, "msg" => "Update successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Update failed! Something is wrong."];
            }
        }else {
            $GateEntry = GateEntry::where([['order_number', '=', $request->order_number], ['status', '=', '1']])->get()->toArray();
            if(sizeof($GateEntry) > 0) {
                $returnData = ["status" => 0, "msg" => "Enter order no already exist. Please try with another order no."];
            }else {
                $order_date = str_replace('/', '-', $request->order_date);
                $vehicle_in_out_date = str_replace('/', '-', $request->vehicle_in_out_date);
                $courier_date = str_replace('/', '-', $request->courier_date);
                $data = new GateEntry;
                $data->transaction_type = $request->transaction_type;
                $data->order_number = $request->order_number;
                $data->order_date = date('Y-m-d', strtotime($order_date));
                $data->vehicle_no = $request->vehicle_no;
                $data->driver_name = $request->driver_name;
                $data->contact_no = $request->contact_no;
                $data->vehicle_in_out_date = date('Y-m-d', strtotime($vehicle_in_out_date));
                $data->courier_date = date('Y-m-d', strtotime($courier_date));
                $data->courier_number = $request->courier_number;
                $data->no_of_box = $request->no_of_box;
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
    // List
    public function list_gate_entry(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('gate_entry');
            $query->select('*');
            if($keyword)
            {
                $sql = "transaction_type like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('transaction_type', 'asc');
                else
                    $query->orderBy('gate_entry_id', 'desc');
            }
            else
            {
                $query->orderBy('gate_entry_id', 'DESC');
            }
            $query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date_show', function ($query) {
                $order_date_show = '';
                if(!empty($query->order_date)) {
                    $order_date_show = date('M d Y', strtotime($query->order_date));
                }
                return $order_date_show;
            })
            ->addColumn('vehicle_in_out_date_show', function ($query) {
                $vehicle_in_out_date_show = '';
                if(!empty($query->vehicle_in_out_date)) {
                    $vehicle_in_out_date_show = date('M d Y', strtotime($query->vehicle_in_out_date));
                }
                return $vehicle_in_out_date_show;
            })
            ->addColumn('courier_date_show', function ($query) {
                $courier_date_show = '';
                if(!empty($query->courier_date)) {
                    $courier_date_show = date('M d Y', strtotime($query->courier_date));
                }
                return $courier_date_show;
            })
            ->addColumn('action', function ($query) {
                $ConsignmentReceipt = ConsignmentReceipt::where([['order_id', '=', $query->order_number]])->get()->toArray();
                if(sizeof($ConsignmentReceipt) > 0) {
                    $action = '<a href="javascript:void(0)" class="edit-gate-entry" data-id="'.$query->gate_entry_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a>';
                }else {
                    $action = '<a href="javascript:void(0)" class="edit-gate-entry" data-id="'.$query->gate_entry_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-gate-entry" data-id="'.$query->gate_entry_id.'"><button type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button></a>';
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
    // export
    public function gate_entry_export(){
        $query = GateEntry::OrderBy('gate_entry_id', 'ASC')->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Transaction');
        $sheet->setCellValue('B1', 'Order_id');
        $sheet->setCellValue('C1', 'Order_date');
        $sheet->setCellValue('D1', 'Vehicle_no');
        $sheet->setCellValue('E1', 'Driver_name');
        $sheet->setCellValue('F1', 'Contact_name');
        $sheet->setCellValue('G1', 'Vehicle In/Out date');
        $sheet->setCellValue('H1', 'Courier_date');
        $sheet->setCellValue('I1', 'Courier_number');
        $sheet->setCellValue('J1', 'No Of Box');
        $rows = 2;
        foreach($query as $empDetails){
            $order_date_show = '';
            if(!empty($empDetails['order_date'])) {
                $order_date_show = date('M d Y', strtotime($empDetails['order_date']));
            }
            $vehicle_in_out_date_show = '';
            if(!empty($empDetails['vehicle_in_out_date'])) {
                $vehicle_in_out_date_show = date('M d Y', strtotime($empDetails['vehicle_in_out_date']));
            }
            $courier_date_show = '';
            if(!empty($empDetails['courier_date'])) {
                $courier_date_show = date('M d Y', strtotime($empDetails['courier_date']));
            }
            $status = ($empDetails['status'] == 1)?'Active':'Inactive';
            $sheet->setCellValue('A' . $rows, $empDetails['transaction_type']);
            $sheet->setCellValue('B' . $rows, $empDetails['order_number']);
            $sheet->setCellValue('C' . $rows, $order_date_show);
            $sheet->setCellValue('D' . $rows, $empDetails['vehicle_no']);
            $sheet->setCellValue('E' . $rows, $empDetails['driver_name']);
            $sheet->setCellValue('F' . $rows, $empDetails['contact_no']);
            $sheet->setCellValue('G' . $rows, $vehicle_in_out_date_show);
            $sheet->setCellValue('H' . $rows, $courier_date_show);
            $sheet->setCellValue('I' . $rows, $empDetails['courier_number']);
            $sheet->setCellValue('J' . $rows, $empDetails['no_of_box']);
            $rows++;
        }
        $fileName = "gate_entry_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Edit
    public function edit_gate_entry(Request $request) {
        if ($request->ajax()) {
            $html = view('backend/receiving_and_putaway/gate_entry_form')->with([
                'gate_entry_data' => GateEntry::where([['gate_entry_id', '=', $request->id]])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Delete
    public function delete_gate_entry(Request $request) {
        if ($request->ajax()) {
            $GateEntry = GateEntry::where([['gate_entry_id', '=', $request->id]])->update(['status' => '2']);
            if($GateEntry) {
                return response()->json(["status" => 1, "msg" => "Delete Successful."]);
            }else {
                return response()->json(["status" => 1, "msg" => "Delete Faild."]);
            }
        }
    }
    
}