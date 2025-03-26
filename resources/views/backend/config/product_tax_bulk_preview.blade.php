{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Tax Name</th>
            <th>Tax Rate</th>
            <th>Tax Type</th>
            <th>Tax Description</th>
            <th>Warehouse</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['tax_name_exist'] == 1)
              <tr style="background-color: red">
                <td colspan="5" align="center">This {{$data['tax_name']}} tax name already taken. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['warehouse_name'] == "")
              <tr style="background-color: red">
                <td>{{$data['tax_name']}}</td>
                <td>{{$data['tax_rate']}}</td>
                <td>{{$data['tax_type']}}</td>
                <td>{{$data['tax_description']}}</td>
                <td align="center">"{{$data['warehouse_id']}}" This in invalid Warehouse ID. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['tax_name']}}</td>
                <td>{{$data['tax_rate']}}</td>
                <td>{{$data['tax_type']}}</td>
                <td>{{$data['tax_description']}}</td>
                <td>{{$data['warehouse_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-product-tax-bulk-csv" value="Submit"> Create Product Tax </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}