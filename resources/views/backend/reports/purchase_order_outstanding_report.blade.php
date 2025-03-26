@extends('backend.admin_after_login')

@section('title', 'Purchase Order Outstanding Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Outstanding Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_outstanding_date" placeholder="Select Date" autocomplete="off">
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_outstanding_supplier">
                    <option value="">Select Supplier</option>
                    @php
                    if(sizeof($Suppliers) > 0 ) {
                        foreach($Suppliers as $sup) {
                        @endphp
                        <option value="{{$sup['supplier_id']}}">{{$sup['full_name']}}</option>
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
        <table style="width: 100%" id="OutstandingReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Party Name</th>
                    <th>Amount</th>
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