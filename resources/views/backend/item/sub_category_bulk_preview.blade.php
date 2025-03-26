{{ Form::open(array('id'=>'PartBrandBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Sub Category Name</th>
            <th>Category Name</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['sub_category_exist'] == "1")
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['sub_category_name']}} sub category name already exist. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['category_exist'] == "")
              <tr style="background-color: red">
                <td>{{$data['sub_category_name']}}</td>
                <td colspan="2" align="center">This {{$data['category_name']}} category ID does not exist. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['sub_category_name']}}</td>
                <td>{{$data['category_name']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-sub-category-bulk-csv" value="Submit"> Create Sub Category </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}