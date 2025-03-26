{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Currency Code</th>
            <th>Currency Description</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['currency_code_exist'] == 0)
              <tr>
                <td>{{$data['currency_code']}}</td>
                <td>{{$data['currency_description']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="2" align="center">This "{{$data['currency_code']}}"" Currency Code already taken. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-currency-bulk-csv" value="Submit"> Create Currency </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}