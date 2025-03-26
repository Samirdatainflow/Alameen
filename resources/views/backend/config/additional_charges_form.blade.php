{{ Form::open(array('id'=>'configAdditionalChargesForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Name *</label>
                @php
                $name = "";
                if(!empty($additional_Charges_data[0]['name']))  {
                    $name = $additional_Charges_data[0]['name'];
                }
                $hidden_id = "";
                if(!empty($additional_Charges_data[0]['additional_charges_id']))  {
                    $hidden_id = $additional_Charges_data[0]['additional_charges_id'];
                }
                @endphp
                <input name="name" id="name" placeholder="" type="text" class="form-control" value="{{$name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}