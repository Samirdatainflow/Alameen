@extends('backend.admin_after_login')

@section('title', 'Customer Report By Inventory')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Customer Report By Inventory</div>
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
        <table style="width: 100%;" id="CustomerReportByInventoryList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Registration Number</th>
                    <th>Customer Name</th>
                    <th>Product ID</th>
                    <th>Part No</th>
                    <th>Product Name</th>
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