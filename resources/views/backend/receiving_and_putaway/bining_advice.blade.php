@extends('backend.admin_after_login')

@section('title', 'Bining Advice')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Bining Advice
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="ReceivingAndPutawayList" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Purchased Order ID</th>
                    {{-- <th>Warehouse</th>
                    <th>Supplier</th> --}}
                    <th>Invoice No</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Download Bining Advice</th>
                    <!-- <th>Received</th> -->
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/bining_advice.js')}}" type="text/javascript"></script>
@stop