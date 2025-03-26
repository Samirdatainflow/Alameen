{{ Form::open(array('id'=>'VatTypeForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Type *</label>
                @php
                $vat_type = "";
                if(!empty($VatTypeData[0]['description']))  {
                    $vat_type = $VatTypeData[0]['description'];
                }
                $hidden_id = "";
                if(!empty($VatTypeData[0]['vat_type_id']))  {
                    $hidden_id = $VatTypeData[0]['vat_type_id'];
                }
                @endphp
                <input name="vat_type" id="vat_type" placeholder="" type="text" class="form-control" value="{{$vat_type}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Percentage </label>
                @php
                $percentage = "";
                if(!empty($VatTypeData[0]['percentage']))  {
                    $percentage = $VatTypeData[0]['percentage'];
                }
                @endphp
                <input name="percentage" id="percentage" placeholder="" type="text" class="form-control" value="{{$percentage}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}
