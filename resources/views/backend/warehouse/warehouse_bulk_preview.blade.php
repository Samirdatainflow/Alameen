{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Name</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Country</th>
            <th>Manager</th>
            <th>Contact</th>
            <th>Surface</th>
            <th>Volume</th>
            <th>Freezone</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="12" align="center">This "{{$data['name']}}" name already taken. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['name']}}</td>
                <td>{{$data['address']}}</td>
                <td>{{$data['city_name']}}</td>
                <td>{{$data['state_name']}}</td>
                <td>{{$data['country_name']}}</td>
                <td>{{$data['manager']}}</td>
                <td>{{$data['contact']}}</td>
                <td>{{$data['surface']}}</td>
                <td>{{$data['volume']}}</td>
                <td>{{$data['freezone']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-warehouse-bulk-csv" value="Submit"> Create Warehouse </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}