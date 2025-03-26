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
use App\OrderReceived;
use App\Products;
use App\PartName;
use App\PurchaseOrderReturn;
use App\PurchaseOrderReturnDetails;
use App\CheckInDetails;
use App\PurchaseOrderReturnFiles;
use App\BinningLocation;
use App\DefectiveBinDetails;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class PurchaseOrderReturnController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/purchase_order_return")->with(array());
    }
    public function list_purchase_order_return(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('purchase_order_return as pr');
            $query->select('pr.purchase_order_return_id', 'pr.order_id', 'pr.note', 's.full_name');
            $query->join('suppliers as s', 's.supplier_id', '=', 'pr.supplier_id', 'left');
            $query->where([['pr.status', '!=', '2']]);
            
            if($keyword)
            {
                $sql = "order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('order_id', 'asc');
                else
                    $query->orderBy('order_id', 'desc');
            }
            else
            {
                $query->orderBy('order_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('items', function ($query) {
                $selectQty = PurchaseOrderReturnDetails::where('order_id',$query->order_id)->sum('return_quantity');
                return $selectQty;
            })
            ->addColumn('invoice_no', function ($query) {
                $invoice_no = "";
                return $invoice_no;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="view-purchase-order-return" data-id="'.$query->order_id.'"><button type="button" class="btn btn-success btn-sm" title="View Details"><i class="fa fa-eye"></i></button></a>';
                return $action;
            })
            ->rawColumns(['details', 'file_details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }

    // Export
    public function purchase_order_return_export()
    {
        
        $query = DB::table('purchase_order_return')
        ->select('purchase_order_return_id', 'order_id', 'note')
        ->where([['status', '!=', '2']])
        ->orderBy('purchase_order_return_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Purchase_order_return_id');
        $sheet->setCellValue('B1', 'Order_id');
        $sheet->setCellValue('C1', 'Items');
        $sheet->setCellValue('D1', 'Return_quantity');
        $sheet->setCellValue('E1', 'Note');
        
        $rows = 2;
        foreach($data as $empDetails){
            $items = '';
            if (!empty($empDetails->order_id)) {
                $items = PurchaseOrderReturnDetails::where('order_id',$empDetails->order_id)->sum('quantity');
            }

            $return_quantity = '';
            if (!empty($empDetails->order_id)) {
                $return_quantity = PurchaseOrderReturnDetails::where('order_id',$empDetails->order_id)->sum('return_quantity');
            }
            $sheet->setCellValue('A' . $rows, $empDetails->purchase_order_return_id);
            $sheet->setCellValue('B' . $rows, $empDetails->order_id);
            $sheet->setCellValue('C' . $rows, $items);
            $sheet->setCellValue('D' . $rows, $return_quantity);
            $sheet->setCellValue('E' . $rows, $empDetails->note);
            $rows++;
        }
        $fileName = "Purchase_order.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Add
    public function add_purchase_order_return(Request $request){
        $query = DB::table('check_in as c');
        $query->select('c.order_id');
        $query->join('purchase_order_return as p', 'p.order_id', '=', 'c.order_id', 'left');
        $query->where([['c.status', '=', '1']]);
        $query->whereNull('p.order_id');
        $listOrderIds = $query->get()->toArray();
        return \View::make("backend/receiving_and_putaway/purchase_order_return_form")->with([
            'listOrderIds' => $listOrderIds,
            'supplierData' => Suppliers::select('supplier_id', 'full_name')->orderBy('full_name', 'ASC')->get()->toArray()
        ])->render();
    }
    public function get_order_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            
            //echo $request->supplier_id; exit();
            $selectData = DB::table('defective_bin_details')->select('order_id', 'supplier_id', 'status')->where([['supplier_id', '=', $request->supplier_id]])->groupBy('order_id','supplier_id', 'status')->get()->toArray();
            if(sizeof($selectData) >0) {
                
                foreach($selectData as $val) {
                    
                    $status = 'Direct Return';
                    
                    if($val->status == "1") $status = "Customer Return";
                    
                    $checkOrderExist = DB::table('purchase_order_return')->where([['order_id', '=', $val->order_id], ['supplier_id', '=', $val->supplier_id]])->get()->toArray();
                    
                    if(sizeof($checkOrderExist) < 1) {
                        array_push($returnData, ['purchase_order_id' => $val->order_id, 'supplier_id' => $val->supplier_id, 'category' => $status]);
                    }
                }
                
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
            // $barcode_number = "";
            
            // $selectBarcode = Orders::select('barcode_number')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            
            // if(sizeof($selectBarcode) >0) {
                
            //     if(!empty($selectBarcode[0]['barcode_number'])) $barcode_number = $selectBarcode[0]['barcode_number'];
            // }
            // $CheckInDetails = CheckInDetails::select('product_id', 'quantity', 'good_quantity', 'bad_quantity')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            // if(sizeof($CheckInDetails) > 0) {
            //     foreach($CheckInDetails as $data) {
            //         $part_name = "";
            //         $pmpno = "";
            //         $price = "";
                    
            //         $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
            //         if(sizeof($Products) > 0) {
            //             if(!empty($Products[0]['part_name_id'])) {
            //                 $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
            //                 if(sizeof($PartName) > 0) {
            //                     if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
            //                 }
            //             }
            //             if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
            //             if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
            //         }
                    
            //         $invoice_qty = 0;
            //         $getInvoiceQty = OrderDetail::select('qty')->where([['order_id', '=', $request->order_id], ['product_id', '=', $data['product_id']]])->get()->toArray();
                    
            //         if(sizeof($getInvoiceQty) > 0) {
            //             $invoice_qty = $getInvoiceQty[0]['qty'];
            //         }
                    
            //         array_push($returnData, array('barcode_number' => $barcode_number, 'invoice_qty' => $invoice_qty, 'product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'good_quantity' => $data['good_quantity'], 'bad_quantity' => $data['bad_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            //     }
            //     return response()->json(["status" => 1, "data" => $returnData]);
            // }else {
            //     return response()->json(["status" => 0, "msg" => "No record found."]);
            // }
        }
    }
    // Save
    public function save_purchase_order_return(Request $request) {
        if ($request->ajax()) {
            
            $flag = 0;
            
            if(sizeof($request->purchase_order_ids) > 0) {
                
                for($i=0; $i<sizeof($request->purchase_order_ids); $i++) {
                    
                    $data = new PurchaseOrderReturn;
                    $data->order_id = $request->purchase_order_ids[$i];
                    $data->supplier_id = $request->supplier_id;
                    $data->status = "1";
                    $data->save();
                        
                    $OrderReturnDetails = DefectiveBinDetails::select('product_id', 'quantity', 'bad_quantity')->where([['order_id', '=', $request->purchase_order_ids[$i]]])->get()->toArray();
                    
                    if(sizeof($OrderReturnDetails) > 0) {
                        
                        foreach($OrderReturnDetails as $rdata) {
                            
                            $part_name = "";
                            $pmpno = "";
                            $price = "";
                            
                            $Products = Products::select('part_name_id', 'pmpno', 'pmrprc')->where([['product_id', '=', $rdata['product_id']]])->get()->toArray();
                            
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
                            
                            $data2 = new PurchaseOrderReturnDetails;
                            $data2->order_id = $request->purchase_order_ids[$i];
                            $data2->product_id = $rdata['product_id'];
                            $data2->quantity = $rdata['quantity'];
                            $data2->return_quantity =$rdata['bad_quantity'];
                            $data2->status = "1";
                            $data2->save();
                            
                            //array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'bad_quantity' => $data['bad_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno));
                        }
                    }
                
                    $flag++;
                }
                
                if($flag == sizeof($request->purchase_order_ids)) {
                    return response()->json(["status" => 1, "msg" => "Save Succesful."]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Save Faild!"]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Save Faild! Something is wrong!"]);
            }
        }
    }
    // Delete
    public function delete_purchase_order_return(Request $request) {
        if ($request->ajax()) {
            $ConsignmentReceipt = PurchaseOrderReturn::where([['order_id', '=', $request->id]])->delete();
            if($ConsignmentReceipt) {
                $ConsignmentReceiptDetails = PurchaseOrderReturnDetails::where([['order_id', '=', $request->id]])->delete();
                if($ConsignmentReceiptDetails) {
                    $PurchaseOrderReturnFiles = PurchaseOrderReturnFiles::select('file_name')->where([['order_id', '=', $request->id]])->get()->toArray();
                    if(sizeof($PurchaseOrderReturnFiles) > 0) {
                        $flag = 0;
                        foreach($PurchaseOrderReturnFiles as $data) {
                            $filepath = public_path().'/backend/images/purchase_order_return/'.$data['file_name'];
                            if(file_exists($filepath)) {
                                unlink($filepath);
                            }
                            $flag++;
                        }
                        if($flag == sizeof($PurchaseOrderReturnFiles)) {
                            PurchaseOrderReturnFiles::where([['order_id', '=', $request->id]])->delete();
                            return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                        }else {
                            return response()->json(["status" => 0, "msg" => "Something is wrong."]);
                        }
                    }else {
                        return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Delete Faild. Something is wrong."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild."]);
            }
        }
    }
    // View
    public function viewReturnDetails(Request $request){
        
        $returnData = [];
        $OrderReturnDetails = DefectiveBinDetails::select('product_id', 'quantity', 'bad_quantity')->where([['order_id', '=', $request->order_id]])->get()->toArray();
        
        if(sizeof($OrderReturnDetails) > 0) {
            
            foreach($OrderReturnDetails as $data) {
                
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
                
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'bad_quantity' => $data['bad_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno));
            }
        }
        $html = \View::make("backend/receiving_and_putaway/view_purchase_order_return_before")->with([
            'order_return_data' => $returnData,
            'order_id' => $request->order_id,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    
    public function view_purchase_order_return(Request $request){
        $returnData = [];
        $PurchaseOrderReturnDetails = PurchaseOrderReturnDetails::select('product_id', 'quantity', 'good_quantity', 'return_quantity')->where([['order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($PurchaseOrderReturnDetails) > 0) {
            foreach($PurchaseOrderReturnDetails as $data) {
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
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'good_quantity' => $data['good_quantity'], 'return_quantity' => $data['return_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            }
        }
        $html = \View::make("backend/receiving_and_putaway/view_purchase_order_return")->with([
            'order_return_data' => $returnData,
            'order_id' => $request->id,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    // View Files
    public function view_files_purchase_order_return(Request $request){
        $html = \View::make("backend/receiving_and_putaway/view_files_purchase_order_return")->with([
            'PurchaseOrderReturnFiles' => PurchaseOrderReturnFiles::select('purchase_order_return_files_id', 'file_name')->where([['order_id', '=', $request->id], ['status', '=', '1']])->get()->toArray(),
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    // Delete Files
    public function delete_file(Request $request) {
        if ($request->ajax()) {
            $filepath = public_path().'/backend/images/purchase_order_return/'.$request->file_name;
            if(file_exists($filepath)) {
                unlink($filepath);
            }
            $PurchaseOrderReturnFiles = PurchaseOrderReturnFiles::where([['purchase_order_return_files_id', '=', $request->id]])->delete();
            if($PurchaseOrderReturnFiles) {
                return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild."]);
            }
        }
    }
    // Save Files
    public function save_files(Request $request) {
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->return_files) > 0) {
                for($i=0; $i<sizeof($request->return_files); $i++) {
                    $upimages = $request->return_files[$i];
                    $file_name = rand() . '.' . $upimages->getClientOriginalExtension();
                    $upimages->move(public_path('backend/images/purchase_order_return/'), $file_name);
                    $fileData = new PurchaseOrderReturnFiles;
                    $fileData->order_id = $request->order_id;
                    $fileData->file_name = $file_name;
                    $fileData->status = "1";
                    $fileData->save();
                    $flag++;
                }
                if($flag == sizeof($request->return_files)) {
                    return response()->json(["status" => 1, "msg" => "Upload Succesful."]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Upload Faild."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong!"]);
            }
        }
    }
}