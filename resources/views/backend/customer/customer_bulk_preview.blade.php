{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
<div class="row">
    <div class="col-md-12">
        <div class = "table-responsive">
          <table class="table table-hover table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Customer Code</th>
                <th>Name Of The Customer</th>
                <th>Register No</th>
                <th>Name Of The Sponsor's</th>
                <th>Sponsor Category</th>
                <th>Sponsor Place Of Work</th>
                <th>Sponsor Id</th>
                <th>Cradit Application Attached</th>
                <th>Other Customer Codes</th>
                <th>Authorizer Name</th>
                <th>Official Email Id</th>
                <th>Official Whatsapp No</th>
                <th>Official Messaging No</th>
                <th>Security Cheques</th>
                <th>Application Form</th>
                <th>Credit Limit</th>
                <th>Credit Period</th>
                <th>Discount %</th>
                <th>Payment Mode</th>
                <th>Region</th>
                <th>Teritory</th>
                <th>Area</th>
                <th>Stop Sale</th>
                <th>Store Address</th>
                <th>Delivery address</th>
                <th>HO Address</th>
                <th>CR Address</th>
              </tr>
            </thead>
            <tbody>
              @php
              @endphp
              @if(sizeof($dataArr) > 0)
                @foreach($dataArr as $data)
                  @if($data['customer_id_exist'] == 0)
                  <tr>
                    <td>{{$data['customer_code']}}</td>
                    <td>{{$data['customer_name']}}</td>
                    <td>{{$data['reg_no']}}</td>
                    <td>{{$data['sponsor_name']}}</td>
                    <td>{{$data['sponsor_category']}}</td>
                    <td>{{$data['sponsor_place_of_work']}}</td>
                    <td>{{$data['sponsor_id']}}</td>
                    <td>{{$data['credit_appl_ind']}}</td>
                    <td>{{$data['other_customer_code']}}</td>
                    <td>{{$data['authorizer_name']}}</td>
                    <td>{{$data['customer_email_id']}}</td>
                    <td>{{$data['customer_wa_no']}}</td>
                    <td>{{$data['customer_off_msg_no']}}</td>
                    <td>{{$data['security_cheques_submitted']}}</td>
                    <td>{{$data['application_required_ind']}}</td>
                    <td>{{$data['customer_credit_limit']}}</td>
                    <td>{{$data['customer_credit_period']}}</td>
                    <td>{{$data['customer_discount_percent']}}</td>
                    <td>{{$data['customer_payment_method']}}</td>
                    <td>{{$data['customer_region']}}</td>
                    <td>{{$data['customer_teritory']}}</td>
                    <td>{{$data['customer_area']}}</td>
                    <td>{{$data['customer_stopsale']}}</td>
                    <td>{{$data['store_address']}}</td>
                    <td>{{$data['delivery_address']}}</td>
                    <td>{{$data['ho_address']}}</td>
                    <td>{{$data['cr_address']}}</td>
                  </tr>
                  @else
                  <tr style="background-color: red">
                    <td colspan="27" align="center">This "{{$data['customer_code']}}"" customer code already taken. It will be skipped while uploading.</td>
                  </tr>
                  @endif
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
    </div>
</div>
<br>
<p class="text-right">
    <button type="button" name="submit" class="btn-shadow btn btn-info save-customer-bulk-csv" value="Submit"> Create Customer </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>
{{ Form::close() }}