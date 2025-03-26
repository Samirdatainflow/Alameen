{{ Form::open(array('id'=>'previewOrderForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Part No</th>
            <th>Part Brand</th>
            <th>Part Name</th>
            <th>Unit</th>
            <th>Manufacturer No</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($products) > 0)
            @foreach($products as $data)
              @if($data['product'] != "")
              <tr>
                <td>{{$data['part_no']}}</td>
                <td>{{$data['part_brand']}}</td>
                <td>{{$data['part_name']}}</td>
                <td>{{$data['unit_name']}}</td>
                <td>{{$data['manufacturing_no']}}</td>
                <td>{{$data['quantity']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td>{{$data['part_no']}}</td>
                <td colspan="4" align="center">This is wrong product. It will be skipped when upload.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info create_mutiple_order_csv" value="Submit"> Create Order </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Cancel </button>
  </p>
{{ Form::close() }}