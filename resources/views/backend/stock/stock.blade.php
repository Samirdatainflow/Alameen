@extends('backend.admin_after_login')

@section('title', 'Stock')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-cubes metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Stock
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="StockTable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Product Id</th>
                    <th>Part No.</th>
                    <th>Product Name</th>
                    <th>Warehouse Name</th>
                    <th>Lot Name</th>
                    <th>Quantity</th>
                    {{-- <th>Date</th> --}}
                    {{-- <th class="right-border-radius">Action</th> --}}
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/stock.js')}}" type="text/javascript"></script>
@stop