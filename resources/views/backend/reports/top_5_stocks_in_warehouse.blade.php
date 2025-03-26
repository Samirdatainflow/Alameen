@extends('backend.admin_after_login')

@section('title', 'Top 5 Stocks in Warehouse')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Top 5 Stocks in Warehouse</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="Top5StocksInWarehouse" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Warehouse</th>
                    <th>Product ID</th>
                    <th>Part No</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/stock_management_report.js')}}" type="text/javascript"></script>
@stop