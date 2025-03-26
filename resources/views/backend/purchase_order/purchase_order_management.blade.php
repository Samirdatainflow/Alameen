@extends('backend.admin_after_login')

@section('title', 'Purchase Order Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Purchase Order Management
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <select class="form-control" id="filter_supplier">
                    <option value="">Select Supplier </option>
                    @if(!empty($supplier_data))
                        @foreach($supplier_data as $data)
                        <option value="{{$data['supplier_id']}}">{{$data['full_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_part_brand" onchange="changeBrand(this.value)">
                    <option value="">Select Part Brand</option>
                    @if(!empty($PartBrand))
                        @foreach($PartBrand as $data)
                        <option value="{{$data['part_brand_id']}}">{{$data['part_brand_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info" id="ResetFilter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="PurchaseOrderList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Warehouse</th>
                    <th>Supplier</th>
                    <th>Delivery Date</th>
                    <th>Items Qty</th>
                    <th style="width:120px; text-align:center">Barcode</th>
                    <th>Details</th>
                    {{-- <th>Approved</th>
                    <th>Received</th> --}}
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/purchase_order_management.js')}}" type="text/javascript"></script>
@stop