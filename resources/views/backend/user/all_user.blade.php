@extends('backend.admin_after_login')

@section('title', 'User')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-user metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>All User
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="user" class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Status</th>
                <th>User Type</th>
                <th>User Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
       
        </tbody>
    </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/user.js')}}" type="text/javascript"></script>
@stop