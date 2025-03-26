{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Lote Name</th>
            <th>Product</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['lot_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="3" align="center">This {{$data['lot_name']}} lot name already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['product_name'] == "")
              <tr style="background-color: red">
                <td colspan="3" align="center">"{{$data['partno']}}" This in invalid part no. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['lot_name']}}</td>
                <td>{{$data['product_name']}}</td>
                <td>{{$data['quantity']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-lots-bulk-csv" value="Submit"> Create Lots </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}