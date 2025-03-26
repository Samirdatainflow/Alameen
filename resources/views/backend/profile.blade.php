@extends('backend.admin_after_login')

@section('title', 'Profile')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-user metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Profile</div>
                </div>
                <div class="page-title-actions">
                    <div class="d-inline-block">
                        
                    </div>
                </div>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                        {{ Form::open(array('id'=>'ProfileForm')) }}
                            <h3 class="text-center" style="margin-top:15px"> Update Profile</h3>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label>Email *</label>
                                        @php
                                        $emial = "";
                                        if(!empty($UserData)) {
                                        $emial = $UserData[0]['email'];
                                        }
                                        $hidden_id = Session::get('user_id');
                                        @endphp
                                        <input name="email" id="email" placeholder="" type="text" class="form-control" value="{{$emial}}">
                                         <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label>New Password *</label>
                                        <input name="password" id="password" placeholder="" type="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label>Re-Enter Password *</label>
                                        <input name="conpassword" id="conpassword" placeholder="" type="password" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <p class="text-right">
                                <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button>
                            </p>
                        {{ Form::close() }}
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
<script src="{{ URL::asset('public/backend/js/page_js/profile.js')}}" type="text/javascript"></script>
@stop
