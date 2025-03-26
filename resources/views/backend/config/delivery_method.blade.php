@extends('backend.admin_after_login')

@section('title', 'Delivery Method')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Delivery Method
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="DeliveryMethodList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Description</th>
                    <th>Warehouse</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/delivery_method.js')}}" type="text/javascript"></script>
@stop