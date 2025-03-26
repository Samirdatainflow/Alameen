@extends('backend.admin_after_login')

@section('title', 'No of Orders By Dates')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>No of Orders By Dates</div>
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
                <a href="{{URL::to('report/sales-order-report/not-approved-orders')}}" class="btn-shadow btn btn-info">Not Approved Orders</a>
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_from_date_by_days" placeholder="Select from date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_to_date_by_days" placeholder="Select to date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-no-of-orders-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%" id="NoofOrdersByDatesList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Ordered Date</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
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