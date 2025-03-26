@extends('backend.admin_after_login')

@section('title', 'Good/Bad quantity in Order')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Good/Bad quantity in Order</div>
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
        <table style="width: 100%;" id="GoodBadQuantityInOrder" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Good Quanity</th>
                    <th>Bad Quanity</th>
                    <th>Supplier Name</th>
                    <th>Warehouse Name</th>
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