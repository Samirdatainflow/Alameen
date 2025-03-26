@extends('backend.admin_after_login')

@section('title', 'Category')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-certificate metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Category
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row mb-4 filter" style="display: none">
            <div class="col-md-2">
                <select class="form-control" id="filter_model">
                    <option value="">Select Model</option>
                    @if(!empty($model_data))
                        @foreach($model_data as $data)
                        <option value="{{$data['brand_id']}}">{{$data['brand_name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn-shadow btn btn-info" id="ResetFilter">Reset</a>
            </div>
        </div>
        <table style="width: 100%;" id="categoryList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Category Description</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/category.js')}}" type="text/javascript"></script>
@stop