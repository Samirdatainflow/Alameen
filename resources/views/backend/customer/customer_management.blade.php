@extends('backend.admin_after_login')

@section('title', 'Customer Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-address-card metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Customer Management
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="CustomerList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>CUSTOMER Code</th>
                    <th>CUSTOMER NAME</th>
                    <th>Customer Mobile Number</th>
                    <th>CUSTOMER EMAIL</th>
                    <th>Create Order</th>
                    <th class="right-border-radius">ACTION</th>
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
<script src="{{ URL::asset('public/backend/js/page_js/customer_management.js?v="'.$t.'"')}}" type="text/javascript"></script>
@stop