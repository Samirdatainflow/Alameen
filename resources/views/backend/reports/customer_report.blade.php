@extends('backend.admin_after_login')

@section('title', 'Customer Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Customer Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!--================== 
            Report Header
        ====================-->
        @include('backend.reports.customer_report_header')
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_reg_no" placeholder="Enter Registration Number">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_customer_area" placeholder="Enter Customer Area">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_customer_region" placeholder="Enter Customer Region">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_customer_teritory" placeholder="Enter Customer Teritorry">
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="CustomerReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Registration Number</th>
                    <th>Customer Name</th>
                    <th>Customer Region</th>
                    <th>Customer Territory</th>
                    <th>Customer Area</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/customer_report.js')}}" type="text/javascript"></script>
@stop