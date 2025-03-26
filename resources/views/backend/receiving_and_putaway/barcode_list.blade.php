@extends('backend.admin_after_login')

@section('title', 'Barcode Details')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Barcode Details</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="BarcodeList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Supplier Name</th>
                    <th>Barcode Number</th>
                    <th>Invoice No</th>
                    <th>Customer</th>
                    <th>Date Of Invoice</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/barcode.js')}}" type="text/javascript"></script>
@stop