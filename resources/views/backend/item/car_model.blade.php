@extends('backend.admin_after_login')

@section('title', 'Car Model')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-certificate metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Car Model</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter">
            <div class="col-md-2">
                <select class="form-control" id="filter_car_manufacture">
                    <option value="">Select Car Manufacture</option>
                    @if(!empty($CarManufacture))
                        @foreach($CarManufacture as $data)
                        <option value="{{$data['car_manufacture_id']}}">{{$data['car_manufacture']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info" id="ResetFilter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="CarModelList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Model Name</th>
                    <th>Car Manufacture</th>
                    <th>Status</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/car_model.js')}}" type="text/javascript"></script>
@stop