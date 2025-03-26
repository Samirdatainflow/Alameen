@extends('backend.admin_after_login')

@section('title', 'Zone Master')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-usd metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Storage Management <br><strong>2. Zone Name > 3. Row Name > 4. Rack Name > 5. Level Name > 6. Position</strong></div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-md-12">
                <h5><strong>2. Zone Name > 3. Row Name > 4. Rack Name > 5. Level Name > 6. Position</strong></h5>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-md-6">
                <table style="width: 100%;" id="ZoneMasterList" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Zone Name</th>
                            <th>Location Name</th>
                            <th class="right-border-radius">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table style="width: 100%;" id="Rowlist" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Row Name</th>
                            <th>Location</th>
                            <th>Zone</th>
                            
                            <th class="right-border-radius">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div><br/>
        <div class="row">
            <div class="col-md-6">
                <table style="width: 100%;" id="RackList" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Rack Name</th>
                            <th>Location</th>
                            <th>Zone</th>
                            <th>Row</th>
                            <th class="right-border-radius">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
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
        </div><br/>
        <div class="row">
            <div class="col-md-6">
                <table style="width: 100%;" id="Placelist" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>POSITION</th>
                            <th>LOCATION</th>
                            <th>ZONE</th>
                            <th>ROW</th>
                            <th>RACK</th>
                            <th>PLATE</th>
                            <th>Max Capacity</th>
                            <th class="right-border-radius">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/zone_master.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('public/backend/js/page_js/row.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('public/backend/js/page_js/rack.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('public/backend/js/page_js/plate.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('public/backend/js/page_js/place.js')}}" type="text/javascript"></script>
@stop