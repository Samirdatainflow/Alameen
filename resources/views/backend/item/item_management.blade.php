@extends('backend.admin_after_login')

@section('title', 'Item Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-certificate metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Item Management
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
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
            <div class="col-md-2 mb-3">
                <input type="text" class="form-control" id="filter_part_no" placeholder="Enter Part No">
            </div>
        	<div class="col-md-2 mb-3">
                <select class="form-control" id="filter_part_name" onchange="changeCategory(this.value)">
                    <option value="">Select Part Name</option>
                    @if(!empty($PartName))
                        @foreach($PartName as $data)
                        <option value="{{$data['part_name_id']}}">{{$data['part_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_car_manufacture">
                    <option value="">Select Car Manufacture</option>
                    @if(!empty($car_manufacture))
                        @foreach($car_manufacture as $data)
                        <option value="{{$data['car_manufacture_id']}}">{{$data['car_manufacture']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>


            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_car_model">
                    <option value="">Select Car Model</option>
                    @if(!empty($car_model))
                        @foreach($car_model as $data)
                        <option value="{{$data['brand_id']}}">{{$data['brand_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-3" style="display: none">
                <div class="position-relative form-group">
                    <select class="form-control" id="filter_from_year">
                        <option value="">Select From Year</option>
                        @php
                        $fromYear=(int)date('Y');
                        $toYear = "2000";
                        for(; $fromYear >= $toYear; $fromYear--) {
                            $sel = "";
                            if(!empty($item_data[0]['from_year']))  {
                                if($item_data[0]['from_year'] == $fromYear) $sel = 'selected="selected"';
                            }
                        @endphp
                        <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                        @php
                        }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-md-2 mb-3" style="display: none">
                <div class="position-relative form-group">
                    <select class="form-control" id="filter_from_month">
                        <option value="">Select From Month</option>
                        @php
                        for($i = 0; $i <= 12; ++$i) {
                            $time = strtotime(sprintf('+%d months', $i));
                            $Monthdecimalvalue = date('m', $time);
                            $MonthName = date('F', $time);
                            $sel = "";
                            if(!empty($item_data[0]['from_month']))  {
                                if($item_data[0]['from_month'] == $Monthdecimalvalue) $sel = 'selected="selected"';
                            }
                            printf('<option value="%s" '.$sel.'>%s</option>', $Monthdecimalvalue, $MonthName);
                        }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-md-2 mb-3" style="display: none">
                <div class="position-relative form-group">
                    <select class="form-control" id="filter_to_year">
                        <option value="">Select To Year</option>
                        @php
                        $fromYear = date("Y",strtotime("+4 year"));
                        $toYear = "2000";
                        for(; $fromYear >= $toYear; $fromYear--) {
                            $sel = "";
                            if(!empty($item_data[0]['to_year']))  {
                                if($item_data[0]['to_year'] == $fromYear) $sel = 'selected="selected"';
                            }
                        @endphp
                        <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                        @php
                        }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-md-2 mb-3" style="display: none">
                <div class="position-relative form-group">
                    <select class="form-control" id="filter_to_month">
                        <option value="">Select To Month</option>
                        @php
                        for($i = 0; $i <= 12; ++$i) {
                            $time = strtotime(sprintf('+%d months', $i));
                            $Monthdecimalvalue = date('m', $time);
                            $MonthName = date('F', $time);
                            $sel = "";
                            if(!empty($item_data[0]['to_month']))  {
                                if($item_data[0]['to_month'] == $Monthdecimalvalue) $sel = 'selected="selected"';
                            }
                        printf('<option value="%s" '.$sel.'>%s</option>', $Monthdecimalvalue, $MonthName);
                        }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <select class="form-control" id="filter_category" onchange="changeCategory(this.value)">
                    <option value="">Select Category</option>
                    @if(!empty($product_categoriy_data))
                        @foreach($product_categoriy_data as $data)
                        <option value="{{$data['category_id']}}">{{$data['category_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <select class="form-control list-sub-category" id="filter_sub_category">
                    <option value="">Select Sub Category</option>
                </select>
            </div>
            
            
            <!--<div class="col-md-2 mb-3" >-->
            <!--    <input type="text" class="form-control" id="engine_no" placeholder="Enter Engine No">-->
            <!--</div>-->
            <div class="col-md-2 mb-3" >
                <input type="text" class="form-control" id="chassis" placeholder="Enter Chassis/Model">
            </div>
            <div class="col-md-2 mb-3" >
                <input type="text" class="form-control" id="manufacturer_no" placeholder="Enter Manufacturer No">
            </div>
            <div class="col-md-2 mb-3" >
                <input type="text" class="form-control" id="alternate_part_no" placeholder="Enter Alternate Part No">
            </div>
            <div class="col-md-2 mb-3" style="display: none;">
                <select class="form-control" id="filter_units">
                    <option value="">Select Units</option>
                    @if(!empty($unit_data))
                        @foreach($unit_data as $data)
                        <option value="{{$data['unit_id']}}">{{$data['unit_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info" id="ResetFilter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="ItemList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>item Id</th>
                    <th>PART NO</th>
                    <th>Alternate PART NO</th>
                    <th>PRODUCT NAME</th>
                    <th>UNIT</th>
                    <th>CATEGORY</th>
                    <th>ALERT</th>
                    <th>QTY ON HAND</th>
                    <th>LC Price</th>
                    <th>SELLING PRICE</th>
                    <!-- <th>STATUS</th> -->
                    <th class="right-border-radius">ACTION</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        {{-- <div class="row mb-4">
            <input name="tags" id="input-tags" style="width:500px !important" />
        </div> --}}
    </div>
@endsection
@section('page-script')
@php
$t=time();
@endphp
<script src="{{ URL::asset('public/backend/js/page_js/item_management.js?v="'.$t.'"')}}" type="text/javascript"></script>
@stop