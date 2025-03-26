@extends('backend.admin_after_login')

@section('title', 'Sale Order Management')

@section('content')
<style type="text/css">
    .list-group {
    position: absolute;
    height: 250px;
    overflow-y: scroll;
    z-index: 99;
    background-color: #fff;
    }
</style>
     <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Save Order
                        
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        <!-- <button type="button" aria-haspopup="true" id="add_user" aria-expanded="false" class="btn-shadow btn btn-info">
                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                <i class="fa fa-plus fa-w-20"></i>
                            </span>
                            Add User
                        </button> -->
                    </div>
                </div>    
            </div>
        </div>

        <table style="width: 100%;" id="save_order" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Order Id</th>
                <th>Client Name</th>
                <th>Sponsor Name</th>
                <th>Grand Total</th>
                <th>Created On</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
        
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/save_order.js')}}" type="text/javascript"></script>
@stop