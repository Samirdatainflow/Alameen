@extends('backend.admin_after_login')

@section('title', 'Purchase Order Invoice Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Invoice Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <select class="form-control" id="filter_invoice_supplier">
                    <option value="">Select Supplier</option>
                    @php
                    if(sizeof($SupplierData) > 0 ) {
                        foreach($SupplierData as $supp) {
                        @endphp
                        <option value="{{$supp['supplier_id']}}">{{$supp['full_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_invoice_month">
                    <option value="">Select Month</option>
                    <option value='01'>Janaury</option>
                    <option value='02'>February</option>
                    <option value='03'>March</option>
                    <option value='04'>April</option>
                    <option value='05'>May</option>
                    <option value='06'>June</option>
                    <option value='07'>July</option>
                    <option value='08'>August</option>
                    <option value='09'>September</option>
                    <option value='10'>October</option>
                    <option value='11'>November</option>
                    <option value='12'>December</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-invoice-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%" id="InvoiceReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Invoice Date</th>
                    <th>Due Days</th>
                    <th>Total</th>
                    <th>Due Amount</th>
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