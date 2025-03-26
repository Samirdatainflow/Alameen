{{ Form::open(array('id'=>'CustomerManagementForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Customer Code *</label>
                        @php
                        $customer_id = "";
                        if(!empty($client_data[0]['customer_id']))  {
                            $customer_id = $client_data[0]['customer_id'];
                        }
                        $hidden_id = "";
                        if(!empty($client_data[0]['client_id']))  {
                            $hidden_id = $client_data[0]['client_id'];
                        }
                        @endphp 
                        <input name="customer_id" id="customer_id" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9-_/]/g, '')" placeholder="" type="text" class="form-control" value="{{$customer_id}}">
                        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                    </div>
                    <div class="col-md-4">
                        <label>Name Of The Customer *</label>
                        @php
                        $customer_name = "";
                        if(!empty($client_data[0]['customer_name']))  {
                            $customer_name = $client_data[0]['customer_name'];
                        }
                        @endphp                
                        <input name="customer_name" id="customer_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="" type="text" class="form-control" value="{{$customer_name}}">
                    </div>
                    <div class="col-md-4" style="display:none">
                        <label>Password *</label>
                        <input name="password" id="password" placeholder="" type="text" class="form-control" value="">
                    </div>
                    <div class="col-md-4">
                        <label>Commercial Registration No </label>
                        @php
                        $reg_no = "";
                        if(!empty($client_data[0]['reg_no']))  {
                            $reg_no = $client_data[0]['reg_no'];
                        }
                        @endphp 
                        <input name="reg_no" id="reg_no" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" placeholder="" type="number" class="form-control" value="{{$reg_no}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Name Of The Sponsor's </label>
                        @php
                        $sponsor_name = "";
                        if(!empty($client_data[0]['sponsor_name']))  {
                            $sponsor_name = $client_data[0]['sponsor_name'];
                        }
                        @endphp 
                        <input name="sponsor_name" id="sponsor_name" onkeyup="this.value=this.value.replace(/[^a-zA-Z ]/g, '')" placeholder="" type="text" class="form-control" value="{{$sponsor_name}}">
                    </div>
                    <div class="col-md-4">
                        <label>Sponsor Category</label>
                        <select name="sponsor_category" class="form-control">
                            <option value="">Select</option>
                            @php
                            $SponsorCategoryArray = ['Employee' => 'Employee', 'Business man' => 'Business man', 'Entrepreneur' => 'Entrepreneur', 'Government servant' => 'Government servant,', 'ROP' => 'ROP'];
                            foreach($SponsorCategoryArray as $k=>$v) {
                                $sel = "";
                                if(!empty($client_data[0]['sponsor_category']))  {
                                    if($client_data[0]['sponsor_category'] == $v) $sel = 'selected="selected"';
                                }
                            @endphp
                            <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Sponsor Place Of Work</label>
                        @php
                        $sponsor_place_of_work = "";
                        if(!empty($client_data[0]['sponsor_place_of_work']))  {
                            $sponsor_place_of_work = $client_data[0]['sponsor_place_of_work'];
                        }
                        @endphp 
                        <input name="sponsor_place_of_work" id="sponsor_place_of_work" placeholder="" type="text" class="form-control" value="{{$sponsor_place_of_work}}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Sponsor Id </label>
                        @php
                        $sponsor_id = "";
                        if(!empty($client_data[0]['sponsor_id']))  {
                            $sponsor_id = $client_data[0]['sponsor_id'];
                        }
                        @endphp 
                       <input name="sponsor_id" id="sponsor_id" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" placeholder="" type="number" class="form-control" value="{{$sponsor_id}}">
                    </div>
                    <div class="col-md-4">
                        <label>Credit Application Attached</label>
                        <select class="form-control" name="credit_appl_ind" id="credit_appl_ind">
                            <option disabled="" selected="">Select</option>
                            @php
                            $CraditApplication = array('Yes' => 'y', 'No' => 'n');
                            foreach($CraditApplication as $k=>$v) {
                                $sel = "";
                                if(!empty($client_data[0]['credit_appl_ind']))  {
                                    if($client_data[0]['credit_appl_ind'] == $v) $sel = 'selected="selected"';
                                }
                            @endphp
                                <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Other Customer Codes</label>
                        @php
                        $other_customer_code = "";
                        if(!empty($client_data[0]['other_customer_code']))  {
                            $other_customer_code = $client_data[0]['other_customer_code'];
                        }
                        @endphp 
                        <input name="other_customer_code" id="other_customer_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" placeholder="" type="text" class="form-control" value="{{$other_customer_code}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Authorizer Name</label>
                        @php
                        $authorizer_name = "";
                        if(!empty($client_data[0]['authorizer_name']))  {
                            $authorizer_name = $client_data[0]['authorizer_name'];
                        }
                        @endphp 
                        <input name="authorizer_name" id="authorizer_name" placeholder="" type="text" class="form-control" value="{{$authorizer_name}}">
                    </div>
                    <div class="col-md-4">
                        <label>Official Email Id *</label>
                        @php
                        $customer_email_id = "";
                        if(!empty($client_data[0]['customer_email_id']))  {
                            $customer_email_id = $client_data[0]['customer_email_id'];
                        }
                        @endphp 
                        <input name="customer_email_id" id="customer_email_id" placeholder="" type="email" class="form-control" value="{{$customer_email_id}}">
                    </div>
                    <div class="col-md-4">
                        <label>Official Whatsapp No </label>
                        @php
                        $customer_wa_no = "";
                        if(!empty($client_data[0]['customer_wa_no']))  {
                            $customer_wa_no = $client_data[0]['customer_wa_no'];
                        }
                        @endphp
                        <input name="customer_wa_no" id="customer_wa_no" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="" type="number" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" class="form-control" value="{{$customer_wa_no}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Contact Mobile Number </label>
                        @php
                        $customer_off_msg_no = "";
                        if(!empty($client_data[0]['customer_off_msg_no']))  {
                            $customer_off_msg_no = $client_data[0]['customer_off_msg_no'];
                        }
                        @endphp
                        <input name="customer_off_msg_no" id="customer_off_msg_no" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="" type="number" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" class="form-control" value="{{$customer_off_msg_no}}">
                    </div>
                    <div class="col-md-4">
                        <label>Security Cheques</label>
                        <select class="form-control" name="security_cheques_submitted" id="security_cheques_submitted">
                            <option disabled="" selected="">Select</option>
                            <option value="y">Yes</option>
                            <option value="n">No</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Credit Limit</label>
                        @php
                        $customer_credit_limit = "";
                        if(!empty($client_data[0]['customer_credit_limit']))  {
                            $customer_credit_limit = $client_data[0]['customer_credit_limit'];
                        }
                        @endphp
                        <input name="customer_credit_limit" id="customer_credit_limit" placeholder="Enter Credit Limit " type="number" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" class="form-control" value="{{$customer_credit_limit}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4" style="display:none">
                        <label>Application Form</label>
                        <select class="form-control" name="application_required_ind" id="application_required_ind">
                            <option disabled="" selected="">Select</option>
                            <option value="y">Yes</option>
                            <option value="n">No</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Credit Period</label>
                        @php
                        $customer_credit_period = "";
                        if(!empty($client_data[0]['customer_credit_period']))  {
                            $customer_credit_period = $client_data[0]['customer_credit_period'];
                        }
                        @endphp
                        <input name="customer_credit_period" id="customer_credit_period" placeholder="Enter Credit Period" type="number" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" class="form-control" value="{{$customer_credit_period}}">
                    </div>
                    <div class="col-md-4">
                        <label>Discount %</label>
                        @php
                        $customer_discount_percent = "";
                        if(!empty($client_data[0]['customer_discount_percent']))  {
                            $customer_discount_percent = $client_data[0]['customer_discount_percent'];
                        }
                        @endphp
                        <input name="customer_discount_percent" id="customer_discount_percent" placeholder="" type="number" onkeyup="this.value=this.value.replace(/[^0-9]/g, '')" class="form-control" max="100" value="{{$customer_discount_percent}}">
                    </div>
                    <div class="col-md-4">
                        <label>Payment Mode</label>
                        <select name="customer_payment_method" class="form-control">
                            <option value="">Select</option>
                            @php
                            $PaymentModeArray = ['Cash' => 'Cash', 'Cheque' => 'Cheque'];
                            foreach($PaymentModeArray as $k=>$v) {
                                $sel = '';
                                if(!empty($client_data[0]['customer_payment_method']))  {
                                    if($client_data[0]['customer_payment_method'] == $v) $sel = 'selected="selected"';
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
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Region</label>
                        <select name="customer_region" id="customer_region" class="form-control">
                            <option value="">Select</option>
                            @php
                            $customerRegionArray = [['region' => 'Ad Dakhiliyah', 'teritory' => 'Nizwa'], ['region' => 'Ad Dhahirah', 'teritory' => 'Ibri'], ['region' => 'Al Batinah North', 'teritory' => 'Sohar'], ['region' => 'Al Batinah South', 'teritory' => 'Rustaq'], ['region' => 'Al Buraymi', 'teritory' => 'Al Buraymi'], ['region' => 'Al Wusta', 'teritory' => 'Haima'], ['region' => 'Ash Sharqiyah North', 'teritory' => 'Ibra'], ['region' => 'Ash Sharqiyah South', 'teritory' => 'Sur'], ['region' => 'Dhofar', 'teritory' => 'Salalah'], ['region' => 'Muscat', 'teritory' => 'Muscat'], ['region' => 'Musandam', 'teritory' => 'Khasab']];
                            foreach($customerRegionArray as $rData) {
                                $sel = "";
                                if(!empty($client_data[0]['customer_region']))  {
                                    if($client_data[0]['customer_region'] == $rData['region']){
                                        $sel = 'selected="selected"';
                                    }
                                }
                            @endphp
                            <option value="{{$rData['region']}}" data-teritory="{{$rData['teritory']}}" {{$sel}}>{{$rData['region']}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Teritory</label>
                        <select name="customer_teritory" id="customer_teritory" class="form-control">
                            <option value="">Select</option>
                            @php
                            $customerTeritoryArray = ['Nizwa' => 'Nizwa', 'Ibri' => 'Ibri', 'Sohar' => 'Sohar', 'Rustaq' => 'Rustaq', 'Al Buraymi' => 'Al Buraymi', 'Haima' => 'Haima', 'Ibra' => 'Ibra', 'Sur' => 'Sur', 'Salalah' => 'Salalah', 'Muscat' => 'Muscat', 'Khasab' => 'Khasab'];
                            foreach($customerTeritoryArray as $k=>$v) {
                                $sel = "";
                                $style = 'style=display:none';
                                if(!empty($client_data[0]['customer_region']))  {
                                    if($client_data[0]['customer_teritory'] == $v){
                                        $sel = 'selected="selected"';
                                        $style = 'style=display:block';
                                    }
                                }
                            @endphp
                            <option value="{{$v}}" {{$sel}} {{$style}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Area</label>
                        @php
                        $customer_area = "";
                        if(!empty($client_data[0]['customer_area']))  {
                            $customer_area = $client_data[0]['customer_area'];
                        }
                        @endphp
                        <input name="customer_area" id="customer_area" placeholder=" " type="text" class="form-control" value="{{$customer_area}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Stop Sale</label>
                        <select class="form-control" name="customer_stopsale" id="customer_stopsale">
                            <option disabled="" selected="">Select</option>
                            @php
                            $customerstopsaleArray = array('Yes' => 'y', 'No' => 'n');
                            foreach($customerstopsaleArray as $k=>$v) {
                                $sel = "";
                                if(!empty($client_data[0]['customer_stopsale']))  {
                                    if($client_data[0]['customer_stopsale']==$v) $sel='selected="selected"';
                                }
                            @endphp
                            <option value="{{$v}}" {{$sel}}>{{$k}}</option>
                            @php
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>VATIN</label>
                        @php
                        $vatin = "";
                        if(!empty($client_data[0]['vatin']))  {
                            $vatin = $client_data[0]['vatin'];
                        }
                        @endphp
                        <input name="vatin" id="vatin" placeholder="Add VATIN " type="text" class="form-control" value="{{$vatin}}">
                    </div>
                    @php
                    $customer_file = "";
                    if(!empty($client_data[0]['customer_file']))  {
                        $customer_file = $client_data[0]['customer_file'];
                    }
                    @endphp
                    <input name="hidden_customer_file" id="hidden_customer_file" type="hidden" class="form-control" value="{{$customer_file}}">
                    <div class="col-md-4">
                        <label>Upload Customer Document</label>
                        <input type="file" id="customer_file" name="customer_file[]" class="file-upload-default" multiple />
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" id="customer_file" name="customer_file" disabled placeholder="Upload Document" />
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Store Address</label>
                        @php
                        $store_address = "";
                        if(!empty($client_data[0]['store_address']))  {
                            $store_address = $client_data[0]['store_address'];
                        }
                        @endphp
                        <textarea class="form-control" name="store_address" placeholder="Enter Store Address">{{$store_address}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Delivery address</label>
                        @php
                        $delivery_address = "";
                        if(!empty($client_data[0]['delivery_address']))  {
                            $delivery_address = $client_data[0]['delivery_address'];
                        }
                        @endphp
                        <textarea class="form-control" name="delivery_address" placeholder="Enter Delivery Address">{{$delivery_address}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>HO Address</label>
                        @php
                        $ho_address = "";
                        if(!empty($client_data[0]['ho_address']))  {
                            $ho_address = $client_data[0]['ho_address'];
                        }
                        @endphp
                        <textarea class="form-control" name="ho_address" placeholder="Enter HO Address">{{$ho_address}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>CR Address</label>
                        @php
                        $cr_address = "";
                        if(!empty($client_data[0]['cr_address']))  {
                            $cr_address = $client_data[0]['cr_address'];
                        }
                        @endphp
                        <textarea class="form-control" name="cr_address" placeholder="Enter CR Address">{{$cr_address}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Bulk Customer Create</label>
                <input type="file" id="customer_csv" name="customer_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="customer_csv" name="customer_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-customer" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}