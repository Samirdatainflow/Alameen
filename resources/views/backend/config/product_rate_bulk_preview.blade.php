{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Default Rate</th>
            <th>Level 1</th>
            <th>Level 2</th>
            <th>Level 3</th>
            <th>Level 4</th>
            <th>Level 5</th>
            <th>Warehouse</th>
            <th>Product</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['default_rate_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="8" align="center">This {{$data['default_rate']}} Default rate already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['warehouse_name'] == "")
              <tr style="background-color: red">
                <td>{{$data['default_rate']}}</td>
                <td>{{$data['level_1']}}</td>
                <td>{{$data['level_2']}}</td>
                <td>{{$data['level_3']}}</td>
                <td>{{$data['level_4']}}</td>
                <td>{{$data['level_5']}}</td>
                <td colspan="3" align="center">"{{$data['warehouse_id']}}" This in invalid Warehouse ID. It will be skipped while uploading.</td>
              </tr>
               @elseif($data['product_name'] == "")
              <tr style="background-color: red">
                <td>{{$data['default_rate']}}</td>
                <td>{{$data['level_1']}}</td>
                <td>{{$data['level_2']}}</td>
                <td>{{$data['level_3']}}</td>
                <td>{{$data['level_4']}}</td>
                <td>{{$data['level_5']}}</td>
                <td>{{$data['warehouse_name']}}</td>
                <td colspan="3" align="center">"{{$data['part_no']}}" This in invalid part no. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['default_rate']}}</td>
                <td>{{$data['level_1']}}</td>
                <td>{{$data['level_2']}}</td>
                <td>{{$data['level_3']}}</td>
                <td>{{$data['level_4']}}</td>
                <td>{{$data['level_5']}}</td>
                <td>{{$data['warehouse_name']}}</td>
                <td>{{$data['product_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-product-rate-bulk-csv" value="Submit"> Create Product Rate </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}