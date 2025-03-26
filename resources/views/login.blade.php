@extends('app_login')

@section('title', 'Warehouse Login')

@section('content')
<!-- style="background-image: url('{{URL::asset('public/backend/images/warehouse_background.jpg')}}');" -->
    <div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar bg-white" >
        <div class="app-main" style="padding-top: 0px">
            <div class="app-main__inner" style="padding-bottom: 30px">
                <div class="row">
                    <div class="col-md-6 height-93vh display-table">
                        <div class="row display-table-cell" >
                            <div class="col-md-6 offset-md-3" >
                                {{Form::open(array('id'=>'loginForm'))}}
                                    <p class="text-center"><img src="{{URL::asset('public/backend/images/pro-biz black.png')}}" class="login-logo"></p>
                                    <h4 class="text-center mb-4">Sign in to Your Account</h4>
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail" class="">Email Address</label>
                                        <input name="email" id="exampleEmail" placeholder="Enter a Email" type="email" class="form-control">
                                    </div>
                                    <div class="position-relative form-group">
                                        <label for="examplePassword" class="">Password</label>
                                        <input name="password" id="password" placeholder="Enter password" type="password" class="form-control">
                                    </div>
                                    <div class="position-relative form-group">
                                        <label for="examplePassword" class=""><input name="remember_me" id="remember_me"  type="checkbox"> Remember me</label>
                                        
                                    </div>
                                    <button type="submit" class="mt-1 btn btn-primary submit-btn">Login</button>
                                {{Form::close()}}
                                <p class="text-center forget-password "><a href="#">Forgot Your Password</a></p>
                        </div>
                        </div>
                        <div class="row display-table-footer-group">
                            <div class="col-md-12">
                                <p class="copy-right text-center mb-0">Copyright {{date('Y')}} WMS - All Rights Reserved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 height-93vh">
                        <div class="login-design height-93vh" style="background-image: url('{{URL::asset('public/backend/images/warehouse_background.jpg')}}');"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection