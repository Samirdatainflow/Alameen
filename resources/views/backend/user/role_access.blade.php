@extends('backend.admin_after_login')

@section('title', 'Role Access')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-user metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Role Access
                    </div>
                </div>
            </div>
        </div>
       
        <table style="width: 100%;" id="roleAccessList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Warehouse</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/user_role_access.js')}}" type="text/javascript"></script>
@stop