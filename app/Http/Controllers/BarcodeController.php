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

class BarcodeController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/barcode_list")->with(array());
    }
    public function list_barcode_table_data(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('barcode_scann_details as b');
            $query->join('suppliers as s', 's.supplier_id', '=', 'b.supplier_name', 'left');
            $query->select('b.*', 's.full_name');
            //$query->where([['o.is_delete', '!=', '1']]);
            if($keyword) {
                $sql = "b.barcode_number like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order){
                if($order == "asc")
                    $query->orderBy('b.barcode_number', 'asc');
                else
                    $query->orderBy('b.barcode_number', 'desc');
            }else {
                $query->orderBy('b.barcode_scann_details_id', 'DESC');
            }
            //$query->where([['b.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            
            // ->addColumn('close_status_data', function ($query) {
            //     $close_status_data = '';
            //     if($query->close_status == "0") {
            //         $close_status_data = '<a href="javascript:void(0)" class="close-status-change" data-id="'.$query->order_id.'" data-status="1"><span class="badge badge-success">Open</span></a>';
            //     }
            //     if($query->close_status == "1") {
            //         $close_status_data = '<a href="javascript:void(0)" class="close-status-change" data-id="'.$query->order_id.'" data-status="0"><span class="badge badge-danger">Close</span></a>';
            //     }
            //     return $close_status_data;
            // })
            // ->addColumn('action', function ($query) {
            //     $action = '<a href="javascript:void(0)" data-id="'.$query->order_id.'" class="btn btn-danger btn-sm delete-binning-task" data-id="'.$query->order_id.'" title="Delete"><i class="fa fa-trash"></i></a>';
            //     if($query->close_status == "1") {
            //         $action = "";
            //     }
            //     $action = '<a href="javascript:void(0);" data-id="'.$query->order_id.'" data-print_binning_task="'.$query->print_binning_task.'" class="download-binning-invoice btn btn-warning action-btn" title="Print Binning Task"><i class="fa fa-download" aria-hidden="true"></i> Print Binning Task</a>';
            //     if($query->print_binning_task == 1) {
            //         $action .= ' <a href="javascript:void(0);" data-id="'.$query->order_id.'" class="reset-print btn btn-success action-btn" title="Reset Print">Reset Print</a>';
            //     }
            //     return $action;
            // })
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // export
    public function barcode_list_export()
    {
        
        $query = DB::table('barcode_scann_details as b')
        ->select('b.*', 's.full_name')
        ->join('suppliers as s', 's.supplier_id', '=', 'b.supplier_name', 'left')
        ->orderBy('b.barcode_scann_details_id', 'DESC');
        $selectData = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'Part No');
        $sheet->setCellValue('C1', 'Part Name');
        $sheet->setCellValue('D1', 'Supplier Name');
        $sheet->setCellValue('E1', 'Barcode Number');
        $sheet->setCellValue('F1', 'Invoice No');
        $sheet->setCellValue('G1', 'Customer');
        $sheet->setCellValue('H1', 'Date Of Invoice');
        $rows = 2;
        
        foreach($selectData as $val){
            
            $sheet->setCellValue('A' . $rows, $val->order_id);
            $sheet->setCellValue('B' . $rows, $val->part_no);
            $sheet->setCellValue('C' . $rows, $val->part_name);
            $sheet->setCellValue('D' . $rows, $val->supplier_name);
            $sheet->setCellValue('E' . $rows, $val->barcode_number);
            $sheet->setCellValue('F' . $rows, $val->invoice_no);
            $sheet->setCellValue('G' . $rows, $val->customer);
            $sheet->setCellValue('H' . $rows, $val->date_of_invoice);
            $rows++;
        }
        $fileName = "barcode_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
}