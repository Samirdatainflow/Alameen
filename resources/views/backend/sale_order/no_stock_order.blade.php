@extends('backend.admin_after_login')

@section('title', 'Sale Order Management')

@section('content')
     <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>No Stock Order
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        <!-- <button type="button" aria-haspopup="true" id="add_user" aria-expanded="false" class="btn-shadow btn btn-info">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fa fa-plus fa-w-20"></i>
                            </span>
                            Add User
                        </button> -->
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_customer" onchange="changeCategory(this.value)">
                    <option value="">Select Customer</option>
                    @if(!empty($Clients))
                        @foreach($Clients as $data)
                        <option value="{{$data['client_id']}}">{{$data['customer_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_from_date" placeholder="Select From Date">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control datepicker" id="filter_to_date" placeholder="Select To Date">
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info rest-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="sale_order" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Order Id</th>
                <th>Client Name</th>
                <th>Sponsor Name</th>
                <th>Grand Total</th>
                <th>Created On</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
        
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/no_stock_order.js')}}" type="text/javascript"></script>
@stop