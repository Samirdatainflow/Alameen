<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use DB;
use DataTables;
use App\Categories;
use App\ProductCategories;
class ItemController extends Controller {
    public function item() {

        $product_categories=ProductCategories::get();
        return \View::make("backend/item/item")->with(array('product_categories'=>$product_categories));
    }
    public function get_item(Request $request){
        if ($request->ajax()) {
            $query = DB::table('products');
            $query->join('suppliers', 'products.supplier_id', '=', 'suppliers.supplier_id');
            $query->join('product_categories', 'products.category_id', '=', 'product_categories.category_id');
            $query->join('warehouses', 'products.warehouse_id', '=', 'warehouses.warehouse_id');
            $query->select('products.product_id','products.product_manual_id', 'products.product_name','products.product_unit','products.category_id', 'suppliers.full_name as supplier_name', 'product_categories.category_name', 'warehouses.name as warehouse_name');
            
            if(!empty($request->product_name)) {
                $query->where('products.product_name', 'like', '%' . $request->product_name . '%');
            }
            if(!empty($request->category_id)) {
                $query->where('products.category_id', '=', $request->category_id );
            }
            $query->orderBy('product_id', 'DESC');
            $query->get();
            $datatable_array=Datatables::of($query)
                ->addColumn('product_id', function ($query) {
                    $product_id = '';
                    if(!empty($query->product_manual_id)) {
                        $product_id .= $query->product_manual_id;
                    }
                    return $product_id;
                })
                ->addColumn('name', function ($query) {
                    $product_name = '';
                    if(!empty($query->product_name)) {
                        $product_name .= $query->product_name;
                    }
                    return $product_name;
                })
                ->addColumn('supplier', function ($query) {
                    $supplier_name = '';
                    if(!empty($query->supplier_name)) {
                        $supplier_name .= $query->supplier_name;
                    }
                    return $supplier_name;
                })
                ->addColumn('unit', function ($query) {
                    $product_unit = '';
                    if(!empty($query->product_unit)) {
                        $product_unit .= $query->product_unit;
                    }
                    return $product_unit;
                })
                ->addColumn('category', function ($query) {
                    $category_name = '';
                    if(!empty($query->category_name)) {
                        $category_name .= $query->category_name;
                    }
                    return $category_name;
                })
                ->addColumn('warehouse', function ($query) {
                    $warehouse_name = '';
                    if(!empty($query->warehouse_name)) {
                        $warehouse_name .= $query->warehouse_name;
                    }
                    return $warehouse_name;
                })
                // ->addColumn('actions', function ($query) {
                //     $actions = '';
                //     $actions .= '<a data-brand_id="' . $query->id . '" href="javascript:void(0);" name="button" class="view-subbrand btn btn-success action-btn" title="view sub brands"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                //     $actions .= '<a data-brand_id="' . $query->id . '" href="javascript:void(0);" name="button" class="edit-brand btn btn-info action-btn"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                //     //$actions .= '<a data-regions_id="' . $query->id . '" href="javascript:void(0);" name="button" class="trash-regions btn btn-danger action-btn"><i class="fa fa-trash"></i></a>';
                //     return $actions;
                // })
                ->rawColumns(['product_id', 'name', 'supplier', 'unit', 'category', 'warehouse'])
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