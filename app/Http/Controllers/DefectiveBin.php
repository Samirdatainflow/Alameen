<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use File;
use Session;
use App\Users;
use App\Expenses;
use App\Products;
use App\Suppliers;
use App\BinningLocationDetails;
use App\Location;
use DB;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DefectiveBin extends Controller {

    public function index() {
        return \View::make("backend/inventory/direct_return_bin")->with(array());
    }
    
    // dataTable
    public function direct_return_bin_list(Request $request) {
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('defective_bin_details as dbd');
            $query->select('dbd.order_id', 'p.pmpno', 'pn.part_name', 's.full_name', 'dbd.bad_quantity', 'dbd.product_id');
            $query->join('products as p', 'p.product_id', '=', 'dbd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            $query->join('suppliers as s', 's.supplier_id', '=', 'dbd.supplier_id', 'left');
            
            if($keyword)
            {
                $sql = "dbd.order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('dbd.defective_bin_details_id', 'asc');
                else
                    $query->orderBy('dbd.defective_bin_details_id', 'desc');
            }
            else
            {
                $query->orderBy('dbd.defective_bin_details_id', 'DESC');
            }
            $query->where([['dbd.status', '=', '2']]);
            $datatable_array=Datatables::of($query)
            
            ->addColumn('location_name', function ($query) {
                
                $location_name= "";
                $BLDdata = BinningLocationDetails::select('location_id')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                
                if(sizeof($BLDdata) > 0) {
                    
                    $locationData = Location::select('location_name')->where([['location_id', '=', $BLDdata[0]['location_id']]])->get()->toArray();
                    
                    if(sizeof($locationData) > 0) {
                        
                        $location_name = $locationData[0]['location_name'];
                    }
                }
                return $location_name;
            })
            // ->addColumn('part_name', function ($query) {
                
            //     $part_name= "";
            //     $ProductsData = Products::select('pmpno')->where([['product_id', '=', $query->product_id]])->get()->toArray();
                
            //     if(sizeof($ProductsData) > 0) {
            //         $part_name = $ProductsData[0]['pmpno'];
            //     }
            //     return $part_name;
            // })
            // ->addColumn('action', function ($query) {
            //     $action = '<a href="javascript:void(0)" class="edit-expenses" data-id="'.$query->expenses_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Expenses"><i class="fa fa-pencil"></i></button></a>';
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
    
    public function customerReturnBin() {
        return \View::make("backend/inventory/customer_return_bin")->with(array());
    }
    
    public function customerReturnBinList(Request $request) {
        
        if ($request->ajax()) {
            
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('defective_bin_details as dbd');
            $query->select('dbd.order_id', 'p.pmpno', 'pn.part_name', 'dbd.client_id', 'dbd.bad_quantity', 'dbd.product_id', 'dbd.supplier_id');
            $query->join('products as p', 'p.product_id', '=', 'dbd.product_id', 'left');
            $query->join('part_name as pn', 'pn.part_name_id', '=', 'p.part_name_id', 'left');
            //$query->join('clients as c', 'c.client_id', '=', 'dbd.supplier_id', 'left');
            
            if($keyword)
            {
                $sql = "dbd.order_id like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
            if($order)
            {
                if($order == "asc")
                    $query->orderBy('dbd.defective_bin_details_id', 'asc');
                else
                    $query->orderBy('dbd.defective_bin_details_id', 'desc');
            }
            else
            {
                $query->orderBy('dbd.defective_bin_details_id', 'DESC');
            }
            $query->where([['dbd.status', '=', '1']]);
            $datatable_array=Datatables::of($query)
            
            ->addColumn('customer_name', function ($query) {
                
                $customer_name = "";
                $selectCustomer = DB::table('clients')->select('customer_name')->where([['client_id', '=', $query->client_id]])->get()->toArray();
                
                if(sizeof($selectCustomer) > 0) {
                    
                    $customer_name = $selectCustomer[0]->customer_name;
                }
                
                return $customer_name;
            })
            ->addColumn('supplier_name', function ($query) {
                
                $supplier_name= "";
                $selectSupplier = DB::table('suppliers')->select('full_name')->where([['supplier_id', '=', $query->supplier_id]])->get()->toArray();
                
                if(sizeof($selectSupplier) > 0) {
                    
                    $supplier_name = $selectSupplier[0]->full_name;
                }
                
                return $supplier_name;
            })
            
            ->rawColumns([])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}