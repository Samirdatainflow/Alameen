<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Order Management</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{URL::asset('public/backend/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('public/backend/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('public/backend/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{URL::asset('public/backend/vendors/select2/select2.min.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('public/backend/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}" />
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{URL::asset('public/backend/css/demo_2/style.css')}}" />
    <!-- End layout styles -->
    <!-- <link rel="shortcut icon" href="{{URL::asset('public/backend/images/favicon.png')}}" /> -->
    <link rel="stylesheet" href="{{ URL::asset('public/backend/sweetalert2/css/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('public/backend/css/custom.css')}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        var base_url="{{url::to('/')}}";
    </script>
  </head>
  <body>
    <div class="container-scroller"  >
      
      <!-- partial -->
      <div class="container-fluid page-body-wrapper" style="height: 100vh">
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-12 grid-margin stretch-card mt-100">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title text-center">Order Management</h4>
                                        <p class="card-description">   </p>
                                        <form id="loginForm">
                                            <div class="position-relative form-group">
                                                <label for="exampleEmail" class="">Email</label>
                                                <input name="email" id="exampleEmail" placeholder="Enter a Email" type="email" class="form-control">
                                            </div>
                                            <div class="position-relative form-group">
                                                <label for="examplePassword" class="">Password</label>
                                                <input name="password" id="password" placeholder="Enter password" type="password" class="form-control">
                                            </div>
                                            <button type="submit" class="mt-1 btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
          </div>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{URL::asset('public/backend/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{URL::asset('public/backend/vendors/select2/select2.min.js')}}"></script>
    <script src="{{URL::asset('public/backend/vendors/typeahead.js/typeahead.bundle.min.js')}}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{URL::asset('public/backend/js/off-canvas.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/hoverable-collapse.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/misc.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/settings.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/todolist.js')}}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{URL::asset('public/backend/js/file-upload.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/typeahead.js')}}"></script>
    <script src="{{URL::asset('public/backend/js/select2.js')}}"></script>
    
    <script src="{{ URL::asset('public/backend/page_js/auth_script.js')}}" type="text/javascript"></script>
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="{{ URL::asset('public/backend/sweetalert2/js/sweetalert2.all.min.js')}}"></script>
    <!-- End custom js for this page -->
  </body>
</html>