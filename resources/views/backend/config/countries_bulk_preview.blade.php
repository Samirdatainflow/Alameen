{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Country Code</th>
            <th>Country Name</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['country_name_exist'] == 0)
              <tr>
                <td>{{$data['country_code']}}</td>
                <td>{{$data['country_name']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="2" align="center">This "{{$data['country_name']}}"" name already taken. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-countries-bulk-csv" value="Submit"> Create Country </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}