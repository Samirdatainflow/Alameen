@extends('backend.admin_after_login')

@section('title', 'Top 5 High Damage Supplier Product')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Top 5 High Damage Supplier Product</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!--================== 
            Inventory Header
        ====================-->
        @include('backend.reports.inventory_header')
        <div class="row mb-4 filter" style="display:none">
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_part_no" placeholder="Enter Part No">
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_part_name" onchange="changeCategory(this.value)">
                    <option value="">Select Part Name</option>
                    @if(!empty($PartName))
                        @foreach($PartName as $data)
                        <option value="{{$data['part_name_id']}}">{{$data['part_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_category">
                    <option value="">Select Category</option>
                    @if(!empty($product_categoriy_data))
                        @foreach($product_categoriy_data as $data)
                        <option value="{{$data['category_id']}}">{{$data['category_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_supplier" multiple data-live-search="true" title="Select Supplier">
                    @if(!empty($Suppliers))
                        @foreach($Suppliers as $sdata)
                        <option value="{{$sdata['supplier_id']}}">{{$sdata['full_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_warehouse" multiple data-live-search="true" title="Select Warehouse">
                    @if(!empty($Warehouses))
                        @foreach($Warehouses as $wdata)
                        <option value="{{$wdata['warehouse_id']}}">{{$wdata['name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="Top5HighDamageSupplierProduct" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Damaged Quantity</th>
                    <th>Product ID</th>
                    <th>Part No.</th>
                    <th>Product Name</th>
                    <th>Warehouse Name</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/inventory_report.js')}}" type="text/javascript"></script>
@stop