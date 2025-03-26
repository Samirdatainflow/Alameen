@extends('backend.admin_after_login')

@section('title', 'Receiving & Put Away Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Receiving & Put Away Report</div>
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
        <table style="width: 100%;" id="ReceivingPutAwayReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Courier Number</th>
                    <th>Courier Date</th>
                    <th>Vehicle Number</th>
                    <th>Number of box</th>
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