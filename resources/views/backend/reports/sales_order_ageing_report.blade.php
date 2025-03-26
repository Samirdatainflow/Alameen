@extends('backend.admin_after_login')

@section('title', 'Sales Order Ageing Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Ageing Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter" style="display:none">
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_outstanding_date" placeholder="Select Date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_outstanding_customer">
                    <option value="">Select Customer</option>
                    @php
                    if(sizeof($ClientData) > 0 ) {
                        foreach($ClientData as $client) {
                        @endphp
                        <option value="{{$client['client_id']}}">{{$client['customer_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-outstanding-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%" id="AgeingReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Current Date</th>
                    <th>1-15 Days</th>
                    <th>16-30 Days</th>
                    <th>31-45 Days</th>
                    <th>> 45 Days</th>
                    <th>Total</th>
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