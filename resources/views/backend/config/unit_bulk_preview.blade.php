{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Unit Name</th>
            <th>Unit Type</th>
            <th>Base Factor</th>
            <th>Base Measurement Unit</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['unit_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="4" align="center">This {{$data['unit_name']}} name already taken. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['unit_name']}}</td>
                <td>{{$data['unit_type']}}</td>
                <td>{{$data['base_factor']}}</td>
                <td>{{$data['base_measurement_unit']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-unit-bulk-csv" value="Submit"> Create Unit </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}