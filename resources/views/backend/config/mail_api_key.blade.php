@extends('backend.admin_after_login')

@section('title', 'Mail Config')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-envelope metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Mail Config
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="mailApiKeyTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Host Name</th>
                    <th>User Name</th>
                    <th>Smtp Port</th>
                    <th>From Mail</th>
                    <th>From Name</th>
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
<script src="{{ URL::asset('public/backend/js/page_js/mail_api_key.js')}}" type="text/javascript"></script>
@stop