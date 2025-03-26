@extends('backend.admin_after_login')

@section('title', 'Supplier Report')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Supplier Report</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <input type="text" class="form-control" id="filter_supplier_code" placeholder="Enter Supplier Code">
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_country" data-live-search="true" title="Select Country" onchange="chnageCountry(this.value)">
                    <option value="">Select Country</option>
                    @if(!empty($CountriesData))
                        @foreach($CountriesData as $country)
                        <option value="{{$country['country_id']}}">{{$country['country_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_state" data-live-search="true" title="Select State" onchange="chnageState(this.value)">
                    <option value="">Select State</option>
                    @if(!empty($StateData))
                        @foreach($StateData as $state)
                        <option value="{{$state['state_id']}}">{{$state['state_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control selectpicker" id="filter_city" data-live-search="true" title="Select City">
                    <option value="">Select City</option>
                    @if(!empty($CitiesData))
                        @foreach($CitiesData as $city)
                        <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="filter_status" title="Select Status">
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info reset-filter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="SupplierReportList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Code</th>
                    <th>Supplier Name</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zipcode</th>
                    <th>Country</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/supplier_report.js')}}" type="text/javascript"></script>
@stop