@extends('backend.admin_after_login')

@section('title', 'Purchase Partial Outstanding')

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
                    <div>Partial Outstanding</div>
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
                <select class="form-control" id="partial_outstanding_filter_supplier">
                    <option value="">Select Supplier</option>
                    @if(!empty($SupplierData))
                        @foreach($SupplierData as $cdata)
                        <option value="{{$cdata['supplier_id']}}">{{$cdata['full_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info rest-partial-outstanding">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="PartialOutstandingTable" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Supplier name</th>
                <th>Invoice Date</th>
                <th>Invoice Amount</th>
                <th>Invoice Number</th>
                <th>Due amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
        
    </div>
    
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/purchase_order_outstanding.js')}}" type="text/javascript"></script>
@stop