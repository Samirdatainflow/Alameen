@extends('backend.admin_after_login')

@section('title', 'Retuns ')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-retweet metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>List of Returns</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="returnTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Return ID</th>
                    <th>Return Data</th>
                    <th>Return Type</th>
                    <th>Delivery Id</th>
                    <th>Order Id</th>
                    <th>Form</th>
                    <th>Action</th>
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
<script src="{{ URL::asset('public/backend/js/page_js/returns_management.js?v="'.$t.'"')}}" type="text/javascript"></script>
@stop
