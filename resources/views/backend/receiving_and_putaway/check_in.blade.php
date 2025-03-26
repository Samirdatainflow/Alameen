@extends('backend.admin_after_login')

@section('title', 'Check In')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Check In</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="CheckInList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Check In ID</th>
                    <th>Order No</th>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Good Quantity</th>
                    <th>Bad Quantity</th>
                    <th>Details</th>
                    <th>Barcode Scann</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
@endsection
@section('page-script')
@php
$t=time();
@endphp
<script src="{{ URL::asset('public/backend/js/page_js/check_in.js?v="'.$t.'"')}}" type="text/javascript"></script>
@stop