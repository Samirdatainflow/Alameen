{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Car Manufacture</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['car_manufacture_exist'] == 0)
              <tr>
                <td>{{$data['car_manufacture']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td align="center">This {{$data['car_manufacture']}} name already exist. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-car-manufacture-bulk-csv" value="Submit"> Create Car Manufacture </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}