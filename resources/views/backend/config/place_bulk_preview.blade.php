{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Position</th>
            <th>Location</th>
            <th>Zone</th>
            <th>Row</th>
            <th>Rack</th>
            <th>Plate</th>
            <th>Max Capacity</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['place_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="6" align="center">This {{$data['place_name']}} name already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['location_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['plate_name']}}</td>
                <td colspan="5" align="center">"{{$data['location_name']}}" This is invalid location. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['zone_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['place_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td align="center" colspan="4">"{{$data['zone_name']}}" This is invalid zone or it not under this "{{$data['location_name']}}" location. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['row_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['place_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td>{{$data['zone_name']}}</td>
                <td align="center" colspan="3">"{{$data['row_name']}}" This is invalid row or it not under this "{{$data['zone_name']}}" zone or invalid location. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['rack_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['place_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td>{{$data['zone_name']}}</td>
                <td>{{$data['row_name']}}</td>
                <td align="center" colspan="2">"{{$data['rack_name']}}" This is invalid rack or it not under this "{{$data['row_name']}}" row or invalid location. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['plate_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['place_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td>{{$data['zone_name']}}</td>
                <td>{{$data['row_name']}}</td>
                <td>{{$data['rack_name']}}</td>
                <td align="center">"{{$data['plate_name']}}" This is invalid plate or it not under this "{{$data['rack_name']}}" row or invalid location. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['place_name']}}</td>
                <td>{{$data['location_name']}}</td>
                <td>{{$data['zone_name']}}</td>
                <td>{{$data['row_name']}}</td>
                <td>{{$data['rack_name']}}</td>
                <td>{{$data['plate_name']}}</td>
                <td>{{$data['max_capacity']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-place-bulk-csv" value="Submit"> Create Position </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}