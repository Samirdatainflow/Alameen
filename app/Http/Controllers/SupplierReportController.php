<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Session;
use App\Users;
use App\Countries;
use App\State;
use App\Cities;
use DB;
use DataTables;

class SupplierReportController extends Controller {
    public function index() {
        return \View::make("backend/reports/supplier_report")->with([
            'CountriesData' => Countries::select('country_id', 'country_name')->where([['status','!=',1]])->orderBy('country_name', 'ASC')->get()->toArray(),
            'StateData' => State::select('state_id', 'state_name')->where([['status','!=',1]])->orderBy('state_name', 'ASC')->get()->toArray(),
            'CitiesData' => Cities::select('city_id', 'city_name')->where([['status','!=',1]])->orderBy('city_name', 'ASC')->get()->toArray(),
        ]);
    }
    public function supplier_report_list(Request $request) {
        if ($request->ajax()) {
            $order = $request->input('order.0.dir');
            $keyword = $request->input('search.value');
            $query = DB::table('suppliers as s');
            //$query->select('supplier_id','supplier_code', 'full_name', 'business_title', 'mobile', 'phone', 'address', 'city_id', 'state_id', 'zipcode', 'country_id', 'email', 'status');
            $query->select('s.*', 'c.country_name as country', 'st.state_name as state', 'ci.city_name as city');
            $query->join('countries as c', 'c.country_id', '=', 's.country_id', 'left');
            $query->join('state as st', 'st.state_id', '=', 's.state_id', 'left');
            $query->join('cities as ci', 'ci.city_id', '=', 's.city_id', 'left');
            if($keyword) {
                $query->whereRaw("(s.full_name like '%$keyword%' or s.business_title like '%$keyword%' or ci.city_name like '%$keyword%' or st.state_name like '%$keyword%' or c.country_name)");
            }
            if($order) {
                if($order == "asc")
                    $query->orderBy('s.supplier_id', 'desc');
                else
                    $query->orderBy('s.supplier_id', 'desc');
            } else {
                $query->orderBy('s.supplier_id', 'DESC');
            }
            $query->where([['s.status', '!=', '2']]);
            if(!empty($request->filter_supplier_code)) {
                $query->where('s.supplier_code', 'like', '%' . $request->filter_supplier_code . '%');
            }
            if(!empty($request->filter_country)) {
                $query->where('s.country_id', $request->filter_country);
            }
            if(!empty($request->filter_state)) {
                $query->where('s.state_id', $request->filter_state);
            }
            if($request->filter_status != '') {
                $query->where([['s.status', '=', $request->filter_status]]);
            }
            $datatable_array=Datatables::of($query)
            ->addColumn('status', function ($query) {
                $status = '';
                if(!empty($query->status)) {
                    // if($query->status == "1") {
                        $status .= '<a href="javascript:void(0)" class="supplier-change-status" data-id="'.$query->supplier_id.'" data-status="0"><span class="badge badge-success">Active</span></a>';
                    }else {
                        $status .= '<a href="javascript:void(0)" class="supplier-change-status" data-id="'.$query->supplier_id.'" data-status="1"><span class="badge badge-danger">Inactive</span></a>';
                    }
                // }
                return $status;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:void(0)" class="edit-supplier" data-id="'.$query->supplier_id.'"><button type="button" class="btn btn-primary btn-sm" title="Edit Supplier"><i class="fa fa-pencil"></i></button></a> <a href="javascript:void(0)" class="delete-supplier" data-id="'.$query->supplier_id.'"><button type="button" class="btn btn-danger btn-sm" title="Edit Supplier"><i class="fa fa-trash"></i></button></a>';
                return $action;
            })
            ->rawColumns(['status', 'action'])
            ->make();
            $data=(array)$datatable_array->getData();
            $data['page']=($_POST['start']/$_POST['length'])+1;
            $data['totalPage']=ceil($data['recordsFiltered']/$_POST['length']);
            return $data;
        }
    }
}