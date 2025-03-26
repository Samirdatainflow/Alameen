@extends('backend.admin_after_login')

@section('title', 'Returns Management Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Returns Management Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_order_id" placeholder="Enter Order ID">
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_customer" title="Select Customer">
                    <option value="">Select Customer</option>
                    @if(!empty($customerData))
                        @foreach($customerData as $cust)
                        <option value="{{$cust['client_id']}}">{{$cust['customer_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="ReturnsManagementReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Return Type</th>
                    <th>Date Of order</th>
                    <th>Date of Return</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/returns_management_report.js')}}" type="text/javascript"></script>
@stop