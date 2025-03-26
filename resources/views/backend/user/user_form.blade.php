{{ Form::open(array('url' => 'save-user','id'=>'saveUserForm')) }} 
<div class="card mb-2">
    <div id="headingOne" class="card-header">
        <button type="button" class="text-left m-0 p-0 btn btn-block">
            <span class="form-heading" style="color: #ff606d">Personal Information</span>
        </button>
    </div>
    <div data-parent="#accordion" id="collapseOne" aria-labelledby="headingOne" class="collapse show" style="">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>First Name *</label>
                        @php
                        $first_name = "";
                        if(!empty($user_data[0]['first_name']))  {
                            $first_name = $user_data[0]['first_name'];
                        }
                        $hidden_id = "";
                        if(!empty($user_data[0]['user_id']))  {
                            $hidden_id = $user_data[0]['user_id'];
                        }
                        @endphp
                        <input name="first_name" id="first_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="Enter " type="text" class="form-control" value="{{$first_name}}">
                        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>Last Name *</label>
                        @php
                        $last_name = "";
                        if(!empty($user_data[0]['last_name']))  {
                            $last_name = $user_data[0]['last_name'];
                        }
                        @endphp
                        <input name="last_name" id="last_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="Enter " type="text" class="form-control" value="{{$last_name}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>Gender *</label>
                        <select name="gender" class="form-control" id="gender1">
                            <option value="">Select </option>
                            @php
                            $GenderArray = array('Male' => 'm', 'Female' => 'f');
                            foreach($GenderArray as $k=>$v) {
                                $sel = "";
                                if(!empty($user_data[0]['gender']))  {
                                    if($user_data[0]['gender'] == $v) $sel = 'selected="selected"';
                                }
                            @endphp
                            <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-4">
                    <div class="position-relative form-group">
                        <label>Date of Birth *</label>
                        @php
                        $date_of_birth = "";
                        if(!empty($user_data[0]['date_of_birth']))  {
                            $date_of_birth = date('d/m/Y', strtotime($user_data[0]['date_of_birth']));
                        }
                        @endphp
                        <input type="text" class="form-control datetimepicker" name="date_of_birth" placeholder="Select" value="{{$date_of_birth}}" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>Mobile Number *</label>
                        @php
                        $mobile = "";
                        if(!empty($user_data[0]['mobile']))  {
                            $mobile = $user_data[0]['mobile'];
                        }
                        @endphp
                        <input name="mobile" type="number" class="form-control" placeholder="Enter Number Only" value="{{$mobile}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>Work phone number</label>
                        @php
                        $phone = "";
                        if(!empty($user_data[0]['phone']))  {
                            $phone = $user_data[0]['phone'];
                        }
                        @endphp
                        <input name="phone" class="form-control" type="number" placeholder="Enter Number Only" value="{{$phone}}">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label>Driving Licence *</label>
                        @php
                        $driving_licence = "";
                        if(!empty($user_data[0]['driving_licence']))  {
                            $driving_licence = $user_data[0]['driving_licence'];
                        }
                        @endphp
                        <input name="driving_licence" type="text" class="form-control" placeholder="Enter Driving Licence" value="{{$driving_licence}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="position-relative form-group">
                        <label>Date of joining *</label>
                        @php
                        $date_of_joining = "";
                        if(!empty($user_data[0]['date_of_joining']))  {
                            $date_of_joining = date('d/m/Y', strtotime($user_data[0]['date_of_joining']));
                        }
                        @endphp
                        <input type="text" class="form-control datetimepicker" name="date_of_joining" placeholder="Select" value="{{$date_of_joining}}" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Experience </label>
                        @php
                        $experience = "";
                        if(!empty($user_data[0]['experience']))  {
                            $experience = $user_data[0]['experience'];
                        }
                        @endphp
                        <textarea class="form-control" name="experience" placeholder="" >{{$experience}}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Permanent Address *</label>
                        @php
                        $address = "";
                        if(!empty($user_data[0]['address']))  {
                            $address = $user_data[0]['address'];
                        }
                        @endphp
                        <textarea class="form-control" name="address" placeholder="" >{{$address}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div id="headingOne" class="card-header">
        <button type="button" class="text-left m-0 p-0 btn btn-block">
            <span class="form-heading" style="color: #ff606d">Account Information</span>
        </button>
    </div>
    <div data-parent="#accordion" id="collapseOne" aria-labelledby="headingOne" class="collapse show" style="">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label>Email *</label>
                        @php
                        $email = "";
                        if(!empty($user_data[0]['email']))  {
                            $email = $user_data[0]['email'];
                        }
                        @endphp
                        <input name="email" id="email" placeholder="Enter " type="email" class="form-control" value="{{$email}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label>User Name*</label>
                        @php
                        $username = "";
                        if(!empty($user_data[0]['username']))  {
                            $username = $user_data[0]['username'];
                        }
                        @endphp
                        <input name="username" id="username" placeholder="Enter" type="text" class="form-control" value="{{$username}}">
                    </div>
                </div>
            </div>
            @php
            if(empty($user_data))  {
            @endphp
            <div class="form-row">
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label>Password *</label>
                        <input name="password" id="password" placeholder="" type="password" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label>Confirm Password *</label>
                        <input name="confirm_password" placeholder="" type="password" class="form-control">
                    </div>
                </div>
            </div>
            @php
            }
            @endphp
            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>User Status *</label>
                        <select name="status" class="form-control" aria-required="true">
                            <option value=""> Select  </option>
                            @php
                            $StatusArray = array('Active' => 'Active', 'Inactive' => 'Inactive');
                            foreach($StatusArray as $k=>$v) {
                                if(!empty($user_data[0]['status']))  {
                                    $sel = "";
                                    if($user_data[0]['status'] == $v) $sel = 'selected="selected';
                                }
                            @endphp
                                <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>User Type *</label>
                        <select name="user_type" class="form-control" id="user_type" aria-required="true">
                            <option value="">Select </option>
                            @php
                            $TypeArray = array('Admin' => 'A', 'Employee' => 'S');
                            foreach($TypeArray as $k=>$v) {
                                if(!empty($user_data[0]['user_type']))  {
                                    $sel = "";
                                    if($user_data[0]['user_type'] == $v) $sel = 'selected="selected';
                                }
                            @endphp
                                <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="fk_user_role" class="form-control" aria-required="true">
                            <option value=""> Select  </option>
                            @php
                            if(!empty($user_roll_data)) {
                            foreach($user_roll_data as $urData) {
                                if(!empty($user_data[0]['fk_user_role']))  {
                                    $sel = "";
                                    if($user_data[0]['fk_user_role'] == $urData['user_role_id']) $sel = 'selected="selected';
                                }
                            @endphp
                                <option value="{{$urData['user_role_id']}}" {{$sel}}>{{$urData['name']}}</option>
                            @php
                                }
                            }
                            @endphp
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Bulk User Create</label>
            <input type="file" id="users_csv" name="users_csv" class="file-upload-default" />
            <div class="input-group col-xs-12">
                <input type="text" class="form-control file-upload-info" id="users_csv" name="users_csv" disabled placeholder="Upload CSV" />
                <span class="input-group-append">
                    <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                </span>
            </div>
        </div>
        <div class="form-group">
            <button class="btn-shadow btn btn-info preview-multiple-users" type="button"> Preview </button>
        </div>
    </div>
</div>
<p class="text-right"><button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p>
{{ Form::close() }}