@extends('backend.admin_after_login')

@section('title', 'Stock Management Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Stock Management Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-12">
                <a href="{{URL::to('report/stock-management-report/top-5-stocks-in-warehouse')}}" class="btn-shadow btn btn-info">Top 5 Stocks in Warehouse</a>
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_part_no" placeholder="Enter Part no">
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_warehouse">
                    <option value="">Select Warehouse</option>
                    @php
                    if(!empty($Warehouses)) {
                        foreach($Warehouses as $wdata) {
                        @endphp
                        <option value="{{$wdata['warehouse_id']}}">{{$wdata['name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_from_date" placeholder="Select from date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_to_date" placeholder="Select to date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="StockManagementReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Part No</th>
                    <th>Product Name</th>
                    <th>Warehouse</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/stock_management_report.js')}}" type="text/javascript"></script>
@stop