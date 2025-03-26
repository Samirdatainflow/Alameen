@extends('backend.admin_after_login')

@section('title', 'Confige Exchange Rate')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-usd metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Exchange Rate
                    </div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <table style="width: 100%;" id="ExchangeRateList" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Trading Date</th>
                    <th>Source Currency</th>
                    <th>Target Currency</th>
                    <th>Closing Rate</th>
                    <th>Average Rate</th>
                    <th class="right-border-radius">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/exchange_rate.js')}}" type="text/javascript"></script>
@stop