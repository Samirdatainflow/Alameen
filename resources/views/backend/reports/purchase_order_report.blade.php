@extends('backend.admin_after_login')

@section('title', 'Purchase Order Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Purchase Order Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_from_date" placeholder="Select from date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_to_date" placeholder="Select to date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_supplier" multiple data-live-search="true" title="Select Supplier">
                    @if(!empty($Suppliers))
                        @foreach($Suppliers as $sdata)
                        <option value="{{$sdata['supplier_id']}}">{{$sdata['full_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_warehouse" multiple data-live-search="true" title="Select Warehouse">
                    @if(!empty($Warehouses))
                        @foreach($Warehouses as $wdata)
                        <option value="{{$wdata['warehouse_id']}}">{{$wdata['name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="PurchaseOrderReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Warehouse</th>
                    <th>Supplier Name</th>
                    <th>Items Quantity</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/purchase_order_report.js')}}" type="text/javascript"></script>
@stop