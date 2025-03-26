@extends('backend.admin_after_login')

@section('title', 'Binning Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Binning Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!--================== 
            Receiving & Put Away Header
        ====================-->
        @include('backend.reports.receiving_put_away_header')

        <div class="row mb-4 filter">
            <div class="col-md-2">
                <select class="form-control" id="filter_user">
                    <option value="">Select Binner Name</option>
                    @php
                    if(!empty($Users)) {
                        foreach($Users as $udata) {
                        @endphp
                        <option value="{{$udata['user_id']}}">{{$udata['first_name']}} {{$udata['last_name']}}</option>
                        @php
                        }
                    }
                    @endphp
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_status">
                    <option value="">Select Status</option>
                    @php
                    $StatusArrayData = array('Processing' => 0, 'Completed' => 1);
                    foreach($StatusArrayData as $k=>$v) {
                        @endphp
                        <option value="{{$v}}">{{$k}}</option>
                        @php
                    }
                    @endphp
                </select>
            </div>
            <!-- <div class="col-md-2">
                <input type="text" class="form-control" id="filter_customer_region" placeholder="Enter Customer Region">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_customer_teritory" placeholder="Enter Customer Teritorry">
            </div> -->
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="BinningReport" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Date</th>
                    <th>Binner Name</th>
                    <th>Items Quantity</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/receiving_put_away_report.js')}}" type="text/javascript"></script>
@stop