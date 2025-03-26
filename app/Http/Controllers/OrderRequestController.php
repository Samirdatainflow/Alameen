<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Warehouses;
use App\Suppliers;
use App\Products;
use App\Orders;
use App\OrderDetail;
use App\ProductCategories;
use App\OrderRequest;
use App\OrderRequestDetails;
use App\ManufacturingNo;
use App\OrderQuotation;
use App\PerformaInvoice;
use DB;
use DataTables;
use PDF;
use App\PartBrand;
use App\PartName;
use App\WmsUnit;
use App\OrderRequestQuotationPrices;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class OrderRequestController extends Controller {

    public function order_request() {
        return \View::make("backend/order_request/order_request")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->orderBy('supplier_id', 'desc')->get()->toArray()
        ]);
    }
    // Add
    public function add_order_request(Request $request){
        if ($request->ajax()) {
            $requestSupplier = [];
            $OrderRequestDetails = [];
            $order_request_unique_id = '';
            if(!empty($request->id)) {
                $order_request_unique_id = $request->id;
                $OrderRequestSupplier = OrderRequest::select('supplier_id')->where([['order_request_unique_id', '=', $request->id]])->get()->toArray();
                if(sizeof($OrderRequestSupplier) > 0) {
                    foreach($OrderRequestSupplier as $sup) {
                        $requestSupplier[] = $sup['supplier_id'];
                    }
                }
                $OrderRequestDetailsSelect = OrderRequestDetails::where([['order_request_unique_id', '=', $request->id]])->get()->toArray();
                if(sizeof($OrderRequestDetailsSelect) > 0) {
                    foreach($OrderRequestDetailsSelect as $sd) {
                        $part_no = '';
                        $part_name = '';
                        $c_name = '';
                        $ct = '';
                        $product_price = '';
                        $part_brand_name = '';
                        $unit_name = '';
                        $manufacturing_no = '';
                        $products = Products::select('pmpno', 'ct', 'part_name_id', 'pmrprc', 'part_brand_id', 'unit', 'car_manufacture_id')->where([['product_id', '=', $sd['product_id']]])->get()->toArray();
                        if(sizeof($products) > 0) {
                            $part_no = $products[0]['pmpno'];
                            $product_price = $products[0]['pmrprc'];
                            $ct = $products[0]['ct'];
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                $part_name = $PartName[0]['part_name'];
                            }
                            $PartBrand = PartBrand::select('part_brand_name')->where([['part_brand_id', '=', $products[0]['part_brand_id']]])->get()->toArray();
                            if(sizeof($PartBrand) > 0) {
                                $part_brand_name = $PartBrand[0]['part_brand_name'];
                            }
                            $WmsUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $products[0]['unit']]])->get()->toArray();
                            if(sizeof($WmsUnit) > 0) {
                                $unit_name = $WmsUnit[0]['unit_name'];
                            }
                            $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $sd['product_id']]])->get()->toArray();
                            if(sizeof($ManufacturingNo) > 0) {
                                $out = array();
                                foreach ($ManufacturingNo as $man) {
                                    $manufacturing_no .= $man['manufacturing_no'].", ";
                                }
                                $manufacturing_no = substr($manufacturing_no, 0, -2);
                            }
                            $ProductCategories = ProductCategories::select('category_name')->where([['category_id', '=', $products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($ProductCategories) > 0) {
                                $c_name = $ProductCategories[0]['category_name'];
                            }
                        }
                        array_push($OrderRequestDetails, array('product_id' => $sd['product_id'], 'part_no' => $part_no, 'part_brand_name' => $part_brand_name, 'part_name' => $part_name, 'unit_name' => $unit_name, 'manufacturing_no' => $manufacturing_no, 'c_name' => $c_name, 'ct' => $ct, 'pmrprc' => $product_price, 'current_stock' => '', 'qty' => $sd['qty']));
                    }
                }
            }
            //print_r($requestSupplier); exit();
            $html = view('backend.order_request.order_request_form')->with([
                'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray(),
                'requestSupplier' => $requestSupplier,
                'OrderRequestDetails' => $OrderRequestDetails,
                'order_request_unique_id' => $order_request_unique_id
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function save_order_request(Request $request) {
        $returnData = [];
        if(!empty($request->supplier)) {
            $order_request_unique_id=1;
            $max_request_id = OrderRequest::max('order_request_unique_id');
            if($max_request_id >0)
            {
                $order_request_unique_id = ($max_request_id+1);
            }
            if(!empty($request->hidden_order_request_unique_id)) {
                OrderRequest::where([['order_request_unique_id', '=', $request->hidden_order_request_unique_id]])->delete();
                OrderRequestDetails::where([['order_request_unique_id', '=', $request->hidden_order_request_unique_id]])->delete();
                $order_request_unique_id = $request->hidden_order_request_unique_id;
            }
            $return_url = "order-request";
            $order_request_status = 1;
            if($request->order_request_status == "SaveOrder") {
                $order_request_status = 2;
                $return_url = "save-order-request";
            }
            foreach($request->supplier as $v=>$k) {
            	$data = new OrderRequest;
                $data->order_request_unique_id = $order_request_unique_id;
		        $data->supplier_id = $k;
                $data->created_by = Session::get('user_id');
                $data->mail_status = "0";
                $data->status = "1";
		        $data->order_request_status = $order_request_status;
		        $saveData = $data->save();
            }
            if(sizeof($request->entry_product) > 0) {
                $flag=0;
                for($i = 0; $i<sizeof($request->entry_product); $i++) {
                    $data2 = new OrderRequestDetails;
                    $data2->order_request_unique_id = $order_request_unique_id;
                    $data2->product_id = $request->entry_product[$i];
                    $data2->qty = $request->entry_product_quantity[$i];
                    $data2->status = "1";
                    $saveData = $data2->save();
                    $flag++;
                }
            }
            if($saveData) {
                if($flag == sizeof($request->entry_product)) {
                    $returnData = ["status" => 1, "msg" => "Save successful.", 'return_url' => $return_url];
                }else {
                    $returnData = ["status" => 0, "msg" => "Something is wrong."];
                }
            }else {
                $returnData = ["status" => 0, "msg" => "Save faild."];
            }
        }else {
            $returnData = ["status" => 0, "msg" => "Save faild. No record found"];
        }
        return response()->json($returnData);
    }
    public function list_order_request(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('order_request as o');
            $query->select('o.order_request_unique_id',DB::raw('DATE(o.created_at) as created_att'),'o.created_by');
            $query->where([['o.status', '!=', '2']]);
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.order_request_unique_id', 'asc');
                else
                    $query->orderBy('o.order_request_unique_id', 'desc');
            }
            else
            {
                $query->orderBy('o.order_request_unique_id', 'DESC');
            }
            $query->groupBy(['order_request_unique_id','created_att','created_by']);
            $query->where([['order_request_status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->created_att)) {
                    $order_date = date('d M Y', strtotime($query->created_att));
                }
                return $order_date;
            })
            ->addColumn('created_by', function ($query) {
                $created_by = "";
                $selectQty = Users::where('user_id',$query->created_by)->select('first_name','last_name')->get()->toArray();
                if(sizeof($selectQty)>0)
                {
                    $created_by = $selectQty[0]['first_name']." ".$selectQty[0]['last_name'];
                }
                return $created_by;
            })
            ->addColumn('item', function ($query) {
                $selectQty = OrderRequestDetails::where('order_request_unique_id',$query->order_request_unique_id)->sum('qty');
                return $selectQty;
            })
            ->addColumn('total_supplier', function ($query) {
                $OrderRequest = OrderRequest::where('order_request_unique_id',$query->order_request_unique_id)->get();
                $total_supplier = sizeof($OrderRequest);
                return $total_supplier;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-request-order-details" data-id="'.$query->order_request_unique_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            // ->addColumn('action', function ($query) {
            //     $OrderQuotation = OrderQuotation::where([['order_request_id', '=', $query->order_request_unique_id], ['status', '=', '1']])->get()->toArray();
            //     if(sizeof($OrderQuotation) > 0) {
            //         $action = '<a href="javascript:void(0)" class="generate-pdf" data-id="'.$query->order_request_id.'"><button type="button" class="btn btn-warning btn-sm" title="PDF"><i class="fa fa-download"></i></button></a>';
            //     }else {
            //         $action = '<a href="javascript:void(0)" class="generate-pdf" data-id="'.$query->order_request_id.'"><button type="button" class="btn btn-warning btn-sm" title="PDF"><i class="fa fa-download"></i></button></a> <a href="javascript:void(0)" class="delete-request-order" data-id="'.$query->order_request_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
            //     }
            //     return $action;
            // })
            ->rawColumns(['details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function delete_request_order(Request $request) {
        $returnData = [];
        $upData = OrderRequest::where('order_request_id', $request->id)->update(['status' => "2"]);
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    public function view_request_order_details(Request $request){
        if ($request->ajax()) {
        	$returnData = [];
            $supplierData = [];
            $is_confirm = "";
            $name="";
            $order_request = OrderRequest::select('order_request.created_by','order_request.created_at')->where('order_request_unique_id',$request->id)->groupBy('created_by','created_at','order_request_unique_id')->get()->toArray();
            if(sizeof($order_request)>0)
            {
                $users = Users::select('first_name','last_name')->where('user_id',$order_request[0]['created_by'])->get()->toArray();
                if(sizeof($users)>0)
                {
                    $name = $users[0]['first_name']." ".$users[0]['last_name'];
                }
            }
            $OrderQuotation = OrderQuotation::select('is_confirm')->where([['order_request_unique_id', '=', $request->id], ['is_confirm', '=', '1'], ['status', '=', '1']])->get()->toArray();
            if(!empty($OrderQuotation)) {
                if(!empty($OrderQuotation[0]['is_confirm'])) $is_confirm = $OrderQuotation[0]['is_confirm'];
            }
            $query = DB::table('order_request_details as o');
            $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
            $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
            $query->select('o.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'o.order_request_details_id', 'o.order_request_unique_id','o.qty');
            $query->where([['o.order_request_unique_id', '=', $request->id]]);
            $orderDetails = $query->get()->toArray();
            if(sizeof($orderDetails) > 0) {
            	foreach($orderDetails as $data) {
            		$ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
            		array_push($returnData, array('part_brand_name' => $data->part_brand_name, 'pmpno' => $data->pmpno, 'part_name' => $data->part_name, 'unit_name' => $data->unit_name, 'order_request_details_id' => $data->order_request_details_id, 'order_request_unique_id' => $data->order_request_unique_id, 'qty' => $data->qty, 'manufacturing_no' => $ManufacturingNo));
            	}
            }
            $selectSupplierIds = OrderRequest::select('supplier_id')->where([['order_request_unique_id', '=', $request->id]])->get()->toArray();
            if(sizeof($selectSupplierIds) > 0) {
                foreach($selectSupplierIds as $ids) {
                    $supplier_id = "";
                    $supplier_name = '';
                    if(!empty($ids['supplier_id'])) {
                        $supplier_id = $ids['supplier_id'];
                        $Suppliers = Suppliers::select('supplier_id', 'full_name')->where([['supplier_id', '=', $supplier_id]])->get()->toArray();
                        if(sizeof($Suppliers) > 0) {
                            $supplier_name = $Suppliers[0]['full_name'];
                        }
                    }
                    $order_quotation = "0";
                    $order_quotation_file = "";
                    $order_quotation_file_extention = "";
                    $quotation_is_confirm = "";
                    $OrderQuotation = OrderQuotation::where([['order_request_unique_id', '=', $request->id], ['supplier_id', '=', $ids['supplier_id']]])->get()->toArray();
                    if(sizeof($OrderQuotation) > 0) {
                        if(!empty($OrderQuotation[0]['quotation'])) {
                            $order_quotation_file_extention = substr($OrderQuotation[0]['quotation'], strrpos($OrderQuotation[0]['quotation'], '.' )+1);
                            $url = url('public/backend/images/quotation_file/');
                            $order_quotation_file = $url."/".$OrderQuotation[0]['quotation'];
                            $order_quotation = "1";
                        }
                        if(!empty($OrderQuotation[0]['is_confirm'])) $quotation_is_confirm = $OrderQuotation[0]['is_confirm'];
                    }
                    $performa_invoice = "0";
                    $performa_invoice_extention = "";
                    $performa_invoice_file = "";
                    $PerformaInvoice = PerformaInvoice::where([['order_request_unique_id', '=', $request->id]])->get()->toArray();
                    if(sizeof($PerformaInvoice) > 0) {
                        if(!empty($PerformaInvoice[0]['invoice'])) {
                            $performa_invoice_extention = substr($PerformaInvoice[0]['invoice'], strrpos($PerformaInvoice[0]['invoice'], '.' )+1);
                            $url = url('public/backend/images/invoice_file/');
                            $performa_invoice_file = $url."/".$PerformaInvoice[0]['invoice'];
                        }
                        //if(!empty($OrderQuotation[0]['is_confirm'])) $is_confirm = $OrderQuotation[0]['is_confirm'];
                        $performa_invoice = "1";
                    }
                    $quotation_prices_upload = "0";
                    $OrderRequestQuotationPrices = OrderRequestQuotationPrices::where([['order_request_unique_id', '=', $request->id], ['supplier_id', '=', $supplier_id]])->get()->toArray();
                    if(sizeof($OrderRequestQuotationPrices) > 0) {
                        $quotation_prices_upload = "1";
                    }
                    array_push($supplierData, array('supplier_id' => $supplier_id, 'supplier_name' => $supplier_name, 'order_quotation' => $order_quotation, 'order_quotation_file' => $order_quotation_file, 'order_quotation_file_extention' => $order_quotation_file_extention, 'quotation_is_confirm' => $quotation_is_confirm, 'performa_invoice' => $performa_invoice, 'performa_invoice_extention' => $performa_invoice_extention, 'performa_invoice_file' => $performa_invoice_file, 'quotation_prices_upload' => $quotation_prices_upload));
                }
            }
            $html = view('backend.order_request.order_request_details')->with([
                'order_request_unique_id' => $request->id,
                'created_by' => $name,
                'order_date' => $order_request[0]['created_at'],
                'order_data' => $returnData,
                'is_confirm' => $is_confirm,
                'supplier_data' => $supplierData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Save OQuotation
    public function save_quotation(Request $request) {
        if ($request->ajax()) {
            $upimages = $request->quotation_file;
            $quotation_file = rand() . '.' . $upimages->getClientOriginalExtension();
            $upimages->move(public_path('backend/images/quotation_file/'), $quotation_file);
            $data = new OrderQuotation;
            $data->order_request_unique_id = $request->order_request_unique_id;
            $data->supplier_id = $request->supplier_id;
            $data->quotation = $quotation_file;
            $data->is_confirm = "0";
            $data->status = "1";
            $saveData = $data->save();
            if($saveData) {
                $order_quotation_file_extention = substr($quotation_file, strrpos($quotation_file, '.' )+1);
                $url = url('public/backend/images/quotation_file/');
                $order_quotation_file = $url."/".$quotation_file;
                return response()->json(["status" => 1, "msg" => "Upload successful.", 'order_quotation_file_extention' => $order_quotation_file_extention, 'order_quotation_file' => $order_quotation_file]);
            }else {
                return response()->json(["status" => 1, "msg" => "Upload faild!"]);
            }
        }
    }
    // Save Performa Invoices
    public function upload_performa_invoice(Request $request) {
        if ($request->ajax()) {
            $upimages = $request->performa_invoice;
            $performa_invoice = rand() . '.' . $upimages->getClientOriginalExtension();
            $upimages->move(public_path('backend/images/invoice_file/'), $performa_invoice);
            $data = new PerformaInvoice;
            $data->order_request_unique_id = $request->order_request_unique_id;
            $data->supplier_id = $request->supplier_id;
            $data->invoice = $performa_invoice;
            $data->status = "1";
            $saveData = $data->save();
            if($saveData) {
                $performa_invoice_extention = substr($performa_invoice, strrpos($performa_invoice, '.' )+1);
                $url = url('public/backend/images/invoice_file/');
                $performa_invoice_file = $url."/".$performa_invoice;
                return response()->json(["status" => 1, "msg" => "Upload successful.", 'performa_invoice_extention' => $performa_invoice_extention, 'performa_invoice_file' => $performa_invoice_file]);
            }else {
                return response()->json(["status" => 1, "msg" => "Upload faild!"]);
            }
        }
    }
    public function confirm_order_request(Request $request) {
        if ($request->ajax()) {
            $OrderQuotation = OrderQuotation::where([['order_request_unique_id', '=', $request->order_request_unique_id], ['supplier_id', '=', $request->supplier_id]])->update(['is_confirm' => '1']);
            if($OrderQuotation) {
                return response()->json(["status" => 1, "message" => "Confirm Done"]);
            }else {
                return response()->json(["status" => 0, "message" => "Confirm Faild!"]);
            }
        }
    }
    // Get Product By Part No
    public function get_product_by_part_no(Request $request) {
        if ($request->ajax()) {
            $view = "";
            $query = DB::table('products as p');
            $query->select('p.product_id', 'p.pmpno', 'pn.part_name');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->whereRaw('p.is_deleted != 1 and (p.pmpno LIKE "%'.$request->part_no.'%" or replace(p.pmpno, "-","") LIKE "%'.$request->part_no.'%" or  pn.part_name LIKE "%'.$request->part_no.'%")');
            $Products = $query->limit('100')->get()->toArray();
            //Products::select('product_id', 'part_name', 'pmpno')->whereRaw('supplier_id = '.$request->supplier.' and is_deleted != 1 and (pmpno LIKE "%'.$request->part_no.'%" or  part_name LIKE "%'.$request->part_no.'%") and FIND_IN_SET('.$request->warehouse.' ,warehouse_id )')->limit('100')->get()->toArray();
            if(sizeof($Products) > 0) {
                $view = $view.'<ul class="list-group">';
                $flag = 1;
                foreach($Products as $data) {
                    $view = $view.'<li class="list-group-item" tabindex="'.$flag.'"><a href="#" class="product-details" style="text-decoration: none" data-pmpno="'.$data->pmpno.'" data-product-id="'.$data->product_id.'">'.$data->part_name.' ('.$data->pmpno.')</a></li>';
                    $flag++;
                }
                $view = $view.'</ul>';
                return response()->json(["status" => 1, "data" => $view]);
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
                $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
                $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
                $query->join('wms_units as wu', 'wu.unit_id', '=', 'p.unit', 'left');
                $query->select('p.*', 'pn.part_name', 'pb.part_brand_name', 'wu.unit_name');
                $query->where('p.pmpno', '=', $request->part_no);
                $selectDdata=$query->get()->toArray();
                if(sizeof($selectDdata) > 0) {
                    $manufacturing_no = "";
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $selectDdata[0]->product_id]])->get()->toArray();
                    if(sizeof($ManufacturingNo) > 0) {
                        $out = array();
                        foreach ($ManufacturingNo as $man) {
                            $manufacturing_no .= $man['manufacturing_no'].", ";
                        }
                        $manufacturing_no = substr($manufacturing_no, 0, -2);
                    }
                    array_push($ProductsData, array('product_id' => $selectDdata[0]->product_id, 'pmpno' => $selectDdata[0]->pmpno, 'part_name' => $selectDdata[0]->part_name, 'part_brand_name' => $selectDdata[0]->part_brand_name, 'unit_name' => $selectDdata[0]->unit_name, 'manufacturing_no' => $manufacturing_no));
                    $product_entry_count = $request->product_entry_count + 1;
                    $returnData = array('status' => 1, 'data' => $ProductsData, 'product_entry_count' => $product_entry_count);
                }else {
                    $returnData = array('status' => 1, 'msg' => "No record found.");
                }
            }
            return response()->json($returnData);
        }
    }
    //Order Preview
    public function order_preview(Request $request) {
        $supplier = $request->supplier;
        $file = $_FILES['file']['tmp_name'];
        $productArr = $this->csvToArrayWithAll($file, $supplier);
        return \View::make("backend/order_request/order_preview")->with(array('products'=>$productArr['data']));
    }
    function csvToArrayWithAll($filename = '', $supplier = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    //$supplier_name = "";
                    $part_no = "";
                    $part_brand = "";
                    $part_name = "";
                    $unit_name = "";
                    $manufacturing_no = "";
                    $product = "";
                    $product_details = $this->get_product_details_by_partno($row[0], $supplier);
                    if(!empty($product_details)) {
                        $product = 1;
                        if(!empty($product_details['part_brand'])) $part_brand = $product_details['part_brand'];
                        if(!empty($product_details['part_name'])) $part_name = $product_details['part_name'];
                        if(!empty($product_details['unit_name'])) $unit_name = $product_details['unit_name'];
                        if(!empty($product_details['manufacturing_no'])) $manufacturing_no = $product_details['manufacturing_no'];
                    }
                    array_push($data, array('product' => $product, 'part_no' => $row[0], 'part_brand' => $part_brand, 'part_name' => $part_name, 'unit_name' => $unit_name, 'manufacturing_no' => $manufacturing_no, 'quantity' => $row[1]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    // function get_supplier_by_id($id) {
    //     return Suppliers::where([['supplier_id', '=', $id], ['status', '=', '1']])->get()->toArray();
    // }
    function get_product_details_by_partno($part_no, $supplier) {
        $data_array=[];
        $part_brand = "";
        $part_name = "";
        $unit_name = "";
        $manufacturing_no = "";
        $supplier_ids = explode(',', $supplier);
        $Products = Products::select('product_id', 'part_brand_id', 'part_name_id', 'unit')->where([['pmpno', '=', $part_no]])->whereIn('supplier_id' ,$supplier_ids)->get()->toArray();
        if(sizeof($Products) > 0) {
            if(!empty($Products[0]['part_brand_id'])) {
                $PartBrand = PartBrand::select('part_brand_name')->where('part_brand_id', $Products[0]['part_brand_id'])->get()->toArray();
                if(sizeof($PartBrand) > 0) {
                    if(!empty($PartBrand[0]['part_brand_name'])) $part_brand = $PartBrand[0]['part_brand_name'];
                }
            }
            if(!empty($Products[0]['part_name_id'])) {
                $PartName = PartName::select('part_name')->where('part_name_id', $Products[0]['part_name_id'])->get()->toArray();
                if(sizeof($PartName) > 0) {
                    if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                }
            }
            if(!empty($Products[0]['product_id'])) {
                $WmsUnits = WmsUnit::select('unit_name')->where('unit_id', $Products[0]['unit'])->get()->toArray();
                if(sizeof($WmsUnits) > 0) {
                    if(!empty($WmsUnits[0]['unit_name'])) $unit_name = $WmsUnits[0]['unit_name'];
                }
            }
            $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $Products[0]['product_id']]])->get()->toArray();
            if(sizeof($ManufacturingNo) > 0) {
                $out = array();
                foreach ($ManufacturingNo as $man) {
                    $manufacturing_no .= $man['manufacturing_no'].", ";
                }
                $manufacturing_no = substr($manufacturing_no, 0, -2);
            }
            $data_array = array('product_id'=>$Products[0]['product_id'], 'part_no'=> $part_no, 'part_brand'=> $part_brand, 'part_name'=>$part_name, 'unit_name' => $unit_name, 'manufacturing_no' => $manufacturing_no);
        }
        return $data_array;
    }
    //
    public function create_multiple_order(Request $request){
        $returnData = [];
        $file = $_FILES['file']['tmp_name'];
        $supplier = $request->supplier;
        //$productArr = $this->csvToArray($file, $supplier);
        $supplier_ids = explode(',', $supplier);
        $flag=0;
        foreach($supplier_ids as $k=>$v) {
            $order_request_unique_id=1;
            $max_request_id = OrderRequest::max('order_request_unique_id');
            if($max_request_id >0) {
                $order_request_unique_id = ($max_request_id+1);
            }
            $data = new OrderRequest;
            $data->order_request_unique_id = $order_request_unique_id;
            $data->supplier_id = $v;
            $data->created_by = Session::get('user_id');
            $data->mail_status = "0";
            $data->status = "1";
            $data->save();
            $productArr = $this->csvToArray($file, $supplier);
            foreach($productArr['data'] as $data) {
                if($data['product'] != "") {
                    $data2 = new OrderRequestDetails;
                    $data2->order_request_unique_id = $order_request_unique_id;
                    $data2->product_id = $data['product_id'];
                    $data2->qty = $data['quantity'];
                    $data2->status = "1";
                    $data2->save();
                }
            }
            $flag++;
        }
        if($flag == sizeof($supplier_ids)) {
            $returnData = ["status" => 1, "msg" => "Save successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Something is wrong."];
        }
        return response()->json($returnData);
    }
    function csvToArray($filename = '', $supplier = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = [];
        $sub_total=0;
        $total_gst=0;
        $grand_total=0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else {
                    $part_no = "";
                    $product_id = "";
                    $product = "";
                    $product_details = $this->get_product_details_by_partno($row[0], $supplier);
                    if(!empty($product_details)) {
                        $product = 1;
                        if(!empty($product_details['product_id'])) $product_id = $product_details['product_id'];
                    }
                    array_push($data, array('product' => $product, 'product_id' => $product_id, 'quantity' => $row[1]));
                }
            }
            fclose($handle);
        }
        return array('data'=>$data);
    }
    // Delete Order Details
    public function delete_order_request_details(Request $request) {
        $returnData = [];
        $upData = OrderRequestDetails::where('order_request_details_id', $request->id)->delete();
        if($upData) {
            $returnData = ["status" => 1, "msg" => "Delete successful."];
        }else {
            $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
        }
        return response()->json($returnData);
    }
    public function pdf_request_order(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_request_order_to_html($id));
        return $pdf->stream();
    }
    public function download_request_order(Request $request) {
        $id = $request->id;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->download_request_order_to_html($id));
        return $pdf->stream();
    }
    public function download_request_order_to_html($id) {
        $arrayData = [];
            $OrderRequest = OrderRequest::select('supplier_id')->where([['order_request_unique_id', '=', $id]])->get()->toArray();
            if(sizeof($OrderRequest) > 0) {
                foreach($OrderRequest as $requestData) {
                    $supplier_name = "";
                    $pmpno = "";
                    $pmrprc = "";
                    $part_brand = "";
                    $part_name = "";
                    $unit_name = "";
                    $manufacturing_no = [];
                    $quotation_prices = 0;
                    $Suppliers = Suppliers::where([['supplier_id', '=', $requestData['supplier_id']], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($Suppliers) > 0) {
                        if(!empty($Suppliers[0]['full_name'])) $supplier_name = $Suppliers[0]['full_name'];
                    }
                    $OrderRequestQuotationPrices = OrderRequestQuotationPrices::where([['order_request_unique_id', '=', $id], ['supplier_id', '=', $requestData['supplier_id']]])->get()->toArray();
                    if(sizeof($OrderRequestQuotationPrices) > 0) {
                        foreach($OrderRequestQuotationPrices as $priceData) {
                            $Products = Products::select('pmpno', 'pmrprc', 'part_brand_id', 'part_name_id', 'unit')->where([['product_id', '=', $priceData['product_id']]])->get()->toArray();
                            if(sizeof($Products) > 0) {
                                if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                                if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                                if(!empty($Products[0]['part_brand_id'])) {
                                    $PartBrand = PartBrand::select('part_brand_name')->where('part_brand_id', $Products[0]['part_brand_id'])->get()->toArray();
                                    if(sizeof($PartBrand) > 0) {
                                        if(!empty($PartBrand[0]['part_brand_name'])) $part_brand = $PartBrand[0]['part_brand_name'];
                                    }
                                }
                                if(!empty($Products[0]['part_name_id'])) {
                                    $PartName = PartName::select('part_name')->where('part_name_id', $Products[0]['part_name_id'])->get()->toArray();
                                    if(sizeof($PartName) > 0) {
                                        if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                                    }
                                }
                                $WmsUnits = WmsUnit::select('unit_name')->where('unit_id', $Products[0]['unit'])->get()->toArray();
                                if(sizeof($WmsUnits) > 0) {
                                    if(!empty($WmsUnits[0]['unit_name'])) $unit_name = $WmsUnits[0]['unit_name'];
                                }
                                $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $priceData['product_id']]])->get()->toArray();
                                if(sizeof($ManufacturingNo) > 0) {
                                    $manufacturing_no = $ManufacturingNo;
                                }
                                $persentage = 0;
                                $status = "";
                                if(!empty($priceData['price'])) {
                                    $quotation_prices = $priceData['price'];
                                    if($quotation_prices > $pmrprc) {
                                        $pr1 = $quotation_prices - $pmrprc;
                                        $persentage = ($pr1 * 100) / $pmrprc;
                                        $persentage = number_format ($persentage, 2);
                                        $status = "high";
                                    }else {
                                        $pr1 = $pmrprc - $quotation_prices;
                                        $persentage = ($pr1 * 100) / $pmrprc;
                                        $persentage = number_format ($persentage, 2);
                                        $status = "low";
                                    }
                                }
                            }
                            array_push($arrayData, array('quotation_prices' => $quotation_prices, 'part_no'=> $pmpno, 'part_brand'=> $part_brand, 'part_name'=>$part_name, 'unit_name' => $unit_name, 'manufacturing_no' => $manufacturing_no, 'pmrprc' => $pmrprc, 'persentage' => $persentage, 'status' => $status, 'supplier_name' => $supplier_name));
                        }
                    }
                }
            }
            return view('backend.order_request.download_request_order')->with([
                'OrderRequest' => $arrayData,
            ]);
            // return $arrayData;
    }
    function convert_request_order_to_html($id) {
        $OrderRequestDetails = [];
        $query = DB::table('order_request_details as o');
        $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
        $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
        $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
        $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
        $query->select('o.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'o.order_request_details_id', 'o.order_request_unique_id','o.qty');
        $query->where([['o.order_request_unique_id', '=', $id]]);
        $orderDetails = $query->get()->toArray();
        if(sizeof($orderDetails) > 0) {
            foreach($orderDetails as $data) {
                $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id]])->get()->toArray();
                array_push($OrderRequestDetails, array('part_brand_name' => $data->part_brand_name, 'pmpno' => $data->pmpno, 'part_name' => $data->part_name, 'unit_name' => $data->unit_name, 'order_request_details_id' => $data->order_request_details_id, 'order_request_id' => $data->order_request_unique_id, 'qty' => $data->qty, 'manufacturing_no' => $ManufacturingNo));
            }
        }
        return view('backend.order_request.pdf_request_order')->with([
            'OrderRequest' => OrderRequest::select('order_request_id', 'supplier_id', 'created_at')->where([['order_request_id', '=', $id]])->get()->toArray(),
            'OrderRequestDetails' => $OrderRequestDetails
        ]);
    }
    public function view_order_details_4_price(Request $request){
        if ($request->ajax()) {
            $returnData = [];
            $query = DB::table('order_request_details as o');
            $query->join('products as p', 'p.product_id', '=', 'o.product_id', 'left');
            $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
            $query->select('o.order_request_details_id', 'o.product_id', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'o.order_request_details_id', 'o.order_request_unique_id','o.qty');
            $query->where([['o.order_request_unique_id', '=', $request->order_request_unique_id]]);
            $orderDetails = $query->get()->toArray();
            if(sizeof($orderDetails) > 0) {
                foreach($orderDetails as $data) {
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data->product_id], ['status', '=', '1']])->get()->toArray();
                    array_push($returnData, array('order_request_details_id' => $data->order_request_details_id, 'product_id' => $data->product_id, 'part_brand_name' => $data->part_brand_name, 'pmpno' => $data->pmpno, 'part_name' => $data->part_name, 'unit_name' => $data->unit_name, 'order_request_details_id' => $data->order_request_details_id, 'order_request_unique_id' => $data->order_request_unique_id, 'qty' => $data->qty, 'manufacturing_no' => $ManufacturingNo));
                }
            }
            $html = view('backend.order_request.order_details_4_upload_price')->with([
                'order_request_unique_id' => $request->order_request_unique_id,
                'supplier_id' => $request->supplier_id,
                'order_data' => $returnData,
                'row_id' => $request->row_id,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function save_quotation_prices(Request $request) {
        if ($request->ajax()) {
            $flag = 0;
            if(sizeof($request->quotation_price) > 0) {
                for($i=0; $i< sizeof($request->quotation_price); $i++) {
                    $data = new OrderRequestQuotationPrices;
                    $data->order_request_unique_id = $request->order_request_unique_id;
                    $data->order_request_details_id = $request->order_request_details_id[$i];
                    $data->supplier_id = $request->supplier_id;
                    $data->product_id = $request->product_id[$i];
                    $data->price = $request->quotation_price[$i];
                    $data->save();
                    $flag++;
                }
                if($flag == sizeof($request->quotation_price)) {
                    return response()->json(["status" => 1, "msg" => "Price upload successful.", 'order_request_unique_id' => $request->order_request_unique_id, 'supplier_id' => $request->supplier_id, 'row_id' => $request->row_id]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "Something is wrong"]);
            }
        }
    }
    public function view_quotation_price(Request $request){
        if ($request->ajax()) {
            $returnData = [];
            // $query = DB::table('order_request_quotation_prices as op');
            // $query->join('order_request_details as od', 'od.order_request_unique_id', '=', 'op.order_request_unique_id', 'left');
            // $query->join('products as p', 'p.product_id', '=', 'op.product_id', 'left');
            // $query->join('part_brand as pb', 'pb.part_brand_id', '=', 'p.part_brand_id', 'left');
            // $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            // $query->join('wms_units as un', 'un.unit_id', '=', 'p.unit', 'left');
            // $query->select('op.order_request_quotation_prices_id', 'op.product_id', 'op.price', 'pb.part_brand_name', 'p.pmpno', 'pn.part_name', 'un.unit_name', 'op.order_request_unique_id','od.qty');
            // $query->where([['op.order_request_unique_id', '=', $request->order_request_unique_id], ['op.supplier_id', '=', $request->supplier_id]]);
            //$orderDetails = $query->get()->toArray();
            $orderDetails = OrderRequestQuotationPrices::where([['order_request_unique_id', '=', $request->order_request_unique_id], ['supplier_id', '=', $request->supplier_id]])->get()->toArray();
            //print_r($orderDetails); exit();
            if(sizeof($orderDetails) > 0) {
                foreach($orderDetails as $data) {
                    $part_brand_name = "";
                    $pmpno = "";
                    $part_name = "";
                    $unit_name = "";
                    $qty = "";
                    $Products = Products::select('part_brand_id', 'pmpno', 'part_name_id', 'unit')->where([['product_id', '=', $data['product_id']], ['is_deleted', '=', '0']])->get()->toArray();
                    if(sizeof($Products)) {
                        if(!empty($Products[0]['part_brand_id'])) {
                            $PartBrand = PartBrand::select('part_brand_name')->where([['part_brand_id', '=', $Products[0]['part_brand_id']]])->get()->toArray();
                            if(sizeof($PartBrand) > 0) {
                                $part_brand_name = $PartBrand[0]['part_brand_name'];
                            }
                        }
                        if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                        if(!empty($Products[0]['part_name_id'])) {
                            $PartName = PartName::select('part_name')->where([['part_name_id', '=', $Products[0]['part_name_id']]])->get()->toArray();
                            if(sizeof($PartName) > 0) {
                                $part_name = $PartName[0]['part_name'];
                            }
                        }
                        if(!empty($Products[0]['unit'])) {
                            $WmsUnit = WmsUnit::select('unit_name')->where([['unit_id', '=', $Products[0]['unit']]])->get()->toArray();
                            if(sizeof($WmsUnit) > 0) {
                                $unit_name = $WmsUnit[0]['unit_name'];
                            }
                        }
                    }
                    $OrderRequestDetails = OrderRequestDetails::select('qty')->where([['order_request_unique_id', '=', $request->order_request_unique_id]])->get()->toArray();
                    if(sizeof($OrderRequestDetails) > 0) {
                        $qty = $OrderRequestDetails[0]['qty'];
                    }
                    $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $data['product_id']], ['status', '=', '1']])->get()->toArray();
                    array_push($returnData, array('product_id' => $data['product_id'], 'price' => $data['price'], 'part_brand_name' => $part_brand_name, 'pmpno' => $pmpno, 'part_name' => $part_name, 'unit_name' => $unit_name, 'order_request_unique_id' => $data['order_request_unique_id'], 'qty' => $qty, 'manufacturing_no' => $ManufacturingNo));
                }
            }
            $html = view('backend.order_request.view_quotation_price')->with([
                'order_request_unique_id' => $request->order_request_unique_id,
                'supplier_id' => $request->supplier_id,
                'order_data' => $returnData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    public function compare_price(Request $request) {
        if ($request->ajax()) {
            $arrayData = [];
            $OrderRequest = OrderRequest::select('supplier_id')->where([['order_request_unique_id', '=', $request->order_request_unique_id]])->get()->toArray();
            if(sizeof($OrderRequest) > 0) {
                foreach($OrderRequest as $requestData) {
                    $supplier_name = "";
                    $pmpno = "";
                    $pmrprc = "";
                    $part_brand = "";
                    $part_name = "";
                    $unit_name = "";
                    $manufacturing_no = [];
                    $quotation_prices = 0;
                    $Suppliers = Suppliers::where([['supplier_id', '=', $requestData['supplier_id']], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($Suppliers) > 0) {
                        if(!empty($Suppliers[0]['full_name'])) $supplier_name = $Suppliers[0]['full_name'];
                    }
                    $OrderRequestQuotationPrices = OrderRequestQuotationPrices::where([['order_request_unique_id', '=', $request->order_request_unique_id], ['supplier_id', '=', $requestData['supplier_id']]])->get()->toArray();
                    if(sizeof($OrderRequestQuotationPrices) > 0) {
                        foreach($OrderRequestQuotationPrices as $priceData) {
                            $Products = Products::select('pmpno', 'pmrprc', 'part_brand_id', 'part_name_id', 'unit')->where([['product_id', '=', $priceData['product_id']]])->get()->toArray();
                            if(sizeof($Products) > 0) {
                                if(!empty($Products[0]['pmpno'])) $pmpno = $Products[0]['pmpno'];
                                if(!empty($Products[0]['pmrprc'])) $pmrprc = $Products[0]['pmrprc'];
                                if(!empty($Products[0]['part_brand_id'])) {
                                    $PartBrand = PartBrand::select('part_brand_name')->where('part_brand_id', $Products[0]['part_brand_id'])->get()->toArray();
                                    if(sizeof($PartBrand) > 0) {
                                        if(!empty($PartBrand[0]['part_brand_name'])) $part_brand = $PartBrand[0]['part_brand_name'];
                                    }
                                }
                                if(!empty($Products[0]['part_name_id'])) {
                                    $PartName = PartName::select('part_name')->where('part_name_id', $Products[0]['part_name_id'])->get()->toArray();
                                    if(sizeof($PartName) > 0) {
                                        if(!empty($PartName[0]['part_name'])) $part_name = $PartName[0]['part_name'];
                                    }
                                }
                                $WmsUnits = WmsUnit::select('unit_name')->where('unit_id', $Products[0]['unit'])->get()->toArray();
                                if(sizeof($WmsUnits) > 0) {
                                    if(!empty($WmsUnits[0]['unit_name'])) $unit_name = $WmsUnits[0]['unit_name'];
                                }
                                $ManufacturingNo = ManufacturingNo::select('manufacturing_no')->where([['product_id', '=', $priceData['product_id']]])->get()->toArray();
                                if(sizeof($ManufacturingNo) > 0) {
                                    $manufacturing_no = $ManufacturingNo;
                                }
                                $persentage = 0;
                                $status = "";
                                if(!empty($priceData['price'])) {
                                    $quotation_prices = $priceData['price'];
                                    if($quotation_prices > $pmrprc) {
                                        $pr1 = $quotation_prices - $pmrprc;
                                        $persentage = ($pr1 * 100) / $pmrprc;
                                        $persentage = number_format ($persentage, 2);
                                        $status = "high";
                                    }else {
                                        $pr1 = $pmrprc - $quotation_prices;
                                        $persentage = ($pr1 * 100) / $pmrprc;
                                        $persentage = number_format ($persentage, 2);
                                        $status = "low";
                                    }
                                }
                            }
                            array_push($arrayData, array('quotation_prices' => $quotation_prices, 'part_no'=> $pmpno, 'part_brand'=> $part_brand, 'part_name'=>$part_name, 'unit_name' => $unit_name, 'manufacturing_no' => $manufacturing_no, 'pmrprc' => $pmrprc, 'persentage' => $persentage, 'status' => $status, 'supplier_name' => $supplier_name));
                        }
                    }
                }
            }
            $html = view('backend.order_request.view_compare_price')->with([
                'compare_data' => $arrayData,
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Save Order Request
    public function save_order_request_page() {
        return \View::make("backend/order_request/save_order_request")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->orderBy('supplier_id', 'desc')->get()->toArray()
        ]);
    }
    public function list_save_order_request(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('order_request as o');
            $query->select('o.order_request_unique_id',DB::raw('DATE(o.created_at) as created_att'),'o.created_by');
            $query->where([['o.status', '!=', '2']]);
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.order_request_unique_id', 'asc');
                else
                    $query->orderBy('o.order_request_unique_id', 'desc');
            }
            else
            {
                $query->orderBy('o.order_request_unique_id', 'DESC');
            }
            $query->groupBy(['order_request_unique_id','created_att','created_by']);
            $query->where([['order_request_status', '=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->created_att)) {
                    $order_date = date('d M Y', strtotime($query->created_att));
                }
                return $order_date;
            })
            ->addColumn('created_by', function ($query) {
                $created_by = "";
                $selectQty = Users::where('user_id',$query->created_by)->select('first_name','last_name')->get()->toArray();
                if(sizeof($selectQty)>0)
                {
                    $created_by = $selectQty[0]['first_name']." ".$selectQty[0]['last_name'];
                }
                return $created_by;
            })
            ->addColumn('item', function ($query) {
                $selectQty = OrderRequestDetails::where('order_request_unique_id',$query->order_request_unique_id)->sum('qty');
                return $selectQty;
            })
            ->addColumn('total_supplier', function ($query) {
                $OrderRequest = OrderRequest::where('order_request_unique_id',$query->order_request_unique_id)->get();
                $total_supplier = sizeof($OrderRequest);
                return $total_supplier;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-request-order-details" data-id="'.$query->order_request_unique_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a> <a href="javascript:void(0)" class="edit-save-request-order" data-id="'.$query->order_request_unique_id.'" title="Edit"><span class="badge badge-primary"><i class="fa fa-pencil"></i></span></a>';
                return $details;
            })
            ->rawColumns(['details', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
    public function order_request_export(){
        $query = DB::table('order_request')->select('order_request_unique_id',DB::raw('DATE(created_at) as created_att'),'created_by')->where([['status', '!=', '2'], ['order_request_status', '!=', '2']])->groupBy(['order_request_unique_id','created_att','created_by'])->orderBy('order_request_unique_id', 'DESC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order Request ID');
        $sheet->setCellValue('B1', 'Order Request Date');
        $sheet->setCellValue('C1', 'Created By');
        $sheet->setCellValue('D1', 'Items');
        $sheet->setCellValue('E1', 'Total Supplier');
        $rows = 2;
        foreach($query as $td){
            $created_name = "";
            if(!empty($td->created_by)) {
                $Users = Users::where('user_id',$td->created_by)->select('first_name','last_name')->get()->toArray();
                if(sizeof($Users)>0) {
                    $created_name = $Users[0]['first_name']." ".$Users[0]['last_name'];
                }
            }
            $items = 0;
            $total_supplier = 0;
            if(!empty($td->order_request_unique_id)) {
                $items = OrderRequestDetails::where('order_request_unique_id',$td->order_request_unique_id)->sum('qty');
                $OrderRequest = OrderRequest::where('order_request_unique_id',$td->order_request_unique_id)->get();
                $total_supplier = sizeof($OrderRequest);
            }
            $sheet->setCellValue('A' . $rows, $td->order_request_unique_id);
            $sheet->setCellValue('B' . $rows, date('d M Y', strtotime($td->created_att)));
            $sheet->setCellValue('C' . $rows, $created_name);
            $sheet->setCellValue('D' . $rows, $items);
            $sheet->setCellValue('E' . $rows, $total_supplier);
            $rows++;
        }
        $fileName = "Order_Request.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
    public function save_order_request_export(){
        $query = DB::table('order_request')->select('order_request_unique_id',DB::raw('DATE(created_at) as created_att'),'created_by')->where([['status', '!=', '2'], ['order_request_status', '=', '2']])->groupBy(['order_request_unique_id','created_att','created_by'])->orderBy('order_request_unique_id', 'DESC')->get()->toArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Order Request ID');
        $sheet->setCellValue('B1', 'Order Request Date');
        $sheet->setCellValue('C1', 'Created By');
        $sheet->setCellValue('D1', 'Items');
        $sheet->setCellValue('E1', 'Total Supplier');
        $rows = 2;
        foreach($query as $td){
            $created_name = "";
            if(!empty($td->created_by)) {
                $Users = Users::where('user_id',$td->created_by)->select('first_name','last_name')->get()->toArray();
                if(sizeof($Users)>0) {
                    $created_name = $Users[0]['first_name']." ".$Users[0]['last_name'];
                }
            }
            $items = 0;
            $total_supplier = 0;
            if(!empty($td->order_request_unique_id)) {
                $items = OrderRequestDetails::where('order_request_unique_id',$td->order_request_unique_id)->sum('qty');
                $OrderRequest = OrderRequest::where('order_request_unique_id',$td->order_request_unique_id)->get();
                $total_supplier = sizeof($OrderRequest);
            }
            $sheet->setCellValue('A' . $rows, $td->order_request_unique_id);
            $sheet->setCellValue('B' . $rows, date('d M Y', strtotime($td->created_att)));
            $sheet->setCellValue('C' . $rows, $created_name);
            $sheet->setCellValue('D' . $rows, $items);
            $sheet->setCellValue('E' . $rows, $total_supplier);
            $rows++;
        }
        $fileName = "Save_Order_Request.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save("public/export/".$fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/')."/export/".$fileName);
    }
}