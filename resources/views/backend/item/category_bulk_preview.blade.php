{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Category</th>
            <th>Category Description</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['category_name_exist'] == 0)
              <tr>
                <td>{{$data['category_name']}}</td>
                <td>{{$data['category_description']}}</td>
              </tr>
              @else
              <tr style="background-color: red">
                <td colspan="2" align="center">This {{$data['category_name']}} category name already exist. It will be skipped while uploading.</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-category-bulk-csv" value="Submit"> Create Category </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}