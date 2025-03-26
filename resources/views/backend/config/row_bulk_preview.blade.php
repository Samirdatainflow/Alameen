{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Row Name</th>
            <th>Location</th>
            <th>Zone</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['row_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="3" align="center">This {{$data['row_name']}} name / location already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['location_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['row_name']}}</td>
                <td colspan="2" align="center">This {{$data['location_name']}} is invalid location. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['zone_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['row_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td align="center">This {{$data['zone_name']}} is invalid zone. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['row_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td>{{$data['zone_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-row-bulk-csv" value="Submit"> Create Row </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}