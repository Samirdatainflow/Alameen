{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Car Manufacture</th>
            <th>Car Model</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['car_manufacture_exist'] == "")
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['car_manufacture']}} is incorrect manufacture. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['brand_name_exist'] == 0)
              <tr>
                <td>{{$data['car_manufacture']}}</td>
                <td>{{$data['brand_name']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['brand_name']}} name already taken. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-car-model-bulk-csv" value="Submit"> Create Car Model </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}