<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Profile Edit</title>
    <!-- STYLESHEETS -->
    @include('layout.stylesheets')
    <!-- STYLESHEETS -->
    <style>
      input::-webkit-outer-spin-button,
      input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      /* Firefox */
      input[type=number] {
        -moz-appearance: textfield;
      }
      .stock,.row_total{
        vertical-align: middle !important;
      }
      .c_pass{
        position: absolute;
        right: 33px;
      }
      .submit_b{
        width: 206px;
      }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_horizontal-navbar.html -->
      <!-- HEADER -->
      @include('layout.header')
      <!-- HEADER -->
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
          <div class="content-wrapper pb-0">
              <div class="page-header">
                <h3 class="page-title">Update Profile</h3>
                <nav aria-label="breadcrumb">
                </nav>
              </div>
              {{Form::open(array('id'=>'Update_profile'))}}
              <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="container">
                          <div class="row">
                            <!-- <div class="col-md-5"> -->
                              <button class="btn btn-primary bg-blue c_pass" type="button" id="PasswordChangeForm">Change Password</button>
                            <!-- </div> -->
                          </div><br>
                          <!-- <h5>Personal Information</h5> -->
                          <div class="row mt-20">
                            <div class="col-md-6">
                              <label>Customer Name</label>
                              @foreach($users_data as $user)
                              <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ $user->customer_name }}" required="required">
                              
                            </div>
                            <div class="col-md-6">
                              <label>Sponsor Name</label>
                              <input type="text" name="sponsor_name" id="sponsor_name" class="form-control" value="{{ $user->sponsor_name }}" required="required">
                            </div>
                          </div>
                          <!-- <div class="row mt-20">
                            <div class="col-md-4">
                              <label>Gender</label>
                              <select class="form-control">
                                <option disabled="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label>Date of Birth</label>
                              <input type="date" name="d_o_b" class="form-control" value="{{ $user->date_of_birth }}">
                            </div>
                            <div class="col-md-4">
                              <label>Mobile No</label>
                              <input type="text" name="mobile_no" class="form-control" value="{{ $user->mobile }}">
                            </div>
                          </div> -->
                          <!-- <div class="row mt-20">
                            <div class="col-md-4">
                              <label>Work Phone Mumber</label>
                              <input type="number" name="w_p_number" class="form-control" value="{{ $user->phone }}">
                            </div>
                            <div class="col-md-8">
                              <label>Address</label>
                              <input type="text" name="address" class="form-control" value="{{ $user->address }}">
                            </div>
                          </div> -->
                        </div>
                      </div><br>
                      <hr>
                      <div class="row">
                        <div class="container">
                          <!-- <h5>Account Information</h5> -->
                          <div class="row mt-20">
                            <div class="col-md-6">
                              <label>Customer Email</label>
                              <input type="text" name="Name" class="form-control" value="{{ $user->customer_email_id }}" disabled="">
                            </div>
                            <div class="col-md-6">
                              <!-- <label>User Name</label>
                              <input type="text" name="Name" class="form-control" value="{{ $user->username }}" disabled=""> -->
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="container">
                          <div class="row mt-20">
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                              <button class="btn btn-primary bg-blue submit_b">Submit</button>
                            </div>
                            <div class="col-md-4">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            {{Form::close()}}
            
          </div>
          <!-- FOOTER -->
          @include('layout.footer')
          <!-- FOOTER -->
        </div>
      </div>
    </div>
    <div class="modal" tabindex="-1" id="passwordChangeModal" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{Form::open(array('id'=>'update_password'))}}
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" id="current_password" class="form-control">
                <p style="color: red;display: none" id="current_pass_wrong">Password is worng</p>
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control">
              </div>
              <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="submit" class="btn bg-blue text-white">Update Password</button>
            </div>
          </div>
          {{Form::close()}}
        </div>
      </div>
    </div>
  </div>
   <!-- SCRIPTS -->
   @include('layout.scripts')
   <script src="{{URL::to('public/backend/js/page/profile.js')}}" type="text/javascript"></script>
   <!-- SCRIPTS -->
  </body>
</html>