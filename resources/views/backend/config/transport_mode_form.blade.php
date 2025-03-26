{{ Form::open(array('id'=>'configTransportModeForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Transport Mode *</label>
                @php
                $transport_mode = "";
                if(!empty($transport_mode_data[0]['transport_mode']))  {
                    $transport_mode = $transport_mode_data[0]['transport_mode'];
                }
                $hidden_id = "";
                if(!empty($transport_mode_data[0]['transport_mode_id']))  {
                    $hidden_id = $transport_mode_data[0]['transport_mode_id'];
                }
                @endphp
                <input name="transport_mode" id="transport_mode" placeholder="" type="text" class="form-control" value="{{$transport_mode}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}