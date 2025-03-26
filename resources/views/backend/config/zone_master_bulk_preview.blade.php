{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Zone Name</th>
            <th>Location</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['zone_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['zone_name']}} name is already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['location_name'] == "")
              <tr style="background-color: red">
                <td>{{$data['zone_name']}}</td>
                <td align="center">This {{$data['location_id']}} is invalid location ID. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['zone_name']}}</td>
                <td>{{$data['location_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-zone-master-bulk-csv" value="Submit"> Create Zone </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}