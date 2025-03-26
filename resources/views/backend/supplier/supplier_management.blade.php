@extends('backend.admin_after_login')

@section('title', 'Supplier Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-address-book metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Supplier Management
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="supplierList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Supplier Code</th>
                    <th>Full Name</th>
                    <th>Business Title</th>
                    <th>Mobile</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zipcode</th>
                    <th>Country</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/supllier.js')}}" type="text/javascript"></script>
@stop