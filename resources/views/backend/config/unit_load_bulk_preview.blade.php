{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Unit Load Type</th>
            <th>Unit</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['unit_load_type_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="3" align="center">This {{$data['unit_load_type']}} Load type already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['unit_name_exist'] == "")
              <tr style="background-color: red">
                <td colspan="3" align="center">"{{$data['unit_name']}}" This in invalid unit. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['unit_load_type']}}</td>
                <td>{{$data['unit_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-unit-load-bulk-csv" value="Submit"> Create Unit Load </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}