{{ Form::open(array('id'=>'categoryBulkForm')) }}
  <div class="row">
    <div class="col-md-12">
      <table class="table table-hover table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Trading Date</th>
            <th>Source Currency</th>
            <th>Target Date</th>
            <th>Closing Rate</th>
            <th>Average Rate</th>
          </tr>
        </thead>
        <tbody>
          @php
          @endphp
          @if(sizeof($dataArr) > 0)
            @foreach($dataArr as $data)
              @if($data['source_currency_exist'] == 0)
              <tr style="background-color: red">
                <td>{{$data['trading_date']}}</td>
                <td colspan="4" align="center">This {{$data['source_currency_id']}} is incorrect Source Currency. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['target_currency_exist'] == 0)
              <tr style="background-color: red">
                <td>{{$data['trading_date']}}</td>
                <td>{{$data['source_currency']}}</td>
                <td colspan="3" align="center">This {{$data['target_currency_id']}} is incorrect Target Currency. It will be skipped while uploading.</td>
              </tr>
              @elseif($data['currency_same'] == 1)
              <tr style="background-color: red">
                <td>{{$data['trading_date']}}</td>
                <td>{{$data['source_currency']}}</td>
                <td>{{$data['target_currency']}}</td>
                <td colspan="2" align="center">Source Currency and Target Currency is same. It will be skipped while uploading.</td>
              </tr>
              @else
              <tr>
                <td>{{$data['trading_date']}}</td>
                <td>{{$data['source_currency']}}</td>
                <td>{{$data['target_currency']}}</td>
                <td>{{$data['closing_rate']}}</td>
                <td>{{$data['average_rate']}}</td>
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
      <button type="button" name="submit" class="btn-shadow btn btn-info save-exchange-rate-bulk-csv" value="Submit"> Create Exchange Rate </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
  </p>
{{ Form::close() }}