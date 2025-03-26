@extends('backend.admin_after_login')

@section('title', 'Order Request')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Order Request</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!-- <div class="row mb-4 filter">
            <div class="col-md-2">
                <select class="form-control" id="filter_supplier">
                    <option value="">Select Supplier </option>
                    @if(!empty($supplier_data))
                        @foreach($supplier_data as $data)
                        <option value="{{$data['supplier_id']}}">{{$data['full_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info" id="ResetFilter">Reset</a>
            </div>
        </div> -->
        <table style="width: 100%;" id="OrderRequestList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Order Request ID</th>
                    <th>Order Request Date</th>
                    <th>Created By</th>
                    <th>Items</th>
                    <th>Total Supplier</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
@php
$t=time();
@endphp
<script src="{{ URL::asset('public/backend/js/page_js/order_request.js?v="'.$t.'"')}}" type="text/javascript"></script>
@stop