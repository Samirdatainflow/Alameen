@extends('backend.admin_after_login')

@section('title', 'Binning Task')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Binning Task</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="BinningTaskList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Assigned Employee</th>
                    <th>Status</th>
                    <th>Closed Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/binning_task.js')}}" type="text/javascript"></script>
@stop