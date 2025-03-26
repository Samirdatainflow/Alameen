@extends('backend.admin_after_login')

@section('title', 'Direct Return Bin')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-list-ol metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Direct Return Bin</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
        	
            <!--<div class="col-md-2">-->
            <!--    <input type="text" class="form-control" id="filter_part_no" placeholder="Enter Part No">-->
            <!--</div>-->
            <!--<div class="col-md-2">-->
            <!--    <a href="{{URL::to('inventory-management')}}" class="btn-shadow btn btn-info">Reset</a>-->
            <!--</div>-->
        </div>
        <table style="width: 100%;" id="DirectReturnBinList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Part Number</th>
                    <th>Part Name</th>
                    <th>Purchase Order Id</th>
                    <th>Supplier Name</th>
                    <th>Qty</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
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
<script src="{{ URL::asset('public/backend/js/page_js/defective_bin.js')}}" type="text/javascript"></script>
@stop