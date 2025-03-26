<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Suppliers;
use App\Products;
use App\Orders;
use App\OrderDetail;
use App\ProductCategories;
use App\PartName;
use App\OrderRequest;
use App\OrderRequestDetails;
use App\OrderQuotation;
use App\ConsignmentReceipt;
use App\CheckIn;
use App\OrderRequestQuotationPrices;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Expenses;
use App\PurchaseOrderExpenses;
use App\AlternatePartNo;
use App\CheckInDetails;
use App\WmsProductTaxes;
use App\PartBrand;
use Picqer\Barcode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Storage;
use App\PurchaseReceipt;
use App\ConsignmentReceiptDetails;
use App\VatType;
use App\SaleOrderDetails;
use App\OrdersExpenses;
use App\PurchaseOrderReturnDetails;


class PurchaseOrderManagementController extends Controller {
    
    public function generate_barcode() {
        // $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
        // $image = $generatorPNG->getBarcode('000005263635', $generatorPNG::TYPE_CODE_128);
    
        // Storage::put('barcodes/000005263635.png', $image);
    
        //return response($image)->header('Content-type','image/png');
        echo $this->generateRandomString();
    }
    
    function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function purchase_order_management() {
        return \View::make("backend/purchase_order/purchase_order_management")->with([
            'PartBrand' => PartBrand::select('part_brand_id','part_brand_name')->where([['status', '=', 1], ['part_brand_name', '!=', '']])->orderBy('part_brand_name', 'ASC')->get()->toArray(),
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    // Purchase Order Management Modal
    public function add_purchase_order_management(Request $request){
        if ($request->ajax()) {
            $hidden_order_id = "";
            $Orders = [];
            $OrderDetailArray = [];
            if(!empty($request->order_id)) {
                $hidden_order_id = $request->order_id;
                $Orders = Orders::where([['order_id', '=', $request->order_id]])->get()->toArray();
                $OrderDetail = OrderDetail::where([['order_id', '=', $request->order_id]])->get()->toArray();
                if(sizeof($OrderDetail) > 0) {
                    foreach($OrderDetail as $od) {
                        $query = DB::table('products as p');
                        $query->join('product_categories as pc', 'pc.category_id', '=', 'p.ct', 'left');
                        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                        $query->select('p.*','pc.category_name as c_name', 'pn.part_name');
                        $query->where('p.product_id', '=', $od['product_id']);
                        $selectDdata=$query->get()->toArray();
                        array_push($OrderDetailArray, array('pmpno' => $selectDdata[0]->pmpno, 'part_name' => $selectDdata[0]->part_name, 'product_id' => $selectDdata[0]->product_id, 'c_name' => $selectDdata[0]->c_name, 'ct' => $selectDdata[0]->ct, 'gst' => 0, 'pmrprc' => $od['mrp'], 'qty' => $od['qty']));
                    }
                }
            }
            $html = view('backend.purchase_order.purchase_order_form')->with([
                'warehouses_data' => Warehouses::select('warehouse_id', 'name')->where([['status', '=', '1']])->get()->toArray(),
                'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray(),
                'hidden_order_id' => $hidden_order_id,
                'Orders' => $Orders,
                'OrderDetailArray' => $OrderDetailArray,
                'gst_value' => WmsProductTaxes::where('tax_name', 'like', '%vat%')->get()->toArray(),
                'VatTypeData' => VatType::orderBy('description', 'ASC')->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function save_purchase_order_management(Request $request) {
        $returnData = [];
        $order_request_id = null;
        if(!empty($request->order_request_id)) {
            $order_request_id = $request->order_request_id;
        }
        $msg = "Purchase Order Create Successful.";
        $return_url = "purchase-order-management";
        $orders_status = 1;
        if($request->orders_status == "SaveOrder") {
            $orders_status = 2;
            $return_url = "save-purchase-order";
            $msg = "Purchase Order Save Successful.";
        }
        
        $barcode_number = "";
        
        if($request->orders_status == "CreateOrder") {
            
            $barcode_number = $this->generateRandomString();
            $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $image = $generatorPNG->getBarcode($barcode_number, $generatorPNG::TYPE_CODE_128);
        
            Storage::put('public/barcodes/'.$barcode_number.'.png', $image);
        }
        $order_id = '';
        if(!empty($request->hidden_order_id)) {
            $order_id = $request->hidden_order_id;
            Orders::where([['order_id', '=', $request->hidden_order_id]])->update(['deliverydate' => date('Y-m-d', strtotime(str_replace('/','-',$request->estimated_delivery_date)))]);
            OrderDetail::where([['order_id', '=', $request->hidden_order_id]])->delete();
        }else {
            $data = new Orders;
            $data->datetime = date('Y-m-d');
            $data->deliverydate = date('Y-m-d', strtotime(str_replace('/','-',$request->estimated_delivery_date)));
            $supplier_id = $request->supplier;
            if(!empty($request->hidden_supplier_id)) {
                $supplier_id = $request->hidden_supplier_id;
            }
            $data->supplier_id = $supplier_id;
            $data->order_request_unique_id = $order_request_id;
            $data->warehouse_id = $request->warehouse;
            $data->sub_total = $request->sub_total;
            $data->total_tax = $request->total_tax;
            $data->total_expense = $request->total_expense;
            $data->grand_total = $request->grand_total;
            $data->approved = "0";
            $data->received = "0";
            $data->remarks = $request->remarks;
            $data->is_delete = "0";
            $data->orders_status = $orders_status;
            $data->vat_type_id = $request->vat_type_value;
            $data->barcode_number = $barcode_number;
            $saveData = $data->save();
            if($saveData) {
                $order_id = $data->id;
            }
        }
        
        if(!empty($request->expenses_value)) {
            for($e = 0; $e<sizeof($request->expenses_value); $e++) {
                $data3 = new OrdersExpenses;
                $data3->order_id = $order_id;
                $data3->expense_description = $request->expenses_description[$e];
                $data3->expense_value = $request->expenses_value[$e];
                $data3->expense_vat = $request->expenses_vat[$e];
                $save3rd = $data3->save();
            }
        }
        
        $flag = 0;
        if(sizeof($request->entry_product) > 0) {
            for($i = 0; $i<sizeof($request->entry_product); $i++) {
                $data2 = new OrderDetail;
                $data2->order_id = $order_id;
                $data2->warehouse_id = $request->warehouse;
                $data2->product_id = $request->entry_product[$i];
                $data2->qty = $request->entry_product_quantity[$i];
                $data2->mrp = $request->mrp[$i];
                $save2nd = $data2->save();
                if($orders_status == 1) {
                    $last_po_price = 0;
                    if(!empty($request->mrp[$i])) {
                        $last_po_price = $request->mrp[$i];
                    }
                    $previous_lc_price = 0;
                    if(!empty($request->previous_lc_price[$i])) {
                        $previous_lc_price = $request->previous_lc_price[$i];
                    }
                    Products::where('product_id', $request->entry_product[$i])->update(['last_po_price' => $last_po_price, 'lc_price' => $last_po_price, 'lc_date' => date('Y-m-d'), 'prvious_lc_price' => $previous_lc_price, 'prvious_lc_date' => date('Y-m-d')]);
                }
                $flag++;
            }
        }
        if($flag == sizeof($request->entry_product)) {
            $returnData = ["status" => 1, "msg" => $msg, "return_url" => $return_url];
        }else {
            $returnData = ["status" => 0, "msg" => "Something is wrong."];
        }
        return response()->json($returnData);
    }
    public function list_purchase_order_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'o.barcode_number', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1'], ['orders_status', '=', '1']]);
            if($keyword)
            {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            
            $data_purchase=$query->get()->toArray();
            $purchase= new Collection;
            foreach($data_purchase as $data_array){
                
                if(!empty($request->filter_part_brand)) {
                    
                    $selectOrderDetails = OrderDetail::select('order_id')->where([['order_id', '=', $data_array->order_id]])->whereIn('product_id', [DB::raw("SELECT product_id FROM `products` WHERE `part_brand_id` = '".$request->filter_part_brand."' AND `current_stock` > 0")])->get()->toArray();
                    
                    if(sizeof($selectOrderDetails) > 0) {
                        
                        $order_date = "";
                        if(!empty($data_array->datetime)) {
                            $order_date = date('d M Y', strtotime($data_array->datetime));
                        }
                        
                        $deliverydate = "";
                        if(!empty($data_array->deliverydate)) {
                            $deliverydate = date('d M Y', strtotime($data_array->deliverydate));
                        }
                        
                        $selectQty = "";
                        if(!empty($data_array->order_id)) {
                            $selectQty = OrderDetail::where('order_id',$data_array->order_id)->sum('qty');
                        }
                        
                        $details = '<a href="javascript:void(0)" class="view-order-details" data-id="'.$data_array->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                        $selectCheckIn = CheckIn::where([['order_id', '=', $data_array->order_id]])->get()->toArray();
                        if(sizeof($selectCheckIn) > 0) {
                            $details .= ' <a href="javascript:void(0)" class="purchase-order-invoice" data-id="'.$data_array->order_id.'" title="Order Invoice"><span class="badge badge-success"><i class="fa fa-file-pdf-o"></i></span></a>';
                        }
                        
                        $approved_status = "";
                        
                        if($data_array->approved == '1') {
                            $approved_status = '<span class="badge badge-success">Approved</span>';
                        }else {
                            $approved_status = '<span class="badge badge-danger">Not Approved</span>';
                        }
                        
                        $received_status = "";
                        if($data_array->received == '1') {
                            $received_status = '<span class="badge badge-success">Received</span>';
                        }else {
                            $received_status = '<span class="badge badge-danger">Not Received</span>';
                        }
                        
                        $action = "";
                        $delete = ' <a href="javascript:void(0)" class="delete-purchase-order" data-id="'.$data_array->order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a> ';
                        $check_in = CheckIn::where('order_id',$data_array->order_id)->where('status',1)->get()->toArray();
                        $consignment_receipt = ConsignmentReceipt::where('order_id',$data_array->order_id)->where('status',1)->get()->toArray();
                        if(sizeof($check_in) == 0 && sizeof($consignment_receipt) > 0)
                        {
                            $binning_advice = ' <a href="'.url('create-bining-advice').'" class="binning-advice"><button type="button" class="btn btn-success btn-sm" title="Crate Binning Advice"><i class="fa fa-plus"></i></button></a> ';
                            $action .= $binning_advice;
                        }
                        
                        if($data_array->invoice == "") {
                            $action .= $delete;
                        }
                        
                        $barcode = $data_array->barcode_number;
                        $purchase->push(['order_id'=> $data_array->order_id,'order_date' => $order_date, 'deliverydate' => $deliverydate, 'warehouse_name' =>$data_array->warehouse_name, 'supplier' => $data_array->supplier, 'item' => $selectQty, 'barcode' => $barcode, 'details'=> $details, 'approved_status' => $approved_status, 'received_status' => $received_status, 'action' => $action]);
                    }
                        
                }else {
                    $order_date = "";
                    if(!empty($data_array->datetime)) {
                        $order_date = date('d M Y', strtotime($data_array->datetime));
                    }
                    
                    $deliverydate = "";
                    if(!empty($data_array->deliverydate)) {
                        $deliverydate = date('d M Y', strtotime($data_array->deliverydate));
                    }
                    
                    $selectQty = "";
                    if(!empty($data_array->order_id)) {
                        $selectQty = OrderDetail::where('order_id',$data_array->order_id)->sum('qty');
                    }
                    
                    $details = '<a href="javascript:void(0)" class="view-order-details" data-id="'.$data_array->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                    $selectCheckIn = CheckIn::where([['order_id', '=', $data_array->order_id]])->get()->toArray();
                    if(sizeof($selectCheckIn) > 0) {
                        $details .= ' <a href="javascript:void(0)" class="purchase-order-invoice" data-id="'.$data_array->order_id.'" title="Order Invoice"><span class="badge badge-success"><i class="fa fa-file-pdf-o"></i></span></a>';
                    }
                
                    $approved_status = "";
                    
                    if($data_array->approved == '1') {
                        $approved_status = '<span class="badge badge-success">Approved</span>';
                    }else {
                        $approved_status = '<span class="badge badge-danger">Not Approved</span>';
                    }
                    
                    $received_status = "";
                    if($data_array->received == '1') {
                        $received_status = '<span class="badge badge-success">Received</span>';
                    }else {
                        $received_status = '<span class="badge badge-danger">Not Received</span>';
                    }
                    
                    $action = "";
                    $delete = ' <a href="javascript:void(0)" class="delete-purchase-order" data-id="'.$data_array->order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a> ';
                    $check_in = CheckIn::where('order_id',$data_array->order_id)->where('status',1)->get()->toArray();
                    $consignment_receipt = ConsignmentReceipt::where('order_id',$data_array->order_id)->where('status',1)->get()->toArray();
                    if(sizeof($check_in) == 0 && sizeof($consignment_receipt) > 0)
                    {
                        $binning_advice = ' <a href="'.url('create-bining-advice').'" class="binning-advice"><button type="button" class="btn btn-success btn-sm" title="Crate Binning Advice"><i class="fa fa-plus"></i></button></a> ';
                        $action .= $binning_advice;
                    }
                    
                    if($data_array->invoice == "") {
                        $action .= $delete;
                    }
                    
                    $viewBarcode = "";
                    if(!empty($data_array->barcode_number)) {
                        $barcode = $this->barcodeView($data_array->barcode_number);
                        $viewBarcode = '<img src="'.$barcode.'" alt="Image" title="" style="width:100%; height:25px" /><p>'.$data_array->barcode_number.'</p>';
                    }
                    $purchase->push(['order_id'=> $data_array->order_id,'order_date' => $order_date, 'warehouse_name' => $data_array->warehouse_name, 'supplier' => $data_array->supplier, 'deliverydate' => $deliverydate, 'item' => $selectQty, 'barcode' => $viewBarcode, 'details'=> $details, 'approved_status' => $approved_status, 'received_status' => $received_status, 'action' => $action]);
                }
            }
            
            $datatable_array=Datatables::of($purchase)
                 ->filter(function ($instance) use ($request) {
   
                if (!empty($request->input('search.value'))) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        //echo Str::lower($row['client_name'])."<br>";
                        if (Str::contains(Str::lower($row['warehouse_name']), Str::lower($request->input('search.value')))){
                            return true;
                        }
                        else if (Str::contains(Str::lower($row['supplier']), Str::lower($request->input('search.value')))) {
                            return true;
                        }
                        else if (Str::contains(Str::lower($row['order_id']), Str::lower($request->input('search.value')))) {
                            return true;
                        }
                        else if (Str::contains(Str::lower($row['barcode']), Str::lower($request->input('search.value')))) {
                            return true;
                        }

                        return false;
                    });
                }

            })
            ->rawColumns(['barcode', 'details', 'approved_status', 'received_status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    
    public function barcodeView($barcode_number) {
        $barcode_number = $barcode_number;
        $filename = $barcode_number.".png";
     	$imagePath = Storage::disk('public')->get('barcodes/'.$barcode_number.'.png');
        return "data:image/png;base64,".base64_encode($imagePath);
    }
    
    // export
    public function purchase_order_export()
    {

        $query = DB::table('orders as o')
        ->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier')
        ->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id')
        ->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id')
        ->where([['o.is_delete', '!=', '1']])
        ->orderBy('o.order_id', 'DESC');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order_id');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Warehouse');
        $sheet->setCellValue('D1', 'Supplier');
        $sheet->setCellValue('E1', 'Delivery_date');
        $sheet->setCellValue('F1', 'Items_qty');
        

        $rows = 2;
        foreach($data as $empDetails){
            $order_date = '';
            if(!empty($empDetails->datetime)) {
                $order_date = date('d M Y', strtotime($empDetails->datetime));
            }
            $delivery_date = '';
            if(!empty($empDetails->deliverydate)) {
                $delivery_date = date('d M Y', strtotime($empDetails->deliverydate));
            }

            $item = '';
            if (!empty($empDetails->order_id)) {
                $item = OrderDetail::where('order_id',$empDetails->order_id)->sum('qty');
            }
            // ->addColumn('item', function ($query) {
            //     $selectQty = OrderDetail::where('order_id',$query->order_id)->sum('qty');
            //     return $selectQty;
            // })

            $sheet->setCellValue('A' . $rows, $empDetails->order_id);
            $sheet->setCellValue('B' . $rows, $order_date);
            $sheet->setCellValue('C' . $rows, $empDetails->warehouse_name);
            $sheet->setCellValue('D' . $rows, $empDetails->supplier);
            $sheet->setCellValue('E' . $rows, $delivery_date);
            $sheet->setCellValue('F' . $rows, $item);
            
            // $sheet->setCellValue('G' . $rows, $empDetails->current_stock);
            // $sheet->setCellValue('H' . $rows, $empDetails->pmrprc);
            $rows++;
        }
        $fileName = "Purchase_Order_management.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }

    public function get_product_by_warehouse(Request $request) {
        $returnData = [];
        $selectProducts = Products::select('product_id', 'part_name', 'pmpno')->where([['warehouse_id', '=', $request->warehouse_id], ['is_deleted', '!=', '1']])->get()->toArray();
        if(sizeof($selectProducts) > 0) {
            $returnData = array('status' => 1, 'data' => $selectProducts);
        }else {
            $returnData = array('status' => 0, 'msg' => "No record found.");
        }
        return response()->json($returnData);
    }
    public function get_product_by_supplier(Request $request) {
        $returnData = [];
        $selectProducts = Products::select('product_id', 'part_name', 'pmpno')->where([['supplier_id', '=', $request->supplier_id], ['is_deleted', '!=', '1']])->limit('100')->get()->toArray();
        if(sizeof($selectProducts) > 0) {
            $returnData = array('status' => 1, 'data' => $selectProducts);
        }else {
            $returnData = array('status' => 0, 'msg' => "No record found.");
        }
        return response()->json($returnData);
    }
    public function delete_purchase_order(Request $request) {
        $returnData = [];
        $upData = Orders::where('order_id', $request->id)->update(['is_delete' => "1"]);
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    public function view_purchase_order_details(Request $request){
        if ($request->ajax()) {
            $query = DB::table('order_detail as o');
            $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('o.order_detail_id', 'o.order_id','o.qty', 'o.mrp', 'pn.part_name', 'p.pmpno');
            $query->where([['o.order_id', '=', $request->id]]);
            $orderDetails = $query->get()->toArray();
            $invoice_file = "";
            $invoice_no = "";
            $Orders = Orders::select('invoice', 'invoice_no')->where([['order_id', '=', $request->id]])->get()->toArray();
            if(!empty($Orders)) {
                if(!empty($Orders[0]['invoice'])) $invoice_file = $Orders[0]['invoice'];
                if(!empty($Orders[0]['invoice_no'])) $invoice_no = $Orders[0]['invoice_no'];
            }
            $html = view('backend.purchase_order.purchase_order_details')->with([
                'order_data' => $orderDetails,
                'order_id' => $request->id,
                'invoice_file' => $invoice_file,
                'invoice_no' => $invoice_no,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Order Invoice
    public function purchase_order_invoice(Request $request){
        if ($request->ajax()) {
            $query = DB::table('order_detail as o');
            $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('o.order_detail_id', 'o.order_id','o.qty', 'o.mrp', 'pn.part_name', 'p.pmpno');
            $query->where([['o.order_id', '=', $request->id]]);
            $orderDetails = $query->get()->toArray();
            $invoice_file = "";
            $invoice_no = "";
            $vat_description = "";
            $vat_percentage = "";
            $Orders = Orders::select('invoice', 'invoice_no', 'vat_type_id')->where([['order_id', '=', $request->id]])->get()->toArray();
            if(!empty($Orders)) {
                if(!empty($Orders[0]['invoice'])) $invoice_file = $Orders[0]['invoice'];
                if(!empty($Orders[0]['invoice_no'])) $invoice_no = $Orders[0]['invoice_no'];
                
                if(!empty($Orders[0]['vat_type_id'])) {
                
                    $selectGstType = VatType::select('*')->where([['vat_type_id', '=', $Orders[0]['vat_type_id']]])->get()->toArray();
                    if(sizeof($selectGstType) > 0) {
                        
                        $vat_description = $selectGstType[0]['description'];
                        $vat_percentage = $selectGstType[0]['percentage'];
                    }
                }
                
            }
            $html = view('backend.purchase_order.purchase_order_invoice')->with([
                'order_data' => $orderDetails,
                'order_id' => $request->id,
                'invoice_file' => $invoice_file,
                'invoice_no' => $invoice_no,
                'ExpensesData' => Expenses::orderBy('expenses_description', 'ASC')->get()->toArray(),
                'PurchaseOrderExpenses' => PurchaseOrderExpenses::where('order_id', $request->id)->get()->toArray(),
                'vat_description' => $vat_description,
                'vat_percentage' => $vat_percentage,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function purchase_order_add_expenses(Request $request) {
        $Expenses = Expenses::orderBy('expenses_description', 'ASC')->get()->toArray();
        $budget_expenses_no = 1+ (int)$request->budget_expenses_no;
        $view = "";
        $view = $view.'<div class="col-md-12 col-sm-12 col-xs-12" id="ExpensesDiv'.$budget_expenses_no.'">
                    <div class="form-group" style="display: inline-flex;">
                        <select class="form-control expenses-id" style="margin: 0px 5px 0px 0px;" name="expenses_id[]">
                            <option value="">Select</option>';
                            foreach($Expenses as $edata) {
                            $view = $view.'<option value="'.$edata['expenses_id'].'">'.$edata['expenses_description'].'</option>';
                            }
                        $view = $view.'</select>
                        <div class="">
                            <input type="number" class="form-control expenses-value" name="expenses_value[]" id="" placeholder="Enter value">
                        </div>
                        <a href="javascript:void(0)" onclick="removeExpenses('.$budget_expenses_no.')"><i class="fa fa-times-circle" style="position: absolute; top: 10px;padding-left: 15px;font-size: 20px;"></i></a>
                    </div>
                </div>';
        echo $view;
    }
    public function save_purchase_order_invoice(Request $request) {
        $returnData = [];
        $re = 1;
        PurchaseOrderExpenses::where([['order_id', '=', $request->order_id]])->delete();
        if(!empty($request->expenses_value)) {
            $re = 0;
            $flag = 0;
            for($i=0; $i<sizeof($request->expenses_value); $i++) {
                $data = new PurchaseOrderExpenses;
                $data->expenses_id = $request->expenses_id[$i];
                $data->expenses_value = $request->expenses_value[$i];
                $data->order_id = $request->order_id;
                $data->save();
                $flag++;
            }
            if($flag == sizeof($request->expenses_value)) {
                $re = 1;
            }
        }
        if($re == 1) {
            $returnData = ['status' => 1, 'return_url' => 'print-purchase-order-invoice', 'order_id' => $request->order_id];
        }else {
            $returnData = ['status' => 0, 'msg' => 'Something is wrong! please try again.'];
        }
        return response()->json($returnData);
    }
    public function print_purchase_order_invoice(Request $request) {
        $id = $request->order_id;
        //echo $id; exit();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_purchase_order_invoice_to_html($id));
        return $pdf->stream();
    }
    function convert_purchase_order_invoice_to_html($id) {
        
        $orderDetails = [];
        
        $query = DB::table('check_in_details as c');
        $query->join('orders as o', 'o.order_id', '=', 'c.order_id', 'left');
        $query->join('products as p', 'p.product_id', '=', 'c.product_id', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->join('suppliers as s', 's.supplier_id', '=', 'c.supplier_id', 'left');
        $query->select('o.invoice_no', 'c.order_id', 'c.product_id','c.good_quantity', 'pn.part_name', 'p.pmpno', 's.full_name', 's.phone', 's.email');
        $query->where([['o.order_id', '=', $id]]);
        $selectData = $query->get()->toArray();
        
        if(sizeof($selectData) > 0) {
            
            foreach($selectData as $dt) {
                
                $price = 0;
                $selectPrice = OrderDetail::select('mrp')->where([['order_id', '=', $dt->order_id], ['product_id', '=', $dt->product_id]])->get()->toArray();
                if(sizeof($selectPrice) > 0) {
                    $price = $selectPrice[0]['mrp'];
                }
                array_push($orderDetails, ['invoice_no' => $dt->invoice_no, 'order_id' => $dt->order_id, 'qty' => $dt->good_quantity, 'part_name' => $dt->part_name, 'pmpno' => $dt->pmpno, 'full_name' => $dt->full_name, 'phone' => $dt->phone, 'email' => $dt->email, 'mrp' => $price]);
            }
        }
        
        $PurchaseOrderExpenses = DB::table('purchase_order_expenses as pe')->join('expenses as e', 'e.expenses_id', '=', 'pe.expenses_id', 'left')->select('e.expenses_description', 'pe.expenses_value')->where('pe.order_id', $id)->get()->toArray();
        
        $vat_description = "";
        $vat_percentage = "";
        $Orders = Orders::select('*')->where([['order_id', '=', $id]])->get()->toArray();
        if(!empty($Orders)) {
            if(!empty($Orders[0]['vat_type_id'])) {
                
                $selectGstType = VatType::select('*')->where([['vat_type_id', '=', $Orders[0]['vat_type_id']]])->get()->toArray();
                if(sizeof($selectGstType) > 0) {
                    
                    $vat_description = $selectGstType[0]['description'];
                    $vat_percentage = $selectGstType[0]['percentage'];
                }
            }
        }
        return view('backend.purchase_order.print_purchase_order_invoice')->with([
            'order_data' => $orderDetails,
            'order_id' => $id,
            'PurchaseOrderExpenses' => $PurchaseOrderExpenses,
            'vat_description' => $vat_description,
            'vat_percentage' => $vat_percentage
        ]);
    }
    
    public static function numberTowords(float $amount)
    {
       $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
       // Check if there is any number after decimal
       $amt_hundred = null;
       $count_length = strlen($num);
       $x = 0;
       $string = array();
       $change_words = array(0 => 'Zero', 1 => 'One', 2 => 'Two',
         3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
         7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
         10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
         13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
         16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
         19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
         40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
         70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
      $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
      while( $x < $count_length ) {
           $get_divider = ($x == 2) ? 10 : 100;
           $amount = floor($num % $get_divider);
           $num = floor($num / $get_divider);
           $x += $get_divider == 10 ? 1 : 2;
           if ($amount) {
             $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
             $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
             $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
             '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
             '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
             }else $string[] = null;
           }
       $implode_to_Rupees = implode('', array_reverse($string));
       $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
       " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
       return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
    }
    
    // Get Product By Part No
    public function get_product_by_part_no(Request $request) {
        if ($request->ajax()) {
            if(!empty($request->supplier)) {
                $view = "";
                $query = DB::table('products as p');
                $query->select('p.product_id', 'p.pmpno', 'p.current_stock', 'pn.part_name', 'pb.part_brand_name');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
                $query->whereRaw('is_deleted != 1 and (p.pmpno LIKE "%'.$request->part_no.'%" or replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%")');
                //$query->whereRaw('is_deleted != 1 and (p.pmpno LIKE "%'.$request->part_no.'%" or replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%") and FIND_IN_SET('.$request->warehouse.' ,p.warehouse_id ) and FIND_IN_SET('.$request->supplier.' ,p.supplier_id )');
                $Products = $query->limit(100)->get()->toArray();
                if(sizeof($Products) > 0) {
                    $view = $view.'<ul class="list-group">';
                    foreach($Products as $data) {
                        $alternate_no = [];
                        $alternate_noC = "";
                        $AlternatePartNo = AlternatePartNo::select('alternate_no')->where([['product_id', '=', $data->product_id]])->get()->toArray();
                        if(sizeof($AlternatePartNo) > 0) {
                            foreach($AlternatePartNo as $alt) {
                                $alternate_no[] = "#".$alt['alternate_no'];
                            }
                            $alternate_noC = implode(',', $alternate_no);
                        }
                        $view = $view.'<li class="list-group-item"><a href="#" class="product-details" style="text-decoration: none" data-pmpno="'.$data->pmpno.'">'.$data->part_name.' ('.$data->pmpno.') - '.$data->current_stock.' - '.$data->part_brand_name.'<br>'.$alternate_noC.'</a></li>';
                    }
                    $view = $view.'</ul>';
                    return response()->json(["status" => 1, "data" => $view]);
                }else {
                    return response()->json(["status" => 0, "message" => "No record found."]);
                }
            }else {
                return response()->json(["status" => 0, "message" => "No record found."]);
            }
        }
    }
    // Get Product Details
    public function get_product_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            if(!empty($request->part_no)) {
                $ProductsData = [];
                $query = DB::table('products as p');
                $query->join('product_categories as pc', 'pc.category_id', '=', 'p.ct', 'left');
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->select('p.*','pc.category_name as c_name', 'pn.part_name');
                $query->where('p.pmpno', '=', $request->part_no);
                $selectDdata=$query->get()->toArray();
                if(sizeof($selectDdata) > 0) {
                    array_push($ProductsData, array('product_id' => $selectDdata[0]->product_id, 'pmpno' => $selectDdata[0]->pmpno, 'part_name' => $selectDdata[0]->part_name, 'ct' => $selectDdata[0]->ct, 'c_name' => $selectDdata[0]->c_name, 'pmrprc' => $selectDdata[0]->pmrprc, 'selling_price' => $selectDdata[0]->selling_price, 'last_po_price' => $selectDdata[0]->last_po_price));
                    $product_entry_count = $request->product_entry_count + 1;
                    $returnData = array('status' => 1, 'data' => $ProductsData, 'product_entry_count' => $product_entry_count);
                }else {
                    $returnData = array('status' => 1, 'msg' => "No record found.");
                }
            }
            return response()->json($returnData);
        }
    }
    // Order Preview
    public function order_preview(Request $request) {
        $warehouse = $request->warehouse;
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArrayWithAll($file, $warehouse, $supplier);
        return \View::make("backend/purchase_order/order_preview")->with(array('products'=>$productArr['data']));
    }
    function csvToArrayWithAll($filename = '', $warehouse, $supplier, $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $product_details=$this->get_product_details_by_partno($row[0], $warehouse, $supplier);
                    if(sizeof($product_details)>0 && $row[1]>0 && is_numeric($row[1])) {
                            $product_details['qty']=$row[1];
                            $sub_total+=($row[1]*$product_details['pmrprc']);
                            $total_gst=0;
                            $grand_total+=($row[1]*$product_details['pmrprc']);
                            $data[] = $product_details;
                    }else {
                        if(sizeof($product_details) >0 && is_numeric($row[1])) {
                            $data[] = array('product_id'=>$row[0],'qty'=>"Invalid quantity");
                        }else {
                            if(sizeof($product_details) >0 && $row[1] <= 0) {
                                $data[] = array('product_id'=>$row[0],'qty'=>0 . " Quantity sholud be atleast 1");
                            }else {
                                $data[] = array('product_id'=>$row[0],'qty'=>$row[1] ." This is wrong product. It will be skipped when upload.");
                            }
                        }
                    }
                }
            }
            fclose($handle);
        }
        return array('data'=>$data,'sub_total'=>$sub_total,'tax'=>$total_gst,'grand_total'=>$grand_total);
    }
    function get_product_details_by_partno($part_no, $warehouse, $supplier) {
        $data_array=[];
        $Products = Products::select('product_id', 'pmpno', 'pmrprc', 'ct', 'part_name_id')->where('pmpno', $part_no)->get()->toArray();
        if(sizeof($Products)>0) {
            $part_name = "";
            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
            if(!empty($PartName)) {
                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
            }
            $model = new DB;
            $category_name = "";
            $ProductCategories = ProductCategories::select('category_name')->where('category_id', $Products[0]['ct'])->get()->toArray();
            if(sizeof($ProductCategories) > 0) {
                if(!empty($ProductCategories[0]['category_name'])) $category_name = $ProductCategories[0]['category_name'];
            }
            $data_array=array('product_id'=>$Products[0]['product_id'],'part_no'=>$Products[0]['pmpno'],'part_name'=>$part_name,'ct'=>$Products[0]['ct'],'c_name'=>$category_name,'pmrprc'=>$Products[0]['pmrprc']);
        }
        return $data_array;
    }
    //
    public function create_multiple_order(Request $request){
        $returnData = [];
        $warehouse = $request->warehouse;
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArray($file, $warehouse, $supplier);
        if(sizeof($productArr['data'])>0) {
            $data = new Orders;
            $data->datetime = date('Y-m-d');
            $data->deliverydate = date('Y-m-d', strtotime(str_replace('/','-',$request->estimated_delivery_date)));
            $data->supplier_id = $request->supplier;
            $data->warehouse_id = $request->warehouse;
            $data->sub_total = $productArr['sub_total'];
            $data->total_tax = $productArr['tax'];
            $data->grand_total = $productArr['grand_total'];
            $data->approved = "0";
            $data->received = "0";
            $data->is_delete = "0";
            $saveData = $data->save();
            if($saveData) {
                $flag = 0;
                $last_id = $data->id;
                foreach($productArr['data'] as $product) {
                    $data2 = new OrderDetail;
                    $data2->order_id = $last_id;
                    $data2->warehouse_id = $request->warehouse;
                    $data2->product_id = $product['product_id'];
                    $data2->qty = $product['qty'];
                    $data2->mrp = $product['pmrprc'];
                    $save2nd = $data2->save();
                    $flag++;
                }
                if($flag == sizeof($productArr['data'])) {
                    $returnData = ["status" => 1, "msg" => "Order created successful."];
                }else {
                    $returnData = ["status" => 0, "msg" => "Order created! Something is wrong."];
                }
            }else {
                $returnData = ["status" => 0, "msg" => "Order created faild."];
            }
        }else {
            $returnData = ["status" => 0, "msg" => "Order created failed"];
        }
        return response()->json($returnData);

        //
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArray($file, $warehouse, $supplier);
        if(sizeof($productArr['data'])>0) {
            $order_data = array('client_id' => $request->client, 'sub_total' => $productArr['sub_total'], 'gst' => $productArr['tax'], 'grand_total' => $productArr['grand_total'], 'remarks'=>"", 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d'));
            $last_sale_order_id = DB::table('sale_order')->insertGetId($order_data);
            foreach($productArr['data'] as $product) {
                $product_id = $product['product_id'];
                $mrp = $product['pmrprc'];
                $qty = $product['qty'];
                $gst_array = 0;
                $product_tax = 0;
                $max_order_line_no=SaleOrderDetails::selectRaw('MAX(order_line_no) as olnm')->get();
                $order_details=array('sale_order_id'=>$last_sale_order_id,'order_line_no'=>($max_order_line_no[0]->olnm+1),'product_id'=>$product_id,'product_tax'=>$product_tax,'product_price'=>$mrp,'qty'=>$qty);
                SaleOrderDetails::insert($order_details);
            }
            if(!$last_sale_order_id) {
                $returnData = ["status" => 0, "msg" => "Sorry! There is an error"];
            }else {
                $returnData = ["status" => 1, "msg" => "Order is created successfully"];
            }
        }else {
            $returnData = ["status" => 0, "msg" => "Sorry! Order is failed"];
        }
        return response()->json($returnData);
    }
    function csvToArray($filename = '', $warehouse, $supplier, $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $product_details=$this->get_product_details_by_partno($row[0], $warehouse, $supplier);
                    if(sizeof($product_details)>0 && $row[1]>0 && is_numeric($row[1])) {
                        $product_details['qty']=$row[1];
                        $sub_total+=($row[1]*$product_details['pmrprc']);
                        $total_gst=0;
                        $grand_total+=($row[1]*$product_details['pmrprc']);
                        $data[] = $product_details;
                    }
                }
            }
            fclose($handle);
        }
        return array('data' => $data, 'sub_total' => $sub_total, 'tax' => $total_gst, 'grand_total' => $grand_total);
    }
    // Delete Order Details
    public function delete_order_details(Request $request) {
        $returnData = [];
        $upData = OrderDetail::where('order_detail_id', $request->id)->delete();
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    // 
    public function get_order_request_details(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $Orders = Orders::where([['order_request_unique_id', '=', $request->order_request_id]])->get()->toArray();
            if(sizeof($Orders) >0) {
                return response()->json(['status' => 0, 'msg' => "Order is already done."]);
            }else {
                $OrderRequest = OrderRequest::select('order_request_unique_id')->where([['order_request_unique_id', '=', $request->order_request_id], ['status', '=', '1']])->get()->toArray();
                if(sizeof($OrderRequest) > 0) {
                    //$OrderQuotation = OrderQuotation::where([['order_request_unique_id', '=', $request->order_request_id], ['is_confirm', '=', '1'], ['status', '=', '1']])->get()->toArray();
                    //if(sizeof($OrderQuotation) > 0) {
                        $supplier_id = "";
                        $OrderQuotation = OrderQuotation::select('supplier_id')->where([['order_request_unique_id', '=', $request->order_request_id], ['is_confirm', '=', '1']])->get()->toArray();
                        if(sizeof($OrderQuotation) > 0) {
                            if(!empty($OrderQuotation[0]['supplier_id'])) $supplier_id = $OrderQuotation[0]['supplier_id'];
                        }
                        $OrderRequestDetails = OrderRequestDetails::select('order_request_details_id', 'product_id', 'qty')->where([['order_request_unique_id', '=', $request->order_request_id]])->get()->toArray();
                        if(sizeof($OrderRequestDetails) > 0) {
                            foreach($OrderRequestDetails as $data) {
                                $pmpno = "";
                                $pmrprc = "";
                                $category_name = "";
                                $vat = 0;
                                $category_id = '';
                                $part_name = '';
                                $supplier_price = 0;
                                $OrderQuotation = OrderQuotation::select('supplier_id')->where([['order_request_unique_id', '=', $request->order_request_id], ['is_confirm', '=', '1'], ['status', '!=', '2']])->get()->toArray();
                                if(sizeof($OrderQuotation) > 0) {
                                    $OrderRequestQuotationPrices = OrderRequestQuotationPrices::select('price')->where([['supplier_id', '=', $OrderQuotation[0]['supplier_id']], ['order_request_unique_id', '=', $request->order_request_id], ['order_request_details_id', '=', $data['order_request_details_id']]])->get()->toArray();
                                    if(sizeof($OrderRequestQuotationPrices) > 0) {
                                        $supplier_price = $OrderRequestQuotationPrices[0]['price'];
                                    }
                                }
                                $Products = Products::select('pmpno', 'ct', 'pmrprc', 'part_name_id')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                                if(sizeof($Products) > 0) {
                                    if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                                    if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                                    if(!empty($Products[0]['ct'])) $category_id = $Products[0]['ct'];
                                    if(!empty($Products[0]['ct'])) {
                                        $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $Products[0]['ct']]])->get()->toArray();
                                        if(sizeof($ProductCategories) > 0) {
                                            $category_name = $ProductCategories[0]['category_name'];
                                        }
                                    }
                                    if(!empty($Products[0]['ct'])) {
                                        $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                                        if(sizeof($PartName) > 0) {
                                            $part_name = $PartName[0]['part_name'];
                                        }
                                    }
                                }
                                array_push($returnData, array('product_id' => $data['product_id'], 'supplier_price' => $supplier_price, 'pmpno' => $pmpno, 'pmrprc' => $pmrprc, 'category_id' => $category_id, 'category_name' => $category_name, 'part_name' => $part_name, 'vat' => $vat, 'qty' => $data['qty']));
                            }
                            return response()->json(['status' => 1, 'data' => $returnData, 'supplier_id' => $supplier_id]);
                        }else {
                            return response()->json(['status' => 0, 'msg' => "No record found."]);
                        }
                    //}else {
                        //return response()->json(['status' => 0, 'msg' => "Order is not confirmed yet."]);
                    //}
                }else {
                    return response()->json(['status' => 0, 'msg' => "No record found."]);
                }
            }
        }
    }
    // Upload Invoice
    public function upload_invoice(Request $request) {
        if ($request->ajax()) {
            $upimages = $request->invoice;
            $invoice_file = rand() . '.' . $upimages->getClientOriginalExtension();
            $upimages->move(public_path('backend/images/purchase_invoice/'), $invoice_file);
            $upData = Orders::where([['order_id', '=', $request->hidden_order_id]])->update(['invoice' => $invoice_file, 'invoice_no' => $request->invoice_no]);
            if($upData) {
                $invoice_extention = substr($invoice_file, strrpos($invoice_file, '.' )+1);
                $url = url('public/backend/images/purchase_invoice/');
                $invoice_file = $url."/".$invoice_file;
                return response()->json(["status" => 1, "msg" => "Upload successful.", 'invoice_extention' => $invoice_extention, 'invoice_file' => $invoice_file]);
            }else {
                return response()->json(["status" => 0, "msg" => "Upload faild!"]);
            }
        }
    }
    // Get order form Cart
    public function list_order_from_cart(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $cart_data = $request->cookie('cart_data');
            if(!empty($cart_data)) {
                $data=json_decode($cart_data,true);
                if(sizeof($data)>0) {
                    foreach($data as $key=>$val) {
                        $pmpno = "";
                        $part_name = "";
                        $category_id = "";
                        $category_name = "";
                        $mrp = "";
                        $Products = Products::select('pmpno', 'part_name_id', 'ct', 'pmrprc')->where([['product_id', '=', $val['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                        if(!empty($Products)) {
                            if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                            if(!empty($Products[0]['ct'])) $category_id = $Products[0]['ct'];
                            if(!empty($Products[0]['pmrprc'])) $mrp = $Products[0]['pmrprc'];
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']], ['status', '=', '1']])->get()->toArray();
                            if(!empty($PartName)) {
                                if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                            }
                            $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $Products[0]['ct']], ['status', '=', '0']])->get()->toArray();
                            if(!empty($ProductCategories)) {
                                if(!empty($ProductCategories[0]['category_name'])) $category_name = $ProductCategories[0]['category_name'];
                            }
                        }
                        array_push($returnData, array('product_id' => $val['product_id'], 'pmpno' => $pmpno, 'part_name' => $part_name, 'category_id' => $category_id, 'category_name' => $category_name, 'vat' => 0, 'mrp' => $mrp, 'qty' => $val['qty']));
                    }
                    return response()->json(["status" => 1, "data" => $returnData, 'data_size' => sizeof($data)]);
                }else {
                    return response()->json(["status" => 0, "msg" => "No data in Cart!"]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "No data in Cart!"]);
            }
        }
    }
    // Save Order
    public function save_purchase_order() {
        return \View::make("backend/purchase_order/save_purchase_order")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    public function list_save_purchase_order_management(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1']]);
            if($keyword)
            {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            $query->where([['o.orders_status', '=', 2]]);
            //$query->where([['status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->datetime)) {
                    $order_date = date('d M Y', strtotime($query->datetime));
                }
                return $order_date;
            })
            ->addColumn('deliverydate', function ($query) {
                $delivery_date = '';
                if(!empty($query->deliverydate)) {
                    $delivery_date = date('d M Y', strtotime($query->deliverydate));
                }
                return $delivery_date;
            })
            ->addColumn('item', function ($query) {
                $selectQty = OrderDetail::where('order_id',$query->order_id)->sum('qty');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-order-details" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('approved_status', function ($query) {
                $approved_status = '';
                if($query->approved == '1') {
                    $approved_status = '<span class="badge badge-success">Approved</span>';
                }else {
                    $approved_status = '<span class="badge badge-danger">Not Approved</span>';
                }
                return $approved_status;
            })
            ->addColumn('received_status', function ($query) {
                $received_status = '';
                if($query->received == '1') {
                    $received_status = '<span class="badge badge-success">Received</span>';
                }else {
                    $received_status = '<span class="badge badge-danger">Not Received</span>';
                }
                return $received_status;
            })
            ->addColumn('action', function ($query) {
                $action = '';
                $action .= '<a href="javascript:void(0)" class="edit-purchase-order" data-id="'.$query->order_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-pencil"></i></button></a>';
                $delete = ' <a href="javascript:void(0)" class="delete-purchase-order" data-id="'.$query->order_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
                if($query->invoice == "") {
                    $action .= $delete;
                }
                return $action;
            })
            ->rawColumns(['details', 'approved_status', 'received_status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    // export
    public function save_purchase_order_export() {
        $query = DB::table('orders as o')
        ->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier')
        ->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id')
        ->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id')
        ->where([['o.is_delete', '!=', '1']])
        ->where([['o.orders_status', '=', 2]])
        ->orderBy('o.order_id', 'DESC');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order_id');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Warehouse');
        $sheet->setCellValue('D1', 'Supplier');
        $sheet->setCellValue('E1', 'Delivery_date');
        $sheet->setCellValue('F1', 'Items_qty');
        

        $rows = 2;
        foreach($data as $empDetails){
            $order_date = '';
            if(!empty($empDetails->datetime)) {
                $order_date = date('d M Y', strtotime($empDetails->datetime));
            }
            $delivery_date = '';
            if(!empty($empDetails->deliverydate)) {
                $delivery_date = date('d M Y', strtotime($empDetails->deliverydate));
            }

            $item = '';
            if (!empty($empDetails->order_id)) {
                $item = OrderDetail::where('order_id',$empDetails->order_id)->sum('qty');
            }
            // ->addColumn('item', function ($query) {
            //     $selectQty = OrderDetail::where('order_id',$query->order_id)->sum('qty');
            //     return $selectQty;
            // })

            $sheet->setCellValue('A' . $rows, $empDetails->order_id);
            $sheet->setCellValue('B' . $rows, $order_date);
            $sheet->setCellValue('C' . $rows, $empDetails->warehouse_name);
            $sheet->setCellValue('D' . $rows, $empDetails->supplier);
            $sheet->setCellValue('E' . $rows, $delivery_date);
            $sheet->setCellValue('F' . $rows, $item);
            
            // $sheet->setCellValue('G' . $rows, $empDetails->current_stock);
            // $sheet->setCellValue('H' . $rows, $empDetails->pmrprc);
            $rows++;
        }
        $fileName = "Save_Purchase_Order.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    // Excess Order
    public function excess_purchase_order() {
        return \View::make("backend/purchase_order/excess_purchase_order")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    public function list_excess_purchase_order(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1']]);
            $query->whereIn('o.order_id', [DB::raw("SELECT order_id FROM `check_in_details` WHERE `excess_quantity` > 0 GROUP BY order_id")]);
            if($keyword)
            {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->datetime)) {
                    $order_date = date('d M Y', strtotime($query->datetime));
                }
                return $order_date;
            })
            ->addColumn('deliverydate', function ($query) {
                $delivery_date = '';
                if(!empty($query->deliverydate)) {
                    $delivery_date = date('d M Y', strtotime($query->deliverydate));
                }
                return $delivery_date;
            })
            ->addColumn('excess_quantity', function ($query) {
                $selectQty = CheckInDetails::where([['order_id', '=',$query->order_id], ['excess_quantity', '>', 0]])->sum('excess_quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-excess-order-details" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->rawColumns(['details'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function view_excess_purchase_order_details(Request $request){
        if ($request->ajax()) {
            $query = DB::table('check_in_details as cd');
            $query->join('products as p', 'p.product_id', '=', 'cd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('cd.order_id','cd.quantity', 'cd.excess_quantity', 'pn.part_name', 'p.pmpno');
            $query->where([['cd.order_id', '=', $request->id], ['excess_quantity', '>', 0]]);
            $orderDetails = $query->get()->toArray();
            $html = view('backend.purchase_order.excess_purchase_order_details')->with([
                'order_data' => $orderDetails,
                'order_id' => $request->id
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Damage Order
    public function damage_purchase_order() {
        return \View::make("backend/purchase_order/damage_purchase_order")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    public function list_damage_purchase_order(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1']]);
            $query->whereIn('o.order_id', [DB::raw("SELECT order_id FROM `check_in_details` WHERE `bad_quantity` > 0 GROUP BY order_id")]);
            if($keyword)
            {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->datetime)) {
                    $order_date = date('d M Y', strtotime($query->datetime));
                }
                return $order_date;
            })
            ->addColumn('deliverydate', function ($query) {
                $delivery_date = '';
                if(!empty($query->deliverydate)) {
                    $delivery_date = date('d M Y', strtotime($query->deliverydate));
                }
                return $delivery_date;
            })
            ->addColumn('bad_quantity', function ($query) {
                $selectQty = CheckInDetails::where([['order_id', '=',$query->order_id], ['bad_quantity', '>', 0]])->sum('bad_quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-damage-order-details" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->rawColumns(['details'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function view_damage_purchase_order_details(Request $request){
        if ($request->ajax()) {
            $query = DB::table('check_in_details as cd');
            $query->join('products as p', 'p.product_id', '=', 'cd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('cd.order_id','cd.quantity', 'cd.bad_quantity', 'pn.part_name', 'p.pmpno');
            $query->where([['cd.order_id', '=', $request->id], ['bad_quantity', '>', 0]]);
            $orderDetails = $query->get()->toArray();
            $html = view('backend.purchase_order.damage_purchase_order_details')->with([
                'order_data' => $orderDetails,
                'order_id' => $request->id
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Shortage Order
    public function shortage_purchase_order() {
        return \View::make("backend/purchase_order/shortage_purchase_order")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    public function list_shortage_purchase_order(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('orders as o');
            $query->join('warehouses as w', 'w.warehouse_id', '=', 'o.warehouse_id');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id');
            $query->select('o.order_id','o.datetime', 'o.deliverydate','o.supplier_id','o.warehouse_id','o.approved', 'o.received', 'o.invoice', 'w.name as warehouse_name', 's.full_name as supplier');
            $query->where([['o.is_delete', '!=', '1']]);
            $query->whereIn('o.order_id', [DB::raw("SELECT order_id FROM `check_in_details` WHERE `shortage_quantity` > 0 GROUP BY order_id")]);
            if($keyword)
            {
                $query->whereRaw("(w.name like '%$keyword%' or o.order_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('w.name', 'asc');
                else
                    $query->orderBy('w.name', 'desc');
            }
            else
            {
                $query->orderBy('o.order_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->datetime)) {
                    $order_date = date('d M Y', strtotime($query->datetime));
                }
                return $order_date;
            })
            ->addColumn('deliverydate', function ($query) {
                $delivery_date = '';
                if(!empty($query->deliverydate)) {
                    $delivery_date = date('d M Y', strtotime($query->deliverydate));
                }
                return $delivery_date;
            })
            ->addColumn('shortage_quantity', function ($query) {
                $selectQty = CheckInDetails::where([['order_id', '=',$query->order_id], ['shortage_quantity', '>', 0]])->sum('shortage_quantity');
                return $selectQty;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-shortage-order-details" data-id="'.$query->order_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->rawColumns(['details'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function view_shortage_purchase_order_details(Request $request){
        if ($request->ajax()) {
            $query = DB::table('check_in_details as cd');
            $query->join('products as p', 'p.product_id', '=', 'cd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->select('cd.order_id','cd.quantity', 'cd.shortage_quantity', 'pn.part_name', 'p.pmpno');
            $query->where([['cd.order_id', '=', $request->id], ['shortage_quantity', '>', 0]]);
            $orderDetails = $query->get()->toArray();
            $html = view('backend.purchase_order.shortage_purchase_order_details')->with([
                'order_data' => $orderDetails,
                'order_id' => $request->id
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    
    // Outstanding
    public function purchase_order_outstanding() {

        return \View::make("backend/purchase_order/purchase_order_outstanding")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    
    public function list_purchase_order_outstanding(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('consignment_receipt as cr');
            $query->select('cr.order_id', 'o.invoice_no', 'o.supplier_id', 'o.deliverydate', 'o.vat_type_id', 's.full_name as supplier_name');
            $query->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
            //$query->groupBy('invoice_no', 'client_id');
            $query->where([['cr.status', '!=', '2']]);
            
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            if($keyword)
            {
                $query->whereRaw("(o.invoice_no like '%$keyword%' or s.full_name like '%$keyword%' or cr.order_id like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('cr.order_id', 'asc');
                else
                    $query->orderBy('cr.order_id', 'desc');
            }
            else
            {
                $query->orderBy('cr.order_id', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                ->addColumn('grand_total', function ($query) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = Orders::select('total_tax')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    
                    $grand_total = round($grand_total,3);
                    return $grand_total;
                })
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->deliverydate)) {
                        
                        $date_of_invoice = date('Y-m-d', strtotime($query->deliverydate));
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = orders::select('total_tax')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    $grand_total = round($grand_total,3);
                    $due_amount = $grand_total;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        
                        $payamount = round($purchasePayAmount,3);
                        $due_amount = $grand_total - $payamount;
                        //$due_amount = $payamount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    // $grand_total = 0;
                    // $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $query->order_id]])->get()->toArray();
                    // if(sizeof($selectConsignmentReceipt) > 0) {
                        
                    //     foreach($selectConsignmentReceipt as $crdata) {
                            
                    //         $quantity = $crdata['quantity'];
                    //         $price = 0;
                    //         $selectProducts = Products::select('pmrprc')->where([['product_id', '=', $crdata['product_id']]])->get()->toArray();
                    //         if(sizeof($selectProducts) > 0) {
                    //             $price = $selectProducts[0]['pmrprc'];
                    //         }
                            
                    //         $grand_total = $grand_total + ($quantity * $price);
                    //     }
                    // }
                    
                    // $grand_total = round($grand_total,3);
                    // $due_amount = $grand_total;
                    // $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    // if(!empty($purchasePayAmount)) {
                        
                    //     $payamount = round($purchasePayAmount,3);
                    //     $due_amount = $grand_total - $payamount;
                        
                    //     if($due_amount > 0) {
                    //         $status = '<span class="badge badge-warning">Partial</span>';
                    //     }else {
                    //         $status = '<span class="badge badge-success">Paid</span>';
                    //     }
                    // }
                    
                    // return $status;
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = Orders::select('total_tax')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $query->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $query->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($query->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $query->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    
                    $grand_total = round($grand_total,3);
                    $due_amount = $grand_total;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        
                        $payamount = round($purchasePayAmount,3);
                        $due_amount = $grand_total - $payamount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }else {
                            $status = '<span class="badge badge-success">Paid</span>';
                        }
                    }
                    return $status;
                })
                ->rawColumns(['client_name', 'status'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_purchase_order_outstanding(Request $request) {
        return \View::make("backend/purchase_order/purchase_order_outstanding_form")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ])->render();
    }
    
    public function save_purchase_order_outstanding(Request $request) {
        
        $arrayData = [];
        
        if(!empty($request->order_id)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->order_id); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new PurchaseReceipt;
                    $data->order_id = $request->order_id[$i];
                    $data->supplier_id = $request->supplier_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->pay_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->payment_status = 'outstanding';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
        // echo "<pre>";
        // print_r($arrayData);
        // exit();
    }
    
    public function get_supplier_invoice_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            $PurchaseOrderData = DB::table('consignment_receipt as cr')->select('cr.order_id', 'o.invoice_no', 'o.deliverydate', 'o.vat_type_id')->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left')->where([['o.supplier_id', '=', $request->supplier_id], ['cr.status', '!=', '2']])->get()->toArray();
            
            //$PurchaseOrderData = Orders::select('order_id', 'invoice_no', 'deliverydate', 'grand_total')->where([['supplier_id', '=', $request->supplier_id], ['orders_status', '=', '1'], ['is_delete', '=', '0']])->get()->toArray();
            
            if(sizeof($PurchaseOrderData) > 0) {
                
                foreach($PurchaseOrderData as $data) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = orders::select('total_tax')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    
                    $grand_total = round($grand_total,3);
                    $dueAmount = $grand_total;
                    $paymentStatus = "due";
                    
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $data->order_id]])->sum('pay_amount');
                    
                    if(!empty($purchasePayAmount) > 0) {
                        
                        $dueAmount = $grand_total - $purchasePayAmount;
                        $dueAmount = round($dueAmount, 3);
                        
                        $totalPay = round($purchasePayAmount, 3);
                        if($grand_total == $totalPay) {
                            
                            $paymentStatus = 'paid';
                        }
                    }
                    $invoice_no = "";
                    if(!empty($data->invoice_no)) {
                        $invoice_date = $data->invoice_no;
                    }
                    
                    $invoice_date = "";
                    if(!empty($data->deliverydate)) {
                        $invoice_date = date('Y-m-d', strtotime($data->deliverydate));
                    }
                    if($paymentStatus  == 'due') {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('order_id' => $data->order_id, 'invoice_no' => $data->order_id, 'invoice_date' => $invoice_date, 'grand_total' => $grand_total, 'due_amount' => $dueAmount));
                    }
                }
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function purchase_order_outstanding_export(Request $request)
    {   
        
        $query = DB::table('orders as o');
        $query->select('o.order_id', 'o.invoice_no', 'o.supplier_id', 'o.deliverydate', 'o.grand_total', 's.full_name as supplier_name');
        $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
        if(!empty($request->filter_supplier)) {
            $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
        }
        $query->where([['orders_status', '=', '1'], ['o.is_delete', '=', '0']]);
        $query->orderBy('o.order_id', 'desc');
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Supplier Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Amount');
        $sheet->setCellValue('D1', 'Invoice Number');
        $sheet->setCellValue('E1', 'Due Amount');
        $sheet->setCellValue('F1', 'Status');
        
        $rows = 2;
        foreach($data as $td){
            
            $invoiceDate = date('Y-m-d');
            if(!empty($td->deliverydate)) {
                
                $invoiceDate = $td->deliverydate;
            }
            
            $due_amount = $td->grand_total;
            $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $td->order_id]])->sum('pay_amount');
            if(!empty($purchasePayAmount)) {
                
                $payamount = round($purchasePayAmount,3);
                $due_amount = $td->grand_total - $payamount;
            }
            
            $status = 'Due';
            $due_amount = $td->grand_total;
            $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $td->order_id]])->sum('pay_amount');
            if(!empty($purchasePayAmount)) {
                
                $payamount = round($purchasePayAmount,3);
                $due_amount = $td->grand_total - $payamount;
                
                if($due_amount > 0) {
                    $status = 'Partial';
                }else {
                    $status = 'Paid';
                }
            }
            $sheet->setCellValue('A' . $rows, $td->supplier_name);
            $sheet->setCellValue('B' . $rows, $invoiceDate);
            $sheet->setCellValue('C' . $rows, $td->grand_total);
            $sheet->setCellValue('D' . $rows, $td->order_id);
            $sheet->setCellValue('E' . $rows, $due_amount);
            $sheet->setCellValue('F' . $rows, $status);
            $rows++;
        }
        $fileName = "Purchase-Order-Outstanding.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Partial Outstanding
    public function purchase_partial_outstanding() {

        return \View::make("backend/purchase_order/purchase_partial_outstanding")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    
    public function list_purchase_partial_outstanding(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('purchase_receipt as p');
            $query->select('p.order_id', 'p.invoice_number', 'p.supplier_id', 'p.invoice_date', 'p.invoice_amount', 's.full_name as supplier_name');
            $query->join('suppliers as s', 's.supplier_id', '=', 'p.supplier_id', 'left');
            //$query->where('p.payment_status', 'partial');
            if(!empty($request->filter_supplier)) {
                $query->where([['p.supplier_id', '=', $request->filter_supplier]]);
            }
            if($keyword)
            {
                $query->whereRaw("(p.invoice_number like '%$keyword%' or s.full_name like '%$keyword%' or p.order_id like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('p.invoice_date', 'asc');
                else
                    $query->orderBy('p.invoice_date', 'desc');
            }
            else
            {
                $query->orderBy('p.invoice_date', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->invoice_date)) {
                        
                        $date_of_invoice = $query->invoice_date;
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $due_amount = $query->invoice_amount;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        
                        $payamount = round($purchasePayAmount,3);
                        $due_amount = $query->invoice_amount - $payamount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    $due_amount = $query->invoice_amount;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        
                        $payamount = round($purchasePayAmount,3);
                        $due_amount = $query->invoice_amount - $payamount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }else {
                            $status = '<span class="badge badge-success">Paid</span>';
                        }
                    }
                    
                    return $status;
                })
                ->rawColumns(['client_name', 'status'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_purchase_partial_outstanding(Request $request) {
        return \View::make("backend/purchase_order/purchase_partial_outstanding_form")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ])->render();
    }
    
    public function save_purchase_partial_outstanding(Request $request) {
        
        $arrayData = [];
        if(!empty($request->invoice_no)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->order_id); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new PurchaseReceipt;
                    $data->order_id = $request->order_id[$i];
                    $data->supplier_id = $request->supplier_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->pay_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->payment_status = 'partial';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
    }
    
    public function get_supplier_partial_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            //$purchaseOrderData = Orders::select('order_id', 'invoice_no', 'deliverydate', 'grand_total')->where([['supplier_id', '=', $request->supplier_id], ['orders_status', '=', '1'], ['is_delete', '=', '0']])->get()->toArray();
            
            $purchaseOrderData = DB::table('consignment_receipt as cr')->select('cr.order_id', 'o.invoice_no', 'o.deliverydate', 'o.vat_type_id')->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left')->where([['o.supplier_id', '=', $request->supplier_id], ['cr.status', '!=', '2']])->get()->toArray();
            
            if(sizeof($purchaseOrderData) > 0) {
                
                foreach($purchaseOrderData as $data) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = orders::select('total_tax')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    $grand_total = round($grand_total,3);
                    $dueAmount = $grand_total;
                    
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $data->order_id]])->sum('pay_amount');
                    
                    if(!empty($purchasePayAmount) > 0) {
                        
                        $dueAmount = $grand_total - $purchasePayAmount;
                        $dueAmount = round($dueAmount, 3);
                    }
                    
                    $invoice_date = ""; 
                    
                    $invoice_date = "";
                    if(!empty($data->deliverydate)) {
                        $invoice_date = date('Y-m-d', strtotime($data->deliverydate));
                    }
                    if($dueAmount > 0) {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('order_id' => $data->order_id, 'invoice_no' => $data->order_id, 'invoice_date' => $invoice_date, 'grand_total' => $grand_total, 'due_amount' => $dueAmount));
                    }
                }
                
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function purchase_partial_outstanding_export(Request $request)
    {   
        
        $query = DB::table('purchase_receipt as p');
        $query->select('p.order_id', 'p.invoice_number', 'p.supplier_id', 'p.invoice_date', 'p.invoice_amount', 's.full_name as supplier_name');
        $query->join('suppliers as s', 's.supplier_id', '=', 'p.supplier_id', 'left');
        $query->where('p.payment_status', 'partial');
        if(!empty($request->filter_supplier)) {
            $query->where([['p.supplier_id', '=', $request->filter_supplier]]);
        }
        $data = $query->get()->toArray();
        // print_r($data); exit();    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Supplier Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Amount');
        $sheet->setCellValue('D1', 'Invoice Number');
        $sheet->setCellValue('E1', 'Due Amount');
        $sheet->setCellValue('F1', 'Status');
        
        $rows = 2;
        foreach($data as $td){
            
            $invoiceDate = date('Y-m-d');
            if(!empty($td->deliverydate)) {
                
                $invoiceDate = $td->deliverydate;
            }
            
            $due_amount = $td->invoice_amount;
            $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $td->order_id]])->sum('pay_amount');
            if(!empty($purchasePayAmount)) {
                
                $payamount = round($purchasePayAmount,3);
                $due_amount = $td->invoice_amount - $payamount;
            }
            
            $status = 'Due';
            $due_amount = $td->invoice_amount;
            $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $td->order_id]])->sum('pay_amount');
            if(!empty($purchasePayAmount)) {
                
                $payamount = round($purchasePayAmount,3);
                $due_amount = $td->invoice_amount - $payamount;
                
                if($due_amount > 0) {
                    $status = 'Partial';
                }else {
                    $status = 'Paid';
                }
            }
            $sheet->setCellValue('A' . $rows, $td->supplier_name);
            $sheet->setCellValue('B' . $rows, $invoiceDate);
            $sheet->setCellValue('C' . $rows, $td->invoice_amount);
            $sheet->setCellValue('D' . $rows, $td->invoice_number);
            $sheet->setCellValue('E' . $rows, $due_amount);
            $sheet->setCellValue('F' . $rows, $status);
            $rows++;
        }
        $fileName = "Purchase-Order-Partial -Outstanding.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Receipt
    public function purchase_receipt() {

        return \View::make("backend/purchase_order/purchase_receipt")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    
    public function list_purchase_receipt(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('purchase_receipt as pr');
            $query->select('pr.order_id', 'pr.invoice_number', 'pr.supplier_id', 'pr.invoice_date', 'pr.invoice_amount', 'pr.pay_amount', 's.full_name as supplier_name');
            $query->join('suppliers as s', 's.supplier_id', '=', 'pr.supplier_id', 'left');
            if(!empty($request->filter_supplier)) {
                $query->where([['pr.supplier_id', '=', $request->filter_supplier]]);
            }
            if($keyword)
            {
                $query->whereRaw("(pr.invoice_number like '%$keyword%' or s.full_name like '%$keyword%' or pr.order_id like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('pr.invoice_date', 'asc');
                else
                    $query->orderBy('pr.invoice_date', 'desc');
            }
            else
            {
                $query->orderBy('pr.invoice_date', 'DESC');
            }
            $query->get();
            $datatable_array=Datatables::of($query)
                
                // ->addColumn('client_name', function ($query) {
                //     $customer_name = '';
                //     if(!empty($query->client_id)) {
                //         $selectClient = Clients::select('customer_name')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                //         if(sizeof($selectClient) > 0) {
                //             $customer_name = $selectClient[0]['customer_name'];
                //         }
                //     }
                //     return $customer_name;
                // })
                ->addColumn('date_of_invoice', function ($query) {
                    
                    $date_of_invoice = date('Y-m-d');
                    
                    if(!empty($query->invoice_date)) {
                        
                        $date_of_invoice = $query->invoice_date;
                    }
                    return $date_of_invoice;
                })
                ->addColumn('due_amount', function ($query) {
                    
                    $due_amount = $query->invoice_amount;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        $due_amount = $query->invoice_amount - $purchasePayAmount;
                    }
                    return $due_amount;
                })
                ->addColumn('status', function ($query) {
                    
                    $status = '<span class="badge badge-danger">Due</span>';
                    $due_amount = $query->invoice_amount;
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $query->order_id]])->sum('pay_amount');
                    if(!empty($purchasePayAmount)) {
                        $due_amount = $query->invoice_amount - $purchasePayAmount;
                        
                        if($due_amount > 0) {
                            $status = '<span class="badge badge-warning">Partial</span>';
                        }
                    }
                    
                    return $status;
                })
                ->rawColumns(['client_name', 'status'])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    public function add_purchase_receipt(Request $request)
    {
        
        return \View::make("backend/purchase_order/purchase_receipt_payment_form")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ])->render();
    }
    
    public function get_supplier_receipt_details(Request $request) {
        
        if ($request->ajax()) {
            
            $returnData = [];
            $totalDueAmunt = 0;
            //$SaleOrderData = Orders::select('order_id', 'invoice_no', 'deliverydate', 'grand_total')->where([['supplier_id', '=', $request->supplier_id], ['is_delete', '=', '0'], ['orders_status', '=', '1']])->get()->toArray();
            $purchaseOrderData = DB::table('consignment_receipt as cr')->select('cr.order_id', 'o.invoice_no', 'o.deliverydate', 'o.vat_type_id')->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left')->where([['o.supplier_id', '=', $request->supplier_id], ['cr.status', '!=', '2']])->get()->toArray();
            
            if(sizeof($purchaseOrderData) > 0) {
                
                foreach($purchaseOrderData as $data) {
                    
                    $grand_total = 0;
                    $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectConsignmentReceipt) > 0) {
                        
                        foreach($selectConsignmentReceipt as $crdata) {
                            
                            $quantity = $crdata['quantity'];
                            $price = 0;
                            $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                            if(sizeof($selectProductMrp) > 0) {
                                $price = $selectProductMrp[0]['mrp'];
                            }
                            
                            $grand_total = $grand_total + ($quantity * $price);
                        }
                    }
                    
                    $vatAmount = 0;
                    $selectVat = orders::select('total_tax')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectVat) > 0)
                    {
                        if(!empty($selectVat[0]['total_tax']))
                        {
                            $vatAmount = $selectVat[0]['total_tax'];
                        }
                    }
                    $grand_total = $grand_total + $vatAmount;
                    
                    // If quantity return it will reduce form invoice.
                    $returnPrice = 0;
                    $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $data->order_id]])->get()->toArray();
                    if(sizeof($selectReturns) > 0)
                    {
                        foreach($selectReturns as $rddata)
                        {
                            $qty = $rddata['return_quantity'];
                            $returnProductPrice = 0;
                            
                            $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $data->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                            if(sizeof($selectOrderDetails) > 0)
                            {
                                $returnProductPrice = $selectOrderDetails[0]['mrp'];
                            }
                            $qty *
                            $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                        }
                    }
                    
                    $returnVatPrice = 0;
                    if(!empty($data->vat_type_id))
                    {
                        $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $data->vat_type_id]])->get()->toArray();
                        if(sizeof($selectVatDetails) > 0)
                        {
                            $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                        }
                    }
                    $returnPrice = $returnPrice + $returnVatPrice;
                    $returnPrice = round($returnPrice, 3);
                    
                    $grand_total = $grand_total - $returnPrice;
                    $grand_total = round($grand_total,3);
                    $dueAmount = $grand_total;
                    $paymentStatus = 'due';
                    
                    $purchasePayAmount = PurchaseReceipt::where([['order_id', '=', $data->order_id]])->sum('pay_amount');
                    
                    if(!empty($purchasePayAmount) > 0) {
                        
                        $dueAmount = $grand_total - $purchasePayAmount;
                        $dueAmount = round($dueAmount, 3);
                        
                        $totalPay = round($purchasePayAmount, 3);
                        if($grand_total == $totalPay) {
                            
                            $paymentStatus = 'paid';
                        }
                    }
                    
                    $invoice_date = "";
                    if(!empty($data->deliverydate)) {
                        $invoice_date = date('Y-m-d', strtotime($data->deliverydate));
                    }
                    
                    if($paymentStatus == 'due') {
                        
                        $totalDueAmunt +=$dueAmount;
                        array_push($returnData, array('order_id' => $data->order_id, 'invoice_no' => $data->order_id, 'invoice_date' => $invoice_date, 'grand_total' => $grand_total, 'due_amount' => $dueAmount));
                    }
                }
                
                $totalDueAmunt = round($totalDueAmunt, 3);
                return response()->json(["status" => 1, "data" => $returnData, 'totalDueAmunt' => $totalDueAmunt]);
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    
    public function save_purchase_receipt(Request $request) {
        
        $arrayData = [];
        if(!empty($request->order_id)) {
            
            $flag = 0;
            for($i=0; $i<sizeof($request->order_id); $i++) {
                
                if($request->pay[$i] > 0) {
                    
                    $data = new PurchaseReceipt;
                    $data->order_id = $request->order_id[$i];
                    $data->supplier_id = $request->supplier_id;
                    $data->invoice_date = $request->invoice_date[$i];
                    $data->invoice_number = $request->invoice_no[$i];
                    $data->invoice_amount = $request->invoice_amount[$i];
                    $data->due_amount = $request->due_amount[$i];
                    $data->pay_amount = $request->pay[$i];
                    $data->pay_mode = $request->pay_mode;
                    $data->reference_number = $request->reference_number;
                    $data->payment_date = $request->payment_date;
                    $data->remarks = $request->remarks;
                    $data->payment_status = 'receipt';
                    $data->save();
                    //array_push($arrayData, ['invoice_no' => $request->invoice_no[$i]]);
                }
                $flag++;
            }
            
            if($flag == sizeof($request->invoice_no)) {
                
                $returnData = ["status" => 1, "msg" => "Save successful."];
            }else {
                
                $returnData = ["status" => 0, "msg" => "Save failed! Something is wrong."];
            }
        }
        return response()->json($returnData);
        // echo "<pre>";
        // print_r($arrayData);
        // exit();
    }
    
    public function purchase_receipt_export(Request $request)
    {   
        
        $query = DB::table('purchase_receipt as pr');
        $query->select('pr.order_id', 'pr.invoice_number', 'pr.supplier_id', 'pr.invoice_date', 'pr.invoice_amount', 'pr.pay_amount', 's.full_name as supplier_name');
        $query->join('suppliers as s', 's.supplier_id', '=', 'pr.supplier_id', 'left');
        if(!empty($request->filter_supplier)) {
            $query->where([['pr.supplier_id', '=', $request->filter_supplier]]);
        }
        $query->orderBy('pr.order_id', 'desc');
        $data = $query->get()->toArray();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Supplier Name');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'Invoice Number');
        $sheet->setCellValue('D1', 'Invoice Amount');
        $sheet->setCellValue('E1', 'Pay');
        
        $rows = 2;
        foreach($data as $td){
            
            $sheet->setCellValue('A' . $rows, $td->supplier_name);
            $sheet->setCellValue('B' . $rows, $td->invoice_date);
            $sheet->setCellValue('C' . $rows, $td->invoice_number);
            $sheet->setCellValue('D' . $rows, $td->invoice_amount);
            $sheet->setCellValue('E' . $rows, $td->pay_amount);
            $rows++;
        }
        $fileName = "Purchase-Order-Receipt.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    
    // Purchase Report
    public function purchase_order_purchase_report() {

        return \View::make("backend/purchase_order/purchase_order_purchase_report")->with([
            'SupplierData' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
        ]);
    }
    
    public function list_purchase_order_purchase_report(Request $request){
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('consignment_receipt as cr');
            $query->select('o.supplier_id', 's.full_name');
            $query->join('orders as o', 'o.order_id', '=', 'cr.order_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'o.supplier_id', 'left');
            //$query->groupBy('invoice_no', 'client_id');
            $query->where([['cr.status', '!=', '2']]);
            
            if(!empty($request->filter_supplier)) {
                $query->where([['o.supplier_id', '=', $request->filter_supplier]]);
            }
            if($keyword)
            {
                $query->whereRaw("(o.supplier_id like '%$keyword%' or s.full_name like '%$keyword%')");
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.full_name', 'asc');
                else
                    $query->orderBy('s.full_name', 'asc');
            }
            else
            {
                $query->orderBy('s.full_name', 'asc');
            }
            $query->groupBy('o.supplier_id');
            $datatable_array=Datatables::of($query)
                
                
                ->addColumn('outstanding_amount', function ($query) {
                    
                    $outstanding_amount = 0;
                    
                    $selectOrder = DB::table('orders as o')->select('o.order_id', 'o.vat_type_id')->join('consignment_receipt as cr', 'o.order_id', '=', 'cr.order_id')->where([['o.supplier_id', '=', $query->supplier_id]])->get()->toArray();
                    if(sizeof($selectOrder) > 0)
                    {
                        foreach($selectOrder as $oData)
                        {
                            $grand_total = 0;
                            $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectConsignmentReceipt) > 0) {
                                
                                foreach($selectConsignmentReceipt as $crdata) {
                                    
                                    $quantity = $crdata['quantity'];
                                    $price = 0;
                                    $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $oData->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                                    if(sizeof($selectProductMrp) > 0) {
                                        $price = $selectProductMrp[0]['mrp'];
                                    }
                                    
                                    $grand_total = $grand_total + ($quantity * $price);
                                }
                            }
                            
                            $vatAmount = 0;
                            $selectVat = Orders::select('total_tax')->where([['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectVat) > 0)
                            {
                                if(!empty($selectVat[0]['total_tax']))
                                {
                                    $vatAmount = $selectVat[0]['total_tax'];
                                }
                            }
                            $grand_total = $grand_total + $vatAmount;
                            
                            // If quantity return it will reduce form invoice.
                            $returnPrice = 0;
                            $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectReturns) > 0)
                            {
                                foreach($selectReturns as $rddata)
                                {
                                    $qty = $rddata['return_quantity'];
                                    $returnProductPrice = 0;
                                    
                                    $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $oData->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                    if(sizeof($selectOrderDetails) > 0)
                                    {
                                        $returnProductPrice = $selectOrderDetails[0]['mrp'];
                                    }
                                    $qty *
                                    $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                                }
                            }
                            
                            $returnVatPrice = 0;
                            if(!empty($oData->vat_type_id))
                            {
                                $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $oData->vat_type_id]])->get()->toArray();
                                if(sizeof($selectVatDetails) > 0)
                                {
                                    $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                                }
                            }
                            $returnPrice = $returnPrice + $returnVatPrice;
                            $returnPrice = round($returnPrice, 3);
                            
                            $grand_total = $grand_total - $returnPrice;
                            
                            $grand_total = round($grand_total,3);
                            $outstanding_amount = $outstanding_amount + $grand_total;
                        }
                    }
                    
                    
                    return $outstanding_amount;
                })
                ->addColumn('partial_amount', function ($query) {
                    
                    $outstanding_amount = 0;
                    $partial_amount = 0;
                    
                    $selectOrder = DB::table('orders as o')->select('o.order_id', 'o.vat_type_id')->join('consignment_receipt as cr', 'o.order_id', '=', 'cr.order_id')->where([['o.supplier_id', '=', $query->supplier_id]])->get()->toArray();
                    if(sizeof($selectOrder) > 0)
                    {
                        foreach($selectOrder as $oData)
                        {
                            $grand_total = 0;
                            $selectConsignmentReceipt = ConsignmentReceiptDetails::select('product_id', 'quantity')->where([['status', '=', '1'], ['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectConsignmentReceipt) > 0) {
                                
                                foreach($selectConsignmentReceipt as $crdata) {
                                    
                                    $quantity = $crdata['quantity'];
                                    $price = 0;
                                    $selectProductMrp = OrderDetail::select('mrp')->where([['order_id', '=', $oData->order_id], ['product_id', '=', $crdata['product_id']]])->get()->toArray();
                                    if(sizeof($selectProductMrp) > 0) {
                                        $price = $selectProductMrp[0]['mrp'];
                                    }
                                    
                                    $grand_total = $grand_total + ($quantity * $price);
                                }
                            }
                            
                            $vatAmount = 0;
                            $selectVat = Orders::select('total_tax')->where([['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectVat) > 0)
                            {
                                if(!empty($selectVat[0]['total_tax']))
                                {
                                    $vatAmount = $selectVat[0]['total_tax'];
                                }
                            }
                            $grand_total = $grand_total + $vatAmount;
                            
                            // If quantity return it will reduce form invoice.
                            $returnPrice = 0;
                            $selectReturns = PurchaseOrderReturnDetails::select('order_id', 'product_id', 'return_quantity')->where([['order_id', '=', $oData->order_id]])->get()->toArray();
                            if(sizeof($selectReturns) > 0)
                            {
                                foreach($selectReturns as $rddata)
                                {
                                    $qty = $rddata['return_quantity'];
                                    $returnProductPrice = 0;
                                    
                                    $selectOrderDetails = OrderDetail::select('mrp')->where([['order_id', '=', $oData->order_id], ['product_id', '=', $rddata['product_id']]])->get()->toArray();
                                    if(sizeof($selectOrderDetails) > 0)
                                    {
                                        $returnProductPrice = $selectOrderDetails[0]['mrp'];
                                    }
                                    $qty *
                                    $returnPrice = $returnPrice + ($qty * $returnProductPrice);
                                }
                            }
                            
                            $returnVatPrice = 0;
                            if(!empty($oData->vat_type_id))
                            {
                                $selectVatDetails = VatType::select('percentage')->where([['vat_type_id', '=', $oData->vat_type_id]])->get()->toArray();
                                if(sizeof($selectVatDetails) > 0)
                                {
                                    $returnVatPrice = ($returnPrice * $selectVatDetails[0]['percentage'])/100;
                                }
                            }
                            $returnPrice = $returnPrice + $returnVatPrice;
                            $returnPrice = round($returnPrice, 3);
                            
                            $grand_total = $grand_total - $returnPrice;
                            
                            $grand_total = round($grand_total,3);
                            $outstanding_amount = $outstanding_amount + $grand_total;
                            
                            // $receipt_amount = PurchaseReceipt::where([['order_id', '=', $oData->order_id]])->sum('pay_amount');
                            // if($receipt_amount > 0)
                            // {
                            //     $receipt_amount = round($receipt_amount,3);
                            //     $partial_amount = $outstanding_amount - $receipt_amount;
                            // }
                        }
                    }
                    
                    $receipt_amount = 0;
                    $selectOrder = DB::table('orders as o')->select('o.order_id', 'o.vat_type_id')->join('consignment_receipt as cr', 'o.order_id', '=', 'cr.order_id')->where([['o.supplier_id', '=', $query->supplier_id]])->get()->toArray();
                    if(sizeof($selectOrder) > 0)
                    {
                        $calAmount = 0;
                        foreach($selectOrder as $oData)
                        {
                            $get_receipt_amount = PurchaseReceipt::where([['order_id', '=', $oData->order_id]])->sum('pay_amount');
                            if($get_receipt_amount > 0)
                            {
                                $calAmount += round($get_receipt_amount,3);
                            }
                        }
                        $receipt_amount +=$calAmount;
                    }
                    
                    $partial_amount = $outstanding_amount - $receipt_amount;
                    return $partial_amount;
                })
                ->addColumn('receipt_amount', function ($query) {
                    
                    $receipt_amount = 0;
                    $selectOrder = DB::table('orders as o')->select('o.order_id', 'o.vat_type_id')->join('consignment_receipt as cr', 'o.order_id', '=', 'cr.order_id')->where([['o.supplier_id', '=', $query->supplier_id]])->get()->toArray();
                    if(sizeof($selectOrder) > 0)
                    {
                        $calAmount = 0;
                        foreach($selectOrder as $oData)
                        {
                            $get_receipt_amount = PurchaseReceipt::where([['order_id', '=', $oData->order_id]])->sum('pay_amount');
                            if($get_receipt_amount > 0)
                            {
                                $calAmount += round($get_receipt_amount,3);
                            }
                        }
                        $receipt_amount +=$calAmount;
                    }
                    return $receipt_amount;
                })
                
                ->rawColumns([])
                ->make();
                $data=(array)$datatable_array->getData();
                $data['page']=($_POST['start']/$_POST['length'])+1;
                $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
                return $data;
        }else{
            //
        }
    }
    
    
    
    
    
    
    
    
    
    
}