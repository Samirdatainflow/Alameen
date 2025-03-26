@extends('backend.admin_after_login')

@section('title', 'Position')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-usd metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Position</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="Placelist" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>POSITION</th>
                    <th>LOCATION</th>
                    <th>ZONE</th>
                    <th>ROW</th>
                    <th>RACK</th>
                    <th>PLATE</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/place.js')}}" type="text/javascript"></script>
@stop