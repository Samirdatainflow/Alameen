{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Gender</th>
            <th>Date Of Birth</th>
            <th>Address</th>
            <th>Mobile</th>
            <th>Phone</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>User Type</th>
            <th>User Role</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['username_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="12" align="center">This "{{$data['username']}}" username already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['email_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="12" align="center">This "{{$data['email']}}" email already taken. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['first_name']}}</td>
                <td>{{$data['last_name']}}</td>
                <td>{{$data['gender']}}</td>
                <td>{{$data['date_of_birth']}}</td>
                <td>{{$data['address']}}</td>
                <td>{{$data['mobile']}}</td>
                <td>{{$data['phone']}}</td>
                <td>{{$data['username']}}</td>
                <td>{{$data['email']}}</td>
                <td>{{$data['password']}}</td>
                <td>{{$data['user_type']}}</td>
                <td>{{$data['fk_user_role']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-users-bulk-csv" value="Submit"> Create User </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}