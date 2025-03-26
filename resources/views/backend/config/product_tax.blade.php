@extends('backend.admin_after_login')

@section('title', 'Product Tax')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-usd metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Product Tax
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="productTaxTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Tax Name</th>
                    <th>Tax Rate</th>
                    <th>Tax Type</th>
                    <th>Tax Description</th>
                    <th>WareHouse Name</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/product_tax.js')}}" type="text/javascript"></script>
@stop