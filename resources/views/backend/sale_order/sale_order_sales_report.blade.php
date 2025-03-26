@extends('backend.admin_after_login')

@section('title', 'Sales Report')

@section('content')
<style type="text/css">
    .list-group {
    position: absolute;
    height: 250px;
    overflow-y: scroll;
    z-index: 99;
    background-color: #fff;
    }
</style>
     <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Sales Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        <!-- <button type="button" aria-haspopup="true" id="add_user" aria-expanded="false" class="btn-shadow btn btn-info">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fa fa-plus fa-w-20"></i>
                            </span>
                            Add User
                        </button> -->
                    </div>
                </div>    
            </div>
        </div>
        
        <div class="row mb-4 filter">
            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_customer">
                    <option value="">Select Customer</option>
                    @if(!empty($ClientData))
                        @foreach($ClientData as $cdata)
                        <option value="{{$cdata['client_id']}}">{{$cdata['customer_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info rest-filter">Reset</a>
            </div>
        </div>
        
        <table style="width: 100%;" id="SalesReportTable" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Customer name</th>
                <th>Outstanding Amount</th>
                <th>Partial Outstanding Amount</th>
                <th>Receipt Amount</th>
            </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
        
    </div>
    
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/sale_order_sales_report.js')}}" type="text/javascript"></script>
@stop