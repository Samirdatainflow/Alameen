@extends('backend.admin_after_login')

@section('title', 'Inventory Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-list-ol metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Inventory Management
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
        		<select name="stock_status" class="form-control" id="stock_status">
        			<option value="">Select Stock Status</option>
        			<option value="l">Low Stock</option>
        			<option value="o">Out of Stock</option>
        		</select>
        	</div>
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
                <select class="form-control" id="filter_units">
                    <option value="">Select Units</option>
                    @if(!empty($unit_data))
                        @foreach($unit_data as $data)
                        <option value="{{$data['unit_id']}}">{{$data['unit_name']}}</option>
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
                <a href="{{URL::to('inventory-management')}}" class="btn-shadow btn btn-info">Reset</a>
            </div>
        </div>
        <div class="InventoryfixTableHead">
            <table style="width: 100%;" id="InventoryList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Part No</th>
                    <th>Alternate Part No</th>
                    <th>Product Name</th>
                    <th>UNIT</th>
                    <th>CATEGORY</th>
                    <th>ALERT</th>
                    <th>QTY ON HAND</th>
                    <th>COST</th>
                    <th>SELLING PRICE</th>
                    <th>Transit Quantity</th>
                    <th>Damage Quantity</th>
                    <th>STATUS</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
@endsection
@section('page-script')
@if(isset($_GET['stock_status']))
<script>
    @if($_GET['stock_status'] == "l")
    $("#stock_status").val("l");
    @elseif($_GET['stock_status'] == "o")
    $("#stock_status").val("o");
    @else
    window.location.href="{{URL::to('inventory-management')}}";
    @endif
</script>
@endif
<script src="{{ URL::asset('public/backend/js/page_js/inventory_management.js')}}" type="text/javascript"></script>
@stop