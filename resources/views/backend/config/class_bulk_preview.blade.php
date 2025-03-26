{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Class Value</th>
            <th>Class Description</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['class_value_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['class_value']}} value is already taken. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['class_value']}}</td>
                <td>{{$data['description']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-class-bulk-csv" value="Submit"> Create Class </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}