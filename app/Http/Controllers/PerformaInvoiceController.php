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

class PerformaInvoiceController extends Controller {

    public function performa_invoice() {
        return \View::make("backend/performa_invoice/performa_invoice")->with([
            'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->orderBy('supplier_id', 'desc')->get()->toArray()
        ]);
    }
    // List
    public function list_performa_invoice(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('performa_invoice as pi');
            $query->select('pi.performa_invoice_id', 'pi.order_request_id', 'or.created_at', 's.full_name as supplier_name');
            $query->join('order_request as or', 'or.order_request_id', '=', 'pi.order_request_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'pi.supplier_id', 'left');
            $query->where([['pi.status', '!=', '2']]);
            if($keyword)
            {
                $sql = "s.full_name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('s.supplier_name', 'asc');
                else
                    $query->orderBy('pi.performa_invoice_id', 'desc');
            }
            else
            {
                $query->orderBy('pi.performa_invoice_id', 'DESC');
            }
            if(!empty($request->filter_supplier)) {
                $query->where([['pi.supplier_id', '=', $request->filter_supplier]]);
            }
            $query->where([['pi.status', '!=', '2']]);
            $datatable_array=Datatables::of($query)
            ->addColumn('order_date', function ($query) {
                $order_date = '';
                if(!empty($query->created_at)) {
                    $order_date = date('d M Y', strtotime($query->created_at));
                }
                return $order_date;
            })
            ->addColumn('details', function ($query) {
                $details = '<a href="javascript:void(0)" class="view-performa-invoice" data-id="'.$query->performa_invoice_id.'" title="View"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a>';
                return $details;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="delete-performa-invoice" data-id="'.$query->performa_invoice_id.'"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>';
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
    public function add_invoice(Request $request) {
        if ($request->ajax()) {
            $html = view('backend.performa_invoice.performa_invoice_form')->with([
                'supplier_data' => Suppliers::select('supplier_id', 'full_name')->where([['status', '=', '1']])->get()->toArray()
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
    // Save
    public function save_invoice(Request $request) {
        if ($request->ajax()) {
            $OrderRequest = OrderRequest::where([['order_request_id', '=', $request->order_request_id], ['supplier_id', '=', $request->supplier_id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($OrderRequest) > 0) {
                if(!empty($OrderRequest[0]['order_request_id'])) {
                    $OrderQuotation = OrderQuotation::select('is_confirm')->where([['order_request_id', '=', $OrderRequest[0]['order_request_id']], ['status', '=', '1']])->get()->toArray();
                    if(sizeof($OrderQuotation) > 0) {
                        if($OrderQuotation[0]['is_confirm'] == 1) {
                            $PerformaInvoice = PerformaInvoice::where([['order_request_id', '=', $OrderRequest[0]['order_request_id']], ['supplier_id', '=', $request->supplier_id],['status', '=', '1']])->get()->toArray();
                            if(sizeof($PerformaInvoice) > 0) {
                                return response()->json(["status" => 0, "msg" => "Invoice already uploaded fro this order."]);
                            }else {
                                $upimages = $request->invoice_file;
                                $new_name = rand() . '.' . $upimages->getClientOriginalExtension();
                                $upimages->move(public_path('backend/images/invoice_file/'), $new_name);
                                $data = new PerformaInvoice;
                                $data->order_request_id = $OrderRequest[0]['order_request_id'];
                                $data->supplier_id = $request->supplier_id;
                                $data->invoice = $new_name;
                                $data->status = "1";
                                $saveData = $data->save();
                                if($saveData) {
                                    return response()->json(["status" => 1, "msg" => "Save successful"]);
                                }else {
                                    return response()->json(["status" => 0, "msg" => "Save faild."]);
                                }
                            }
                        }else {
                            return response()->json(["status" => 0, "msg" => "Order is not Confirm."]);
                        }
                    }else {
                        return response()->json(["status" => 0, "msg" => "No record found."]);
                    }
                }else {
                    return response()->json(["status" => 0, "msg" => "No record found."]);
                }
            }else {
                return response()->json(["status" => 0, "msg" => "No record found."]);
            }
        }
    }
    // Delete
    public function delete_invoice(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $upData = PerformaInvoice::where('performa_invoice_id', $request->id)->update(['status' => "2"]);
            if($upData) {
                $returnData = ["status" => 1, "msg" => "Delete successful."];
            }else {
                $returnData = ["status" => 0, "msg" => "Delete failed! Something is wrong."];
            }
            return response()->json($returnData);
        }
    }
    public function view_invoice(Request $request) {
        if ($request->ajax()) {
            $returnData = [];
            $PerformaInvoice = PerformaInvoice::select('order_request_id', 'supplier_id', 'invoice')->where([['performa_invoice_id', '=', $request->id], ['status', '=', '1']])->get()->toArray();
            if(sizeof($PerformaInvoice) > 0) {
                $order_request_date = "";
                $OrderRequest = OrderRequest::select('created_at')->where([['order_request_id', '=', $PerformaInvoice[0]['order_request_id']]])->get()->toArray();
                if(!empty($OrderRequest)) {
                    $order_request_date = $OrderRequest[0]['created_at'];
                }
                $supplier_name = "";
                $Suppliers = Suppliers::select('full_name')->where([['supplier_id', '=', $PerformaInvoice[0]['supplier_id']]])->get()->toArray();
                if(!empty($Suppliers)) {
                    $supplier_name = $Suppliers[0]['full_name'];
                }
                $url = url('public/backend/images/invoice_file/');
                array_push($returnData, array('order_request_id' => $PerformaInvoice[0]['order_request_id'], 'invoice_file' => $url."/".$PerformaInvoice[0]['invoice'], 'order_request_date' => $order_request_date, 'supplier_name' => $supplier_name));
            }
            $html = view('backend.performa_invoice.performa_invoice_details')->with([
                'performa_invoice' => $returnData
            ])->render();
            return response()->json(["status" => 1, "message" => $html]);
        }
    }
}