{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>State Code</th>
            <th>Country </th>
            <th>State Name</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['state_name_exist'] == 0)
              <tr>
                <td>{{$data['state_code']}}</td>
                <td>{{$data['country_name']}}</td>
                <td>{{$data['state_name']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="3" align="center">This "{{$data['state_name']}}"" name already taken. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-state-bulk-csv" value="Submit"> Create State </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}