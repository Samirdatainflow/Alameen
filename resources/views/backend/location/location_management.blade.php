@extends('backend.admin_after_login')

@section('title', 'Location Management')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-location-arrow metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Location Management
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
       <table style="width: 100%;" id="location_list" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location Type</th>
                    <th>Location Functional</th>
                    <th>Location Load Type</th>
                    <th>Location Capacity</th>
                    <th>Order Index</th>
                    <th>Warehouse</th>
                    <th class="right-border-radius">ACTION</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/location.js')}}" type="text/javascript"></script>
@stop