@extends('backend.admin_after_login')

@section('title', 'Product Rate')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-usd metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Product Rate
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="productRateTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Default Rate</th>
                    <th>Level 1</th>
                    <th>Level 2</th>
                    <th>Level 3</th>
                    <th>Level 4</th>
                    <th>Level 5</th>
                    <th>Warehouse Name</th>
                    <th>Product Name</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/product_rate.js')}}" type="text/javascript"></script>

@stop