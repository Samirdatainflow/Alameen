@extends('backend.admin_after_login')

@section('title', 'Reporting Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Reporting Management
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!--<div class="row">-->
        <!--    <div class="col-md-12 text-right mb-3">-->
        <!--        <button type="button" aria-haspopup="true" id="addReporting" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new">-->
        <!--            <span class="btn-icon-wrapper pr-2 opacity-7">-->
        <!--                <i class="fa fa-plus fa-w-20"></i>-->
        <!--            </span>-->
        <!--            Add Reporting -->
        <!--        </button>-->
        <!--    </div>-->
        <!--</div>-->
        <table style="width: 100%;" id="ReportingList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Reporting Manager Name</th>
                    <th>Reporting Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/reporting_management.js')}}" type="text/javascript"></script>
@stop