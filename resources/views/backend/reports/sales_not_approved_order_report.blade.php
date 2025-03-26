@extends('backend.admin_after_login')

@section('title', 'Not Approved Orders')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Not Approved Orders</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-12">
                <a href="{{URL::to('report/sales-order-report/approved-orders')}}" class="btn-shadow btn btn-info">Approved Orders</a>
                <a href="{{URL::to('report/sales-order-report/no-of-orders-by-dates')}}" class="btn-shadow btn btn-info">No of Orders By Dates</a>
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control" id="not_approved_filter_area" placeholder="Enter Area">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="not_approved_filter_territory" placeholder="Enter Territory">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="not_approved_filter_region" placeholder="Enter Region">
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-not-approved-orders-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%" id="NotApprovedOrdersList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Ordered Date</th>
                    <th>Registration Number</th>
                    <th>Customer Name</th>
                    <th>Area</th>
                    <th>Territory</th>
                    <th>Region</th>
                    <th>Reason of Rejection</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/sales_order_report.js')}}" type="text/javascript"></script>
@stop