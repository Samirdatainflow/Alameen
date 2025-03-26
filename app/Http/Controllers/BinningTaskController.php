<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Suppliers;
use App\Orders;
use App\OrderDetail;
use App\OrderApproved;
use App\BiningTask;
use App\CheckIn;
use App\CheckInDetails;
use App\BinningLocationDetails;
use App\Products;
use App\PartName;
use App\Location;
use App\ZoneMaster;
use App\Row;
use App\Rack;
use App\Plate;
use App\Place;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BinningTaskController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/binning_task")->with(array());
    }
    public function list_binning_task(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('bining_task as b');
            $query->join('users as u', 'u.user_id', '=', 'b.user_id', 'left');
            $query->select('b.binning_task_id', 'b.order_id','b.status', 'b.close_status', 'b.print_binning_task','b.created_at','u.first_name','u.last_name');
            //$query->where([['o.is_delete', '!=', '1']]);
            if($keyword) {
                $sql = "u.first_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order){
                if($order == "asc")
                    $query->orderBy('u.first_name', 'asc');
                else
                    $query->orderBy('u.first_name', 'desc');
            }else {
                $query->orderBy('binning_task_id', 'DESC');
            }
            $query->where([['b.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('user_name', function ($query) {
                $user_name = '';
                if(!empty($query->first_name)) {
                    $user_name .= $query->first_name;
                }
                if(!empty($query->last_name)) {
                    $user_name .= " ".$query->last_name;
                }
                return $user_name;
            })
            ->addColumn('binning_date', function ($query) {
                $binning_date = '';
                if(!empty($query->created_at)) {
                    $binning_date = date('d M Y', strtotime($query->created_at));
                }
                return $binning_date;
            })
            ->addColumn('items', function ($query) {
                $items = '';
                return CheckInDetails::where([['order_id', '=', $query->order_id]])->sum('good_quantity');
            })
            ->addColumn('status_data', function ($query) {
                $status_data = '';
                if($query->status == "0") {
                    $status_data = '<a href="javascript:void(0)" class="status-change" data-id="'.$query->order_id.'" data-status="1"><span class="badge badge-warning">Processing</span></a>';
                }
                if($query->status == "1") {
                    $status_data = '<a href="javascript:void(0)" class="status-change" data-id="'.$query->order_id.'" data-status="0"><span class="badge badge-success">Completed</span></a>';
                }
                return $status_data;
            })
            ->addColumn('close_status_data', function ($query) {
                $close_status_data = '';
                if($query->close_status == "0") {
                    $close_status_data = '<a href="javascript:void(0)" class="close-status-change" data-id="'.$query->order_id.'" data-status="1"><span class="badge badge-success">Open</span></a>';
                }
                if($query->close_status == "1") {
                    $close_status_data = '<a href="javascript:void(0)" class="close-status-change" data-id="'.$query->order_id.'" data-status="0"><span class="badge badge-danger">Close</span></a>';
                }
                return $close_status_data;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" data-id="'.$query->order_id.'" class="btn btn-danger btn-sm delete-binning-task" data-id="'.$query->order_id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                if($query->close_status == "1") {
                    $action = "";
                }
                $action = '<a href="javascript:void(0);" data-id="'.$query->order_id.'" data-print_binning_task="'.$query->print_binning_task.'" class="download-binning-invoice btn btn-warning action-btn" title="Print Binning Task"><i class="fa fa-download" aria-hidden="true"></i> Print Binning Task</a>';
                if($query->print_binning_task == 1) {
                    $action .= ' <a href="javascript:void(0);" data-id="'.$query->order_id.'" class="reset-print btn btn-success action-btn" title="Reset Print">Reset Print</a>';
                }
                return $action;
            })
            ->rawColumns(['close_status_data', 'status_data', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // export
    public function binning_task_export()
    {
        
        $query = DB::table('bining_task as b')
        ->select('b.binning_task_id', 'b.order_id','b.status', 'b.close_status', 'b.print_binning_task','b.created_at','u.first_name','u.last_name')
        ->join('users as u', 'u.user_id', '=', 'b.user_id', 'left')
        ->orderBy('b.binning_task_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Task_id');
        $sheet->setCellValue('B1', 'Order_id');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'Item');
        $sheet->setCellValue('E1', 'Assigned_employee');
        $rows = 2;
        foreach($data as $empDetails){
            // echo $empDetails->items; exit();
            $date = '';
            if(!empty($empDetails->created_at)) {
             $date = date('d M Y', strtotime($empDetails->created_at));
            }
            $items = '';
            if (!empty($empDetails->order_id)) {
                $items = CheckInDetails::where([['order_id', '=', $empDetails->order_id]])->sum('good_quantity');
            }
            $user_name = '';
            if(!empty($empDetails->first_name)) {
                $user_name .= $empDetails->first_name;
            }
            if(!empty($empDetails->last_name)) {
                $user_name .= " ".$empDetails->last_name;
            }
            $sheet->setCellValue('A' . $rows, $empDetails->binning_task_id);
            $sheet->setCellValue('B' . $rows, $empDetails->order_id);
            $sheet->setCellValue('C' . $rows, $date);
            $sheet->setCellValue('D' . $rows, $items);
            $sheet->setCellValue('E' . $rows, $user_name);
            $rows++;
        }
        $fileName = "consignment_receipt_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Add
    public function add_binning_task(Request $request) {
        if ($request->ajax()) {
            $query = DB::table('check_in as c');
            $query->select('c.order_id');
            $query->join('bining_task as b', 'b.order_id', '=', 'c.order_id', 'left');
            $query->where([['c.status', '=', '1']]);
            $query->whereNull('b.order_id');
            $listOrderIds = $query->get()->toArray();
            return \View::make("backend/receiving_and_putaway/binning_task_form")->with([
                'listOrderIds' => $listOrderIds,
                'Users' => Users::select('user_id', 'first_name', 'last_name')->where([['user_id', '!=', Session::get('user_id')], ['status', '=', 'Active']])->orderBy('user_id', 'desc')->get()->toArray(),
            ])->render();
        }
    }
    // Save
    public function save_binning_task(Request $request) {
        if ($request->ajax()) {
            $data = new BiningTask;
            $data->order_id = $request->order_id;
            $data->user_id = $request->user_id;
            $data->status = "0";
            $data->close_status = "0";
            $saveData = $data->save();
            if($saveData) {
                return response()->json(["status" => 1, "msg" => "Save Succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Save Faild!"]);
            }
        }
    }
    // Status Change
    public function status_change(Request $request) {
        if ($request->ajax()) {
            $BiningTask = BiningTask::where([['order_id', '=', $request->id]])->update(['status' => '1']);
            if($BiningTask) {
                return response()->json(["status" => 1, "msg" => "Status change succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Status change faild."]);
            }
        }
    }
    // Close Status Change
    public function close_status_change(Request $request) {
        if ($request->ajax()) {
            $BiningTask = BiningTask::where([['order_id', '=', $request->id]])->update(['close_status' => '1']);
            if($BiningTask) {
                return response()->json(["status" => 1, "msg" => "Status change succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Status change faild."]);
            }
        }
    }
    // Delete
    public function delete_binning_task(Request $request) {
        if ($request->ajax()) {
            $BiningTask = BiningTask::where([['order_id', '=', $request->id]])->delete();
            if($BiningTask) {
                return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild."]);
            }
        }
    }
    public function download_binning_invoice(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_request_order_to_html($id));
        return $pdf->stream();
    }
    function convert_request_order_to_html($id) {
        $returnData = [];
        BiningTask::where([['order_id', '=', $id]])->update(['print_binning_task'=>'1']);
        $BinningLocationDetails = BinningLocationDetails::select('product_id', 'quantity', 'location_id', 'zone_id', 'row_id', 'rack_id', 'plate_id', 'place_id')->where([['order_id', '=', $id]])->get()->toArray();
        if(sizeof($BinningLocationDetails) > 0) {
            foreach($BinningLocationDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                }
                $location_name = '';
                if(!empty($data['location_id'])) {
                    $Location = Location::select('location_name')->where([['location_id', '=', $data['location_id']]])->get()->toArray();
                    if(sizeof($Location) > 0) {
                        $location_name = $Location[0]['location_name'];
                    }
                }
                $zone_name = '';
                if(!empty($data['zone_id'])) {
                    $ZoneMaster = ZoneMaster::select('zone_name')->where([['zone_id', '=', $data['zone_id']]])->get()->toArray();
                    if(sizeof($ZoneMaster) > 0) {
                        $zone_name = $ZoneMaster[0]['zone_name'];
                    }
                }
                $row_name = '';
                if(!empty($data['row_id'])) {
                    $Row = Row::select('row_name')->where([['row_id', '=', $data['row_id']]])->get()->toArray();
                    if(sizeof($Row) > 0) {
                        $row_name = $Row[0]['row_name'];
                    }
                }
                $rack_name = '';
                if(!empty($data['rack_id'])) {
                    $Rack = Rack::select('rack_name')->where([['rack_id', '=', $data['rack_id']]])->get()->toArray();
                    if(sizeof($Rack) > 0) {
                        $rack_name = $Rack[0]['rack_name'];
                    }
                }
                $plate_name = '';
                if(!empty($data['plate_id'])) {
                    $Plate = Plate::select('plate_name')->where([['plate_id', '=', $data['plate_id']]])->get()->toArray();
                    if(sizeof($Plate) > 0) {
                        $plate_name = $Plate[0]['plate_name'];
                    }
                }
                $place_name = '';
                if(!empty($data['place_id'])) {
                    $Place = Place::select('place_name')->where([['place_id', '=', $data['place_id']]])->get()->toArray();
                    if(sizeof($Place) > 0) {
                        $place_name = $Place[0]['place_name'];
                    }
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'location_name' => $location_name, 'zone_name' => $zone_name, 'row_name' => $row_name, 'rack_name' => $rack_name, 'plate_name' => $plate_name, 'place_name' => $place_name, 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            }
        }
        // echo "<pre>";
        // print_r($returnData); exit();
        return view('backend.receiving_and_putaway.download_binning_invoice')->with([
            'BinningDetails' => $returnData,
            'id' => $id
        ]);
    }
    public function reset_print(Request $request){
        $up_query = BiningTask::where([['order_id', '=', $request->sale_order_id]])->update(['print_binning_task'=> '0']);
        if($up_query) {
            $returnData = ["status" => 1, "msg" => "Reset successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Reset faild! Semethning is wrong."];
        }
        return response()->json($returnData);
    }
}