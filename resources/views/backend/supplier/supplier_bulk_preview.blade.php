{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Supplier Code</th>
            <th>Full Name</th>
            <th>Business Title</th>
            <th>Mobile</th>
            <th>Phone</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Country</th>
            <th>Zip Code</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['supplier_email_exist'] == "1")
              <tr style="background-color: red">
                <td colspan="11" align="center">This "{{$data['email']}}"" supplier email already taken. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['supplier_code']}}</td>
                <td>{{$data['full_name']}}</td>
                <td>{{$data['business_title']}}</td>
                <td>{{$data['mobile']}}</td>
                <td>{{$data['phone']}}</td>
                <td>{{$data['address']}}</td>
                <td>{{$data['city_name']}}</td>
                <td>{{$data['state_name']}}</td>
                <td>{{$data['country_name']}}</td>
                <td>{{$data['zipcode']}}</td>
                <td>{{$data['email']}}</td>
              </tr>
              @endif
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
  <br>
  <p class="text-right">
      <button type="button" name="submit" class="btn-shadow btn btn-info save-supplier-bulk-csv" value="Submit"> Create Supplier </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}