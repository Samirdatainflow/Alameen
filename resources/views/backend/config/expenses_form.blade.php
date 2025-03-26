{{ Form::open(array('id'=>'ExpensesForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Name *</label>
                @php
                $expenses_description = "";
                if(!empty($expenses_data[0]['expenses_description']))  {
                    $expenses_description = $expenses_data[0]['expenses_description'];
                }
                $hidden_id = "";
                if(!empty($expenses_data[0]['expenses_id']))  {
                    $hidden_id = $expenses_data[0]['expenses_id'];
                }
                @endphp
                <input name="expenses_description" id="expenses_description" placeholder="" type="text" class="form-control" value="{{$expenses_description}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}