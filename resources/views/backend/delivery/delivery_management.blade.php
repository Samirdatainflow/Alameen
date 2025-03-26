@extends('backend.admin_after_login')

@section('title', 'Delivery Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-truck metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Delivery Management</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="DeliveryManagementList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Delivery ID</th>
                    <th>Shipping ID</th>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Vehicle No</th>
                    <th>Driver Name</th>
                    <th>Contact No</th>
                    <th>Vehicle In/Out Date</th>
                    <th>Courier Company</th>
                    <th>Courier Date</th>
                    <th>Courier Number</th>
                    <th>No Of Box</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/delivery_management.js')}}" type="text/javascript"></script>
@stop