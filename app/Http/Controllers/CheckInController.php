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
use App\BiningAdvice;
use App\CheckIn;
use App\CheckInDetails;
use App\WmsStock;
use App\PurchaseOrderReturn;
use App\WmsLots;
use App\BinningLocation;
use App\BarcodeScannDetails;
use App\DefectiveBin;
use App\DefectiveBinDetails;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckInController extends Controller {

    public function index() {
        return \View::make("backend/receiving_and_putaway/check_in")->with(array());
    }
    public function list_check_in(Request $request) {
    	if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('check_in');
            $query->select('check_in_id', 'order_id');
            $query->where([['status', '!=', '2']]);
            //$query->groupBy('order_id');
            if($keyword) {
                $sql = "order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('check_in_id', 'asc');
                else
                    $query->orderBy('check_in_id', 'desc');
            }
            else
            {
                $query->orderBy('check_in_id', 'DESC');
            }
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('items', function ($query) {
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('quantity');
                return $selectQty;
            })
            ->addColumn('good_quantity', function ($query) {
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('good_quantity');
                return $selectQty;
            })
            ->addColumn('bad_quantity', function ($query) {
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('bad_quantity');
                return $selectQty;
            })
            ->addColumn('supplier_name', function ($query) {
                $supplier_name = "";
                $selectSupID = orders::select('supplier_id')->where('order_id',$query->order_id)->get()->toArray();
                if(sizeof($selectSupID) > 0)
                {
                    if(!empty($selectSupID[0]['supplier_id']))
                    {
                        $selectSupplier = Suppliers::select('full_name')->where('supplier_id', $selectSupID[0]['supplier_id'])->get()->toArray();
                        if(sizeof($selectSupplier) > 0) {
                            $supplier_name = $selectSupplier[0]['full_name'];
                        }
                    }
                }
                
                return $supplier_name;
            })
            ->addColumn('details', function ($query) {
                
                $selectQty = CheckInDetails::where('order_id',$query->order_id)->sum('good_quantity');
                
                $details = "";
                $BinningLocation = BinningLocation::where([['order_id', '=', $query->order_id]])->get()->toArray();
                if(sizeof($BinningLocation) > 0) {
                    $details = '<a href="javascript:void(0)" class="view-check-in-details" data-id="'.$query->order_id.'" data-check_in_id="'.$query->check_in_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                }else {
                    $details = '<a href="javascript:void(0)" class="view-check-in" data-id="'.$query->order_id.'" data-check_in_id="'.$query->check_in_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a> ';
                }
                
                $downloadBarcode = "";
                $getBarcodeNumber = Orders::select('barcode_number')->where('order_id',$query->order_id)->get()->toArray();
                if(sizeof($getBarcodeNumber) > 0) {
                    if(!empty($getBarcodeNumber[0]['barcode_number'])) {
                        $downloadBarcode = '<a href="javascript:void(0)" type="button" class="btn btn-warning btn-sm download-barcode-modal" title="Download Barcode" data-order_id="'.$query->order_id.'" data-barcode_number="'.$getBarcodeNumber[0]['barcode_number'].'" data-good_quentity="'.$selectQty.'"><i class="fa fa-barcode"></i></a>';
                    }
                }
                $details = $details." ".$downloadBarcode;
                
                return $details;
            })
            //<a href="javascript:void(0)" class="view-barcode-modal" data-id="'.$query->order_id.'" data-check_in_id="'.$query->check_in_id.'" title="View"><span class="badge badge-success"><i class="fa fa-barcode"></i></span></a>
            ->addColumn('action', function ($query) {
                $BinningLocation = BinningLocation::where([['order_id', '=', $query->order_id]])->get()->toArray();
                if(sizeof($BinningLocation) > 0) {
                    $action = "";
                }else {
                    $action = '<a href="javascript:void(0)" class="delete-check-in" data-id="'.$query->order_id.'" data-check_in_id="'.$query->check_in_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                }
                return $action;
            })
            ->addColumn('barcode_status', function ($query) {
                
                $barcode_status = '<a href="javascript:void(0)" class="view-barcode-modal badge badge-warning" data-id="'.$query->order_id.'" data-check_in_id="'.$query->check_in_id.'" title="Barcode Scan"><span class=""><i class="fa fa-barcode"></i></span> Barcode Scan</a>';
                $BarcodeScannDetails = BarcodeScannDetails::where([['order_id', '=', $query->order_id]])->get()->toArray();
                
                if(sizeof($BarcodeScannDetails) > 0) {
                    
                    $barcode_status = '<span class="badge badge-success">Completed</span>';
                }
                return $barcode_status;
            })
            ->rawColumns(['details', 'action', 'barcode_status'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // Add
    public function add_check_in(Request $request){
        $query = DB::table('bining_advice as b');
        $query->select('b.order_id');
        $query->join('check_in as ci', 'ci.order_id', '=', 'b.order_id', 'left');
        //$query->where([['b.status', '=', '1']]);
        $query->whereNull('ci.order_id');
        $query->groupBy('b.order_id');
        $listbiningAdvice = $query->get()->toArray();
        return \View::make("backend/receiving_and_putaway/check_in_form")->with([
            'listbiningAdvice' => $listbiningAdvice
        ])->render();
    }
    public function get_order_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $warehouse_id = "";
            $Orders = Orders::select('warehouse_id')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            if(sizeof($Orders) > 0) {
                if(!empty($Orders[0]['warehouse_id'])) $warehouse_id = $Orders[0]['warehouse_id'];
            }
            $BiningAdvice = BiningAdvice::select('order_id', 'product_id', 'qty_appr', 'supplier_id')->where([['order_id', '=', $request->order_id]])->get()->toArray();
            if(sizeof($BiningAdvice) > 0) {
                $HTMLContent = '';
                $sl = 1;
                foreach($BiningAdvice as $data) {
                    $part_name = "";
                    $pmpno = "";
                    $price = "";
                    $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'last_po_price')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                    if(sizeof($Products) > 0) {
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        if(!empty($Products[0]['last_po_price'])) {
                            $price = $Products[0]['last_po_price'];
                        }else {
                            $price = $Products[0]['pmrprc'];
                        }
                    }
                    $supplier_id = '';
                    if(!empty($data['supplier_id'])) {
                        $supplier_id = $data['supplier_id'];
                    }
                    $WmsLots = WmsLots::select('lot_id', 'lot_name')->where('status',1)->get()->toArray();
                    $HTMLContent .= '<tr><td>'.($sl+1).'</td><td><input type="hidden" name="product_id[]" value="'.$data['product_id'].'"><input type="text" class="form-control part_name" value="'.$part_name.'" readonly></td><td><input type="text" class="form-control" value="'.$pmpno.'" readonly></td><td><input type="text" class="form-control price" value="'.$price.'" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" value="'.$data['qty_appr'].'" readonly></td><td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control good-quantity" name="good_quantity[]"></td><td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control shortage-quantity" name="shortage_quantity[]"></td><td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control excess-quantity" name="excess_quantity[]"></td><td><input type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control bad-quantity" name="bad_quantity[]"><input type="hidden" class="form-control supplier-id" name="supplier_id[]" value="'.$supplier_id.'"></td><td style="width: 130px;"><select class="form-control" name="lot_name[]"><option value="">Select</option>';
                    if(sizeof($WmsLots)>0) {
                        foreach($WmsLots as $lot) {
                            $HTMLContent .= '<option value="'.$lot['lot_id'].'">'.$lot['lot_name'].'</option>';
                        }
                    }
                    $HTMLContent .= '</select></td></tr>';
                    //array_push($returnData, array('product_id' => $data['product_id'], 'quantity' => $data['qty_appr'], 'supplier_id' => $supplier_id, 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price, 'WmsLots' => $WmsLots));
                    $sl++;
                }
                return response()->json(["status" => 1, "data" => $HTMLContent, 'warehouse_id' => $warehouse_id]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Save
    public function save_check_in(Request $request) {
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->product_id) > 0) {
                $CheckIn = CheckIn::where([['order_id', '=', $request->hidden_id]])->get()->toArray();
                if(sizeof($CheckIn) > 0) {
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        CheckInDetails::where([['order_id', '=', $request->hidden_id], ['product_id', '=', $request->product_id[$i]]])->update(['good_quantity' => $request->good_quantity[$i], 'bad_quantity' => $request->bad_quantity[$i], 'shortage_quantity' => $request->shortage_quantity[$i], 'excess_quantity' => $request->excess_quantity[$i], 'supplier_id' => $request->supplier_id[$i]]);
                        $stock = 0;
                        $oldStock = WmsStock::select('qty')->where([['check_in_id', '=', $request->hidden_check_in_id], ['product_id', '=', $request->product_id[$i]]])->get()->toArray();
                        if(sizeof($oldStock) > 0) {
                            $stock = $oldStock[0]['qty'];
                            Products::where('product_id', $request->product_id[$i])->update(array('current_stock' => DB::raw('current_stock - '.$stock)));
                        }
                        WmsStock::where([['check_in_id', '=', $request->hidden_check_in_id], ['product_id', '=', $request->product_id[$i]]])->update(['qty' => $request->good_quantity[$i]]);
                        Products::where('product_id', $request->product_id[$i])->update(array('current_stock' => DB::raw('current_stock + '.$request->good_quantity[$i])));
                        $flag++;
                    }
                    if($flag == sizeof($request->product_id)) {
                        return response()->json(["status" => 1, "msg" => "Update Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Update Faild!"]);
                    }
                }else {
                    
                    // DefectiveBin
                    // DefectiveBinDetails
                    
                    $data = new CheckIn;
                    $data->order_id = $request->order_id;
                    $data->status = "1";
                    $data->save();
                    $last_id = $data->id;
                    for($i=0; $i<sizeof($request->product_id); $i++) {
                        $data2 = new CheckInDetails;
                        $data2->order_id = $request->order_id;
                        $data2->product_id = $request->product_id[$i];
                        $data2->supplier_id = $request->supplier_id[$i];
                        $data2->quantity = $request->quantity[$i];
                        $data2->good_quantity = $request->good_quantity[$i];
                        $data2->bad_quantity = $request->bad_quantity[$i];
                        $data2->shortage_quantity = $request->shortage_quantity[$i];
                        $data2->excess_quantity = $request->excess_quantity[$i];
                        $data2->status = "1";
                        $data2->save();
                        $data3 = new WmsStock;
                        $data3->check_in_id = $last_id;
                        $data3->product_id = $request->product_id[$i];
                        $good_quantity = 0;
                        if(!empty($request->good_quantity[$i])) {
                            $good_quantity = $request->good_quantity[$i];
                        }
                        $data3->qty = $good_quantity;
                        $data3->warehouse_id = $request->hidden_warehouse_id;
                        $data3->lot_name = $request->lot_name[$i];
                        $data3->status = "1";
                        $data3->save();
                        Products::where('product_id', $request->product_id[$i])->update(array('current_stock' => DB::raw('current_stock + '.$good_quantity)));
                        
                        if(!empty($request->bad_quantity[$i])) {
                            
                            $bindata = new DefectiveBinDetails;
                            $bindata->order_id = $request->order_id;
                            $bindata->product_id = $request->product_id[$i];
                            $bindata->supplier_id = $request->supplier_id[$i];
                            $bindata->quantity = $request->quantity[$i];
                            $bindata->bad_quantity = $request->bad_quantity[$i];
                            $bindata->status = "2";
                            $bindata->save();
                            
                        }
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
    public function delete_check_in(Request $request) {
        if ($request->ajax()) {
            $CheckIn = CheckIn::where([['order_id', '=', $request->id]])->delete();
            if($CheckIn) {
                $getCheckInDetails = CheckInDetails::where([['order_id', '=', $request->id]])->get()->toArray();
                if(sizeof($getCheckInDetails) >0) {
                    foreach($getCheckInDetails as $data) {
                        $stock = 0;
                        $oldStock = WmsStock::select('qty')->where([['check_in_id', '=', $request->check_in_id], ['product_id', '=', $data['product_id']]])->get()->toArray();
                        if(sizeof($oldStock) > 0) {
                            $stock = $oldStock[0]['qty'];
                            Products::where('product_id', $data['product_id'])->update(array('current_stock' => DB::raw('current_stock - '.$stock)));
                        }
                    }
                }
                $CheckInDetails = CheckInDetails::where([['order_id', '=', $request->id]])->delete();
                if($CheckInDetails) {
                    $WmsStock = WmsStock::where([['check_in_id', '=', $request->check_in_id]])->delete();
                    if($WmsStock) {
                        return response()->json(["status" => 1, "msg" => "Delete Succesful."]);
                    }else {
                        return response()->json(["status" => 0, "msg" => "Delete Faild! Something is wrong."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "Delete Faild! Something is wrong."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Delete Faild!"]);
            }
        }
    }
    // View
    public function view_check_in(Request $request){
        $returnData = [];
        $CheckIn = CheckInDetails::select('*')->where([['order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($CheckIn) > 0) {
            foreach($CheckIn as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'last_po_price')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['last_po_price'])) {
                        $price = $Products[0]['last_po_price'];
                    }else {
                        $price = $Products[0]['pmrprc'];
                    }
                    //if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'supplier_id' => $data['supplier_id'], 'quantity' => $data['quantity'], 'good_quantity' => $data['good_quantity'], 'bad_quantity' => $data['bad_quantity'], 'shortage_quantity' => $data['shortage_quantity'], 'excess_quantity' => $data['excess_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            }
        }
        $html = \View::make("backend/receiving_and_putaway/check_in_view")->with([
            'check_in_data' => $returnData,
            'order_id' => $request->id,
            'check_in_id' => $request->check_in_id,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    public function view_check_in_details(Request $request){
        $returnData = [];
        $CheckIn = CheckInDetails::select('*')->where([['order_id', '=', $request->id]])->get()->toArray();
        if(sizeof($CheckIn) > 0) {
            foreach($CheckIn as $data) {
                $part_name = "";
                $pmpno = "";
                $price = "";
                $Products = Products::select('part_name_id', 'pmpno', 'pmrprc', 'last_po_price')->where([['product_id', '=', $data['product_id']]])->get()->toArray();
                if(sizeof($Products) > 0) {
                    if(!empty($Products[0]['part_name_id'])) {
                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                        if(sizeof($PartName) > 0) {
                            if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                        }
                    }
                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                    if(!empty($Products[0]['last_po_price'])) {
                        $price = $Products[0]['last_po_price'];
                    }else {
                        $price = $Products[0]['pmrprc'];
                    }
                    //if(!empty($Products[0]['pmrprc'])) $price = $Products[0]['pmrprc'];
                }
                array_push($returnData, array('product_id' => $data['product_id'], 'supplier_id' => $data['supplier_id'], 'quantity' => $data['quantity'], 'good_quantity' => $data['good_quantity'], 'bad_quantity' => $data['bad_quantity'], 'shortage_quantity' => $data['shortage_quantity'], 'excess_quantity' => $data['excess_quantity'], 'part_name' => $part_name, 'pmpno' => $pmpno, 'price' => $price));
            }
        }
        $html = \View::make("backend/receiving_and_putaway/check_in_details_view")->with([
            'check_in_data' => $returnData,
            'order_id' => $request->id,
            'check_in_id' => $request->check_in_id,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    public function check_in_export(){
        $query = DB::table('check_in')
        ->select('check_in_id', 'order_id')
        ->where([['status', '!=', '2']])
        ->orderBy('order_id', 'DESC');
        $data = $query->get()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CheckIn_id');
        $sheet->setCellValue('B1', 'Order_no');
        $sheet->setCellValue('C1', 'Items');
        $sheet->setCellValue('D1', 'Good_quantity');
        $sheet->setCellValue('E1', 'Bad_quantity');
        $rows = 2;
        foreach($data as $empDetails){
            $items = '';
            if (!empty($empDetails->order_id)) {
                $items = CheckInDetails::where('order_id',$empDetails->order_id)->sum('quantity');
            }
            $good_quantity = '';
            if (!empty($empDetails->order_id)) {
                $good_quantity = CheckInDetails::where('order_id',$empDetails->order_id)->sum('good_quantity');
            }
            $bad_quantity = '';
            if (!empty($empDetails->order_id)) {
                $bad_quantity = CheckInDetails::where('order_id',$empDetails->order_id)->sum('bad_quantity');
            }

            $sheet->setCellValue('A' . $rows, $empDetails->check_in_id);
            $sheet->setCellValue('B' . $rows, $empDetails->order_id);
            $sheet->setCellValue('C' . $rows, $items);
            $sheet->setCellValue('D' . $rows, $good_quantity);
            $sheet->setCellValue('E' . $rows, $bad_quantity);
            $rows++;
        }
        $fileName = "check_in_details.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    public function view_barcode_modal(Request $request) {
        
        $html = \View::make("backend/receiving_and_putaway/view_checkin_barcode_modal")->with([
            'check_in_id' => $request->check_in_id,
        ])->render();
        return response()->json(["status" => 1, "message" => $html]);
    }
    
    public function save_barcode_details_by_scann(Request $request) {
        
        $returnDatra = [];
        $selectExitBarcode = BarcodeScannDetails::select('barcode_number')->where([['barcode_number', '=', $request->barcode_no]])->get()->toArray();
        
        if(sizeof($selectExitBarcode) > 0) {
            
            $returnDatra = ['status' => 0, 'msg' => 'Scann Faild! Barcode already exist in our records!'];
        }else {
            
            $selectOrders = Orders::select('order_id', 'supplier_id')->where([['barcode_number', '=', $request->barcode_no]])->get()->toArray();
            
            if(sizeof($selectOrders) > 0) {
                
                $order_id = $selectOrders[0]['order_id'];
                $supplier_id = $selectOrders[0]['supplier_id'];
                
                $selectOrderDetails = OrderDetail::select('product_id')->where([['order_id', '=', $order_id]])->get()->toArray();
                
                if(sizeof($selectOrderDetails) > 0) {
                    
                    $flag = 0;
                    foreach($selectOrderDetails as $val) {
                        
                        $part_no = "";
                        $part_name = "";
                        $selectProduct = Products::select('pmpno', 'part_name_id')->where([['product_id', '=', $val['product_id']]])->get()->toArray();
                        
                        if(sizeof($selectProduct) > 0) {
                            
                            $part_no = $selectProduct[0]['pmpno'];
                            $selectPartName = PartName::select('part_name')->where([['part_name_id', '=', $selectProduct[0]['part_name_id']]])->get()->toArray();
                            
                            if(sizeof($selectPartName) > 0) {
                                
                                $part_name = $selectPartName[0]['part_name'];
                            }
                        }
                        $data = new BarcodeScannDetails;
                        $data->order_id = $order_id;
                        $data->part_no =$part_no;
                        $data->part_name = $part_name;
                        $data->supplier_name = $supplier_id;
                        $data->barcode_number = $request->barcode_no;
                        // $data->invoice_no =
                        // $data->customer =
                        // $data->date_of_invoice =
                        $data->save();
                        $flag++;
                    }
                    
                    if($flag == sizeof($selectOrderDetails)) {
                        $returnDatra = ['status' => 1, 'msg' => 'Scann Successful.'];
                    }else {
                        $returnDatra = ['status' => 0, 'msg' => 'Scann Faild! Something is wrong.'];
                    }
                    
                }else {
                
                    $returnDatra = ['status' => 0, 'msg' => 'Something is wrong, please try again!'];
                }
                
            }else {
                
                $returnDatra = ['status' => 0, 'msg' => 'Barcode not match in our records!'];
            }
        }
        
        return response()->json($returnDatra);
    }
}