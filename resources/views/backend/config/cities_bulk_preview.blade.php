{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>State</th>
            <th>City Code </th>
            <th>City Name</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['city_name_exist'] == 0)
              <tr>
                <td>{{$data['state_name']}}</td>
                <td>{{$data['city_code']}}</td>
                <td>{{$data['city_name']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="3" align="center">This "{{$data['city_name']}}"" name already taken. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-cities-bulk-csv" value="Submit"> Create City </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}