@extends('backend.admin_after_login')

@section('title', 'All Warehouse')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-building metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Warehouse Management</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <!-- <div class="main-card mb-3 card">
            <div class="card-body">
                <p class="mb-0">Please select your warehouse !!</p>
            </div>
        </div> -->
        <table  id="warehouseTable" class="table table-hover table-bordered"  cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                    <th>Manager</th>
                    <th>Manager Contact</th>
                    <th>Surface Area (m<sup>2</sup>)</th>
                    <th>Free Zone (m<sup>3</sup>)</th>
                    <th>Volume (m<sup>3</sup>)</th>
                    <th>Select Warehouse</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/warehouse.js')}}" type="text/javascript"></script>
@stop