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
use App\PartBrand;
use App\WmsUnit;
use App\BiningAdvice;
use App\ManufacturingNo;
use App\ConsignmentReceipt;
use App\Warehouses;
use DB;
use DataTables;
use PDF;
use Storage;
use Illuminate\Http\Response;

class ReceivingAndPutawayController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/bining_advice")->with(array());
    }
    // Add
    public function add_bining_advice(Request $request){
        if ($request->ajax()) {
            
            $query = DB::table('consignment_receipt as c');
            $query->select('c.order_id');
            $query->join('bining_advice as b', 'c.order_id', '=', 'b.order_id', 'left');
            $query->where([['c.status', '=', '1']]);
            $query->whereNull('b.order_id');
            $listOrderNos = $query->get()->toArray();
            
            $html = view('backend.receiving_and_putaway.bining_advice_form')->with([
                'listOrderNos' => $listOrderNos
                ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Get Purchase Order Details
    public function get_purchase_order_detals(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $datetime = "";
            $consignment_receipt = ConsignmentReceipt::where('order_id',$request->order_id)->where('status',1)->get()->toArray();
            if(sizeof($consignment_receipt)>0)
            {
                $Orders = Orders::select('datetime')->where([['order_request_unique_id', '=', $request->order_id]])->get()->toArray();
                if(sizeof($Orders) > 0) {
                    if(!empty($Orders[0]['datetime'])) $datetime = date('d/m/Y', strtotime($Orders[0]['datetime']));
                }
                $OrderDetail = OrderDetail::where([['order_id', '=', $request->order_id]])->get()->toArray();
                if(sizeof($OrderDetail) > 0) {
                    foreach($OrderDetail as $data) {
                        $part_name = "";
                        $Products = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                        if(sizeof($Products) > 0) {
                            if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                        $qty_appr = "";
                        $approved = "";
                        $BiningAdvice = BiningAdvice::where([['order_detail_id', '=', $data['order_detail_id']], ['order_id', '=', $data['order_id']], ['product_id', '=', $data['product_id']]])->get()->toArray();
                        if(sizeof($BiningAdvice) > 0) {
                            $approved = 1;
                            $qty_appr = $BiningAdvice[0]['qty_appr'];
                        }
                        $supplier_id = "";
                        $Orders = Orders::select('supplier_id')->where([['order_id', '=', $request->order_id]])->get()->toArray();
                        if(sizeof($Orders) > 0) {
                            $supplier_id = $Orders[0]['supplier_id'];
                        }
                        array_push($returnData, array('order_detail_id' => $data['order_detail_id'], 'order_id' => $data['order_id'], 'product_id' => $data['product_id'], 'qty_appr' => $qty_appr, 'mrp' => $data['mrp'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'approved' => $approved, 'supplier_id' => $supplier_id));
                    }
                    return response()->json(["status" => 1, "data" => $returnData, 'purchased_date' => $datetime]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found. You have enter wrong ID!"]);
                }
            }
            else
            {
                return response()->json(["status" => 0, "msg" => "Order is not found"]);
            }
            
        }
    }
    // Save Bining Advice
    public function save_bining_advice(Request $request) {
        if ($request->ajax()) {
            $data = new BiningAdvice;
            $data->order_id = $request->order_id;
            $data->order_detail_id = $request->order_detail_id;
            $data->date_approve = date('Y-m-d H:i:s');
            $data->product_id = $request->product_id;
            $data->qty_appr = $request->qty;
            $data->supplier_id = $request->supplier_id;
            $saveData = $data->save();
            if($saveData) {
                return response()->json(["status" => 1, "msg" => "Save Successful."]);
            }else {
                return response()->json(["status" => 1, "msg" => "Save Faild!"]);
            }
        }
    }
    public function list_receiving_order(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('bining_advice');
            //$query->join('orders as o', 'o.order_id', '=', 'b.order_id');
            //$query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->select('order_id');
            $query->groupBy('order_id');
            if($keyword) {
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
            // ->addColumn('supplier', function ($query) {
            //     $supplier = '';
            //     $Orders = Orders::select('supplier_id')->where([['order_id', '=', $query->order_id]])->get()->toArray();
            //     if(sizeof($Orders) > 0) {
            //         $selectSuppliers = Suppliers::select('full_name')->where('supplier_id',$Orders[0]['supplier_id'])->get();
            //         if(sizeof($selectSuppliers)>0) {
            //             if(!empty($selectSuppliers[0]['full_name'])) $supplier = $selectSuppliers[0]['full_name'];
            //         }
            //     }
            //     return $supplier;
            // })
            ->addColumn('invoice_no', function ($query) {
                $invoice_no = '';
                $Orders = Orders::select('invoice_no')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                if(sizeof($Orders) > 0) {
                    if(!empty($Orders[0]['invoice_no'])) $invoice_no = $Orders[0]['invoice_no'];
                }
                return $invoice_no;
            })
            // ->addColumn('item', function ($query) {
            //     $selectQty = BiningAdvice::where('order_id',$query->order_id)->sum('qty_appr');
            //     return $selectQty;
            // })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-order-details" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('status', function ($query) {
                $status = '';
                $BiningAdvice = BiningAdvice::where('order_id',$query->order_id)->get()->toArray();
                $OrderDetail = OrderDetail::where('order_id',$query->order_id)->get()->toArray();
                if(sizeof($BiningAdvice) > 0 && sizeof($OrderDetail) > 0) {
                    if(sizeof($BiningAdvice) == sizeof($OrderDetail)) {
                        $status = '<span class="badge badge-success">Completed</span>';
                    }else {
                        $status = '<span class="badge badge-warning">Partialy Received</span>';
                    }
                }
                return $status;
            })
            ->addColumn('download_binning_advice', function ($query) {
                //$download_binning_advice = "";
                // if($query->approved == '1') {
                $download_binning_advice = '<a href="javascript:void(0)" type="button" class="btn btn-warning btn-sm download-pdf" title="Download PDF" data-id="'.$query->order_id.'"><i class="fa fa-download"></i></a>';
                // }
                // $downloadBarcode = "";
                // $getBarcodeNumber = Orders::select('barcode_number')->where('order_id',$query->order_id)->get()->toArray();
                // if(sizeof($getBarcodeNumber) > 0) {
                //     if(!empty($getBarcodeNumber[0]['barcode_number'])) {
                //         $downloadBarcode = '<a href="javascript:void(0)" type="button" class="btn btn-warning btn-sm download-barcode-modal" title="Download Barcode" data-order_id="'.$query->order_id.'" data-barcode_number="'.$getBarcodeNumber[0]['barcode_number'].'"><i class="fa fa-barcode"></i></a>';
                //     }
                // }
                // $download_binning_advice = $download_binning_advice." ".$downloadBarcode;
                return $download_binning_advice;
            })
            // ->addColumn('received_status', function ($query) {
            //     $received_status = '';
            //     if($query->received == '1') {
            //         $received_status = '<span class="badge badge-success">Received</span>';
            //     }else if($query->approved == '1'){
            //         $received_status = '<a href="javascript:void(0)" class="order-received" data-id="'.$query->order_id.'"><span class="badge badge-warning">Not Received</span></a>';
            //     }else {
            //     	$received_status = '<span class="badge badge-danger">Not Received</span>';
            //     }
            //     return $received_status;
            // })
            ->rawColumns(['details', 'status', 'confirmed_status', 'download_binning_advice'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function get_order_with_detals(Request $request) {
    	if ($request->ajax()) {
    		$query = DB::table('order_detail as o');
            $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('o.order_id', 'o.product_id', 'o.qty', 'o.warehouse_id', 'p.pmpno', 'pn.part_name');
            $query->where([['o.order_id', '=', $request->id]]);
            $orderDetail = $query->get()->toArray();
    		$html = view('backend.receiving_and_putaway.order_approved_form')->with([
                'order_data' => Orders::select('order_id', 'datetime', 'deliverydate', 'supplier_id')->where([['order_id', '=', $request->id]])->get()->toArray(),
    			'order_detail_data' => $orderDetail
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
    	}
    }
    public function delete_bining_advice(Request $request) {
        if ($request->ajax()) {
            $BiningAdvice = BiningAdvice::where([['order_id', '=', $request->order_id], ['order_detail_id', '=', $request->order_detail_id]])->delete();
            if($BiningAdvice) {
                return response()->json(["status" => 1, "msg" => "Delete Successful."]);
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild."]);
            }
        }
    }
    // Approved Order
    public function approved_receiving_order(Request $request) {
    	if ($request->ajax()) {
    		$returnData = [];
    		if(sizeof($request->approved_product_id) > 0) {
    			$flag = 0;
    			for($i=0; $i < sizeof($request->approved_product_id); $i++) {
    				$data = new OrderApproved;
    				$data->order_id = $request->order_id;
    				$data->date_approve = date('Y-m-d H:i:s');
    				$data->warehouse_id = $request->approved_warehouse_id[$i];
    				$data->product_id = $request->approved_product_id[$i];
    				$data->qty = $request->qty[$i];
    				$data->qty_appr = $request->approved_qty[$i];
    				$data->supplier_id = $request->supplier_id;
    				$data->save();
    				$flag++;
    			}
    			if($flag == sizeof($request->approved_product_id)) {
    				$upOrders = Orders::where([['order_id', '=', $request->order_id]])->update(['approved' => '1']);
    				if($upOrders) {
    					$returnData = array('status' => 1, 'msg' => 'Approved Succesful.');
    				}else {
    					$returnData = array('status' => 1, 'msg' => 'Approved Faild. Something is wrong');
    				}
    			}else {
    				$returnData = array('status' => 0, 'msg' => 'Approved Faild.');
    			}
    		}else {
    			$returnData = array('status' => 1, 'msg' => 'Approved Faild. Something is wrong');
    		}
    		return response()->json($returnData);
    	}
    }
    // Received Order
    public function received_receiving_order(Request $request) {
    	if ($request->ajax()) {
    		$returnData = [];
    		if(sizeof($request->approved_product_id) > 0) {
    			$flag = 0;
    			for($i=0; $i < sizeof($request->approved_product_id); $i++) {
                    // $current_stock = 0;
                    // $selectProducts = Products::select('current_stock')->where([['product_id', '=', $request->approved_product_id[$i]]])->get()->toArray();
                    // if(sizeof($selectProducts) > 0) {
                    //     if(!empty($selectProducts[0]['current_stock'])) $current_stock = $selectProducts[0]['current_stock'];
                    // }
                    // if($request->approved_qty[$i] > 0) {
                    //  $current_stock = $current_stock + $request->approved_qty[$i];
                    // }
                    //Products::where([['product_id', '=', $request->approved_product_id[$i]]])->update(['current_stock' => $current_stock]);
    				$data = new OrderReceived;
    				$data->order_id = $request->order_id;
    				$data->date_reception = date('Y-m-d H:i:s');
    				$data->warehouse_id = $request->approved_warehouse_id[$i];
    				$data->product_id = $request->approved_product_id[$i];
    				$data->qty = $request->qty[$i];
    				$data->qry_appr = $request->approved_qty[$i];
    				$data->save();
    				$flag++;
    			}
    			if($flag == sizeof($request->approved_product_id)) {
    				$upOrders = Orders::where([['order_id', '=', $request->order_id]])->update(['received' => '1']);
    				if($upOrders) {
    					$returnData = array('status' => 1, 'msg' => 'Received Succesful.');
    				}else {
    					$returnData = array('status' => 1, 'msg' => 'Received Faild. Something is wrong');
    				}
    			}else {
    				$returnData = array('status' => 0, 'msg' => 'Received Faild.');
    			}
    		}else {
    			$returnData = array('status' => 1, 'msg' => 'Received Faild. Something is wrong');
    		}
    		return response()->json($returnData);
    	}
    }
    // View Order Details
    public function view_order_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $BiningAdvice = BiningAdvice::select('order_detail_id', 'order_id', 'qty_appr', 'product_id')->where([['order_id', '=', $request->id]])->get()->toArray();
            if(sizeof($BiningAdvice) > 0) {
                foreach($BiningAdvice as $data) {
                    $part_brand_name = "";
                    $part_name = "";
                    $unit_name = "";
                    $manufacturing_no = [];
                    $pmpno = "";
                    $Products = Products::select('part_name_id', 'pmpno', 'part_brand_id', 'unit')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        $PartBrand = PartBrand::select('part_brand_name')->where([['part_brand_id', '=', $Products[0]['part_brand_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartBrand)) {
                            if(!empty($PartBrand[0]['part_brand_name'])) $part_brand_name = $PartBrand[0]['part_brand_name'];
                        }
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                        if(!empty($PartName)) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                        $WmsUnits = WmsUnit::select('unit_name')->where([['unit_id', '=', $Products[0]['unit']]])->get()->toArray();
                        if(!empty($WmsUnits)) {
                            if(!empty($WmsUnits[0]['unit_name'])) $unit_name = $WmsUnits[0]['unit_name'];
                        }
                        $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                        if(!empty($ManufacturingNo)) {
                            $manufacturing_no = $ManufacturingNo;
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    }
                    array_push($returnData, array('order_id' => $data['order_id'], 'part_brand_name' => $part_brand_name, 'part_name' => $part_name, 'pmpno' => $pmpno, 'unit_name' => $unit_name, 'qty' => $data['qty_appr'], 'manufacturing_no' => $manufacturing_no));
                }
            }
            $html = view('backend.receiving_and_putaway.order_details')->with([
                'order_data' => $returnData,
                // 'is_approved' => $is_approved,
                // 'invoice_file' => $invoice_file,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // PDF Download
    public function pdf_bining_advice(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_request_order_to_html($id));
        return $pdf->stream();
    }
    function convert_request_order_to_html($id) {
        $returnData = [];
        $BiningAdvice = BiningAdvice::select('order_detail_id', 'order_id', 'qty_appr', 'product_id')->where([['order_id', '=', $id]])->get()->toArray();
        if(sizeof($BiningAdvice) > 0) {
            foreach($BiningAdvice as $data) {
                $part_brand_name = "";
                $part_name = "";
                $unit_name = "";
                $manufacturing_no = [];
                $pmpno = "";
                $Products = Products::select('part_name_id', 'pmpno', 'part_brand_id', 'unit')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    $PartBrand = PartBrand::select('part_brand_name')->where([['part_brand_id', '=', $Products[0]['part_brand_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($PartBrand)) {
                        if(!empty($PartBrand[0]['part_brand_name'])) $part_brand_name = $PartBrand[0]['part_brand_name'];
                    }
                    $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                    if(!empty($PartName)) {
                        if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                    }
                    $WmsUnits = WmsUnit::select('unit_name')->where([['unit_id', '=', $Products[0]['unit']]])->get()->toArray();
                    if(!empty($WmsUnits)) {
                        if(!empty($WmsUnits[0]['unit_name'])) $unit_name = $WmsUnits[0]['unit_name'];
                    }
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(!empty($ManufacturingNo)) {
                        $manufacturing_no = $ManufacturingNo;
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                }
                array_push($returnData, array('order_id' => $data['order_id'], 'part_brand_name' => $part_brand_name, 'part_name' => $part_name, 'pmpno' => $pmpno, 'unit_name' => $unit_name, 'qty' => $data['qty_appr'], 'manufacturing_no' => $manufacturing_no));
            }
        }
        return view('backend.receiving_and_putaway.download_bining_advice')->with([
            'order_data' => $returnData,
            'order_id' => $id
        ]);
    }
    
    public function download_barcode_modal(Request $request) {
        
    	if ($request->ajax()) {
    	    $html = view('backend.receiving_and_putaway.barcode_modal')->with([
                'order_id' => $request->order_id,
    			'barcode_number' => $request->barcode_number,
    			'selectQty' => $request->good_quentity
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
    	}
    }
    
    public function barcodeView($barcode_number) {
        $barcode_number = $barcode_number;
        $filename = $barcode_number.".png";
     	$imagePath = Storage::disk('public')->get('barcodes/'.$barcode_number.'.png');
        return "data:image/png;base64,".base64_encode($imagePath);
    }
    
    public function download_barcode(Request $request) {
        $barcode_number = $request->barcode_number;
        $download_no = $request->download_no;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_download_barcode_to_html($barcode_number, $download_no));
        return $pdf->stream();
    }
    
    function convert_download_barcode_to_html($barcode_number, $download_no) {
        $barcodeImges = [];
        for($i=1; $i<=$download_no; $i++) {
            $barcode = $this->barcodeView($barcode_number);
            array_push($barcodeImges, ['images' => $barcode]);
        }
        return view('backend.receiving_and_putaway.download_barcode')->with([
            'barcodeImges' => $barcodeImges
        ]);
    }
    
}