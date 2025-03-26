{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Part No</th>
            <th>Part Brand</th>
            <th>Part Name</th>
            <th>Car Manufacture</th>
            <th>Car Model</th>
            <th>Category</th>
            <th>Group</th>
            <th>Unit</th>
            <th>Retail Price</th>
            <th>Quantity On Hand</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['part_no_exist'] == 0)
              <tr>
                <td>{{$data['part_no']}}</td>
                <td>{{$data['part_brand_name']}}</td>
                <td>{{$data['part_name']}}</td>
                <td>{{$data['car_manufacture']}}</td>
                <td>{{$data['car_model']}}</td>
                <td>{{$data['category_name']}}</td>
                <td>{{$data['group_name']}}</td>
                <td>{{$data['unit_name']}}</td>
                <td>{{$data['pmrprc']}}</td>
                <td>{{$data['current_stock']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td align="center" colspan="9">This {{$data['part_no']}} name already exist. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-item-bulk-csv" value="Submit"> Create Item </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}