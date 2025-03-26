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
use App\ConsignmentReceipt;
use App\ConsignmentReceiptDetails;
use App\BiningAdvice;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConsignmentReceiptController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/consignment_receipt")->with(array());
    }
    public function list_consignment_receipt(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('consignment_receipt');
            $query->select('consignment_receipt_id', 'order_id');
            $query->where([['status', '!=', '2']]);
            //$query->groupBy('order_id');
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
                $selectQty = ConsignmentReceiptDetails::where('order_id',$query->order_id)->sum('quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-consignment-receipt" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('action', function ($query) {
                $BiningAdvice = BiningAdvice::where([['order_id', '=', $query->order_id]])->get()->toArray();
                if(sizeof($BiningAdvice) > 0) {
                    $action = "";
                }else {
                    $action = '<a href="javascript:void(0)" class="delete-consignment-receipt" data-id="'.$query->order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                }
                return $action;
            })
            ->rawColumns(['details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Add
    public function add_consignment_receipt(Request $request){
        $query = DB::table('gate_entry as g');
        $query->select('g.order_number');
        $query->join('consignment_receipt as c', 'c.order_id', '=', 'g.order_number', 'left');
        $query->where([['g.status', '=', '1']]);
        $query->whereNull('c.order_id');
        $listInboundNo = $query->get()->toArray();
        return \View::make("backend/receiving_and_putaway/consignment_receipt_form")->with([
            'listInboundNo' => $listInboundNo
        ])->render();
    }
    public function get_order_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $OrderDetail = OrderDetail::select('product_id')->where([['order_id', '=', $request->inbound_order_no]])->get()->toArray();
            if(sizeof($OrderDetail) > 0) {
                foreach($OrderDetail as $data) {
                    $part_name = "";
                    $pmpno = "";
                    $Products = Products::select('part_name_id', 'pmpno')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    }
                    array_push($returnData, array('product_id' => $data['product_id'], 'part_name' => $part_name, 'pmpno' => $pmpno));
                }
                return response()->json(["status" => 1, "data" => $returnData]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Save
    public function save_consignment_receipt(Request $request) {
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->product_id) > 0) {
                if(!empty($request->hidden_id)) {
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        $ConsignmentReceiptDetails = ConsignmentReceiptDetails::where([['order_id', '=', $request->hidden_id], ['product_id', '=', $request->product_id[$i]]])->update(['quantity' => $request->quantity[$i]]);
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Update Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Update Faild!"]);
                    }
                }else {
                    $data = new ConsignmentReceipt;
                    $data->order_id = $request->inbound_order_no;
                    $data->status = "1";
                    $data->save();
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        $data = new ConsignmentReceiptDetails;
                        $data->order_id = $request->inbound_order_no;
                        $data->product_id = $request->product_id[$i];
                        $data->quantity = $request->quantity[$i];
                        $data->status = "1";
                        $data->save();
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Save Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Save Faild!"]);
                    }
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Save Faild! Something is wrong!"]);
            }
        }
    }
    // Delete
    public function delete_consignment_receipt(Request $request) {
        if ($request->ajax()) {
            $ConsignmentReceipt = ConsignmentReceipt::where([['order_id', '=', $request->id]])->delete();
            if($ConsignmentReceipt) {
                $ConsignmentReceiptDetails = ConsignmentReceiptDetails::where([['order_id', '=', $request->id]])->delete();
                if($ConsignmentReceiptDetails) {
                    return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                }else {
                    return response()->json(["status" => 0, "msg" => "Delete Faild. Something is wrong."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild."]);
            }
        }
    }
    // View
    public function view_consignment_receipt(Request $request){
        $returnData = [];
        $ConsignmentReceiptDetails = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($ConsignmentReceiptDetails) > 0) {
            foreach($ConsignmentReceiptDetails as $data) {
                $part_name = "";
                $pmpno = "";
                $Products = Products::select('part_name_id', 'pmpno')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno));
            }
        }
        $html = \View::make("backend/receiving_and_putaway/consignment_receipt_view")->with([
            'consignment_receipt_data' => $returnData,
            'inbound_order_no' => $request->id
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    // public function get_order_with_detals(Request $request) {
    // 	if ($request->ajax()) {
    // 		$query = DB::table('order_detail as o');
    //         $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
    //         $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
    //         $query->select('o.order_id', 'o.product_id', 'o.qty', 'o.warehouse_id', 'p.pmpno', 'pn.part_name');
    //         $query->where([['o.order_id', '=', $request->id]]);
    //         $orderDetail = $query->get()->toArray();
    // 		$html = view('backend.receiving_and_putaway.order_approved_form')->with([
    //             'order_data' => Orders::select('order_id', 'datetime', 'deliverydate', 'supplier_id')->where([['order_id', '=', $request->id]])->get()->toArray(),
    // 			'order_detail_data' => $orderDetail
    //         ])->render();
    //         return response()->json(["status" => 1, "message" => $html]);
    // 	}
    // }
    // Approved Order
    // public function approved_receiving_order(Request $request) {
    // 	if ($request->ajax()) {
    // 		$returnData = [];
    // 		if(sizeof($request->approved_product_id) > 0) {
    // 			$flag = 0;
    // 			for($i=0; $i < sizeof($request->approved_product_id); $i++) {
    // 				$data = new OrderApproved;
    // 				$data->order_id = $request->order_id;
    // 				$data->date_approve = date('Y-m-d H:i:s');
    // 				$data->warehouse_id = $request->approved_warehouse_id[$i];
    // 				$data->product_id = $request->approved_product_id[$i];
    // 				$data->qty = $request->qty[$i];
    // 				$data->qty_appr = $request->approved_qty[$i];
    // 				$data->supplier_id = $request->supplier_id;
    // 				$data->save();
    // 				$flag++;
    // 			}
    // 			if($flag == sizeof($request->approved_product_id)) {
    // 				$upOrders = Orders::where([['order_id', '=', $request->order_id]])->update(['approved' => '1']);
    // 				if($upOrders) {
    // 					$returnData = array('status' => 1, 'msg' => 'Approved Succesful.');
    // 				}else {
    // 					$returnData = array('status' => 1, 'msg' => 'Approved Faild. Something is wrong');
    // 				}
    // 			}else {
    // 				$returnData = array('status' => 0, 'msg' => 'Approved Faild.');
    // 			}
    // 		}else {
    // 			$returnData = array('status' => 1, 'msg' => 'Approved Faild. Something is wrong');
    // 		}
    // 		return response()->json($returnData);
    // 	}
    // }
    // Received Order
    // public function received_receiving_order(Request $request) {
    // 	if ($request->ajax()) {
    // 		$returnData = [];
    // 		if(sizeof($request->approved_product_id) > 0) {
    // 			$flag = 0;
    // 			for($i=0; $i < sizeof($request->approved_product_id); $i++) {
    //                 // $current_stock = 0;
    //                 // $selectProducts = Products::select('current_stock')->where([['product_id', '=', $request->approved_product_id[$i]]])->get()->toArray();
    //                 // if(sizeof($selectProducts) > 0) {
    //                 //     if(!empty($selectProducts[0]['current_stock'])) $current_stock = $selectProducts[0]['current_stock'];
    //                 // }
    //                 // if($request->approved_qty[$i] > 0) {
    //                 //  $current_stock = $current_stock + $request->approved_qty[$i];
    //                 // }
    //                 //Products::where([['product_id', '=', $request->approved_product_id[$i]]])->update(['current_stock' => $current_stock]);
    // 				$data = new OrderReceived;
    // 				$data->order_id = $request->order_id;
    // 				$data->date_reception = date('Y-m-d H:i:s');
    // 				$data->warehouse_id = $request->approved_warehouse_id[$i];
    // 				$data->product_id = $request->approved_product_id[$i];
    // 				$data->qty = $request->qty[$i];
    // 				$data->qry_appr = $request->approved_qty[$i];
    // 				$data->save();
    // 				$flag++;
    // 			}
    // 			if($flag == sizeof($request->approved_product_id)) {
    // 				$upOrders = Orders::where([['order_id', '=', $request->order_id]])->update(['received' => '1']);
    // 				if($upOrders) {
    // 					$returnData = array('status' => 1, 'msg' => 'Received Succesful.');
    // 				}else {
    // 					$returnData = array('status' => 1, 'msg' => 'Received Faild. Something is wrong');
    // 				}
    // 			}else {
    // 				$returnData = array('status' => 0, 'msg' => 'Received Faild.');
    // 			}
    // 		}else {
    // 			$returnData = array('status' => 1, 'msg' => 'Received Faild. Something is wrong');
    // 		}
    // 		return response()->json($returnData);
    // 	}
    // }
    // View Order Details
    // public function view_inspection_of_the_consignment(Request $request) {
    //     if ($request->ajax()) {
    //         $returnData = [];
    //         $OrderDetail = OrderDetail::select('order_detail_id', 'order_id', 'qty', 'mrp', 'product_id')->where([['order_id', '=', $request->id]])->get()->toArray();
    //         if(sizeof($OrderDetail) > 0) {
    //             foreach($OrderDetail as $data) {
    //                 $part_name = "";
    //                 $Products = Products::select('part_name_id', 'pmpno')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
    //                 if(sizeof($Products) > 0) {
    //                     $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
    //                     if(!empty($PartName)) {
    //                         if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
    //                     }
    //                     if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
    //                 }
    //                 $qty_appr = '';
    //                 $SaleOrder = Orders::select('approved')->where([['order_id', '=', $request->id]])->get()->toArray();
    //                 if(sizeof($SaleOrder)>0) {
    //                     $OrderApproved = OrderApproved::select('qty_appr')->where([['order_id', '=', $request->id]])->get()->toArray();
    //                     if(sizeof($OrderApproved) > 0) {
    //                         $qty_appr = $OrderApproved[0]['qty_appr'];
    //                     }
    //                 }
    //                 array_push($returnData, array('order_detail_id' => $data['order_detail_id'], 'order_id' => $data['order_id'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'qty' => $data['qty'], 'mrp' => $data['mrp'], 'qty_appr' => $qty_appr));
    //             }
    //         }
    //         $is_approved=0;
    //         $Orders = Orders::select('approved')->where([['order_id', '=', $request->id]])->get()->toArray();
    //         if(sizeof($Orders)>0) {
    //             if(!empty($Orders[0]['approved'])) $is_approved = $Orders[0]['approved'];
    //         }
    //         $html = view('backend.receiving_and_putaway.view_inspection_of_the_consignment')->with([
    //             'order_data' => $returnData,
    //             'is_approved' => $is_approved
    //         ])->render();
    //         return response()->json(["status" => 1, "message" => $html]);
    //     }
    // }
    // public function consignment_add_location(Request $request) {
    //     if ($request->ajax()) {
    //         $returnData = [];
    //         $html = view('backend.receiving_and_putaway.consignment_add_location_form')->with([
    //             'order_data' => $returnData
    //         ])->render();
    //         return response()->json(["status" => 1, "message" => $html]);
    //     }
    // }
    public function consignment_receipt_export(){
        $query = DB::table('consignment_receipt')
        ->select('consignment_receipt_id', 'order_id')
        ->where([['status', '!=', '2']])
        ->orderBy('consignment_receipt_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Consignment_receipt_id');
        $sheet->setCellValue('B1', 'Order_id');
        $sheet->setCellValue('C1', 'Items');
        $rows = 2;
        foreach($data as $empDetails){
            // echo $empDetails->items; exit();
            $items = '';
            if(!empty($empDetails->order_id)) {
              $items = ConsignmentReceiptDetails::where('order_id',$empDetails->order_id)->sum('quantity');  
            }

            $sheet->setCellValue('A' . $rows, $empDetails->consignment_receipt_id);
            $sheet->setCellValue('B' . $rows, $empDetails->order_id);
            $sheet->setCellValue('C' . $rows, $items);
            $rows++;
        }
        $fileName = "consignment_receipt_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}