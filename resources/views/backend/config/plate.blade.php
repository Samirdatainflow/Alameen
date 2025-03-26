@extends('backend.admin_after_login')

@section('title', 'Level')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-list metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Level</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="PlateList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Level Name</th>
                    <th>Location</th>
                    <th>Zone Name</th>
                    <th>Row Name</th>
                    <th>Rack Name</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/plate.js')}}" type="text/javascript"></script>
@stop