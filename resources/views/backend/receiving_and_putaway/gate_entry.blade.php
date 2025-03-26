@extends('backend.admin_after_login')

@section('title', 'Gate Entry')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Gate Entry</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="GateEntryList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Transaction Type</th>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Vehicle No</th>
                    <th>Driver Name</th>
                    <th>Contact No</th>
                    <th>Vehicle In/Out Date</th>
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
<script src="{{ URL::asset('public/backend/js/page_js/gate_entry.js')}}" type="text/javascript"></script>
@stop