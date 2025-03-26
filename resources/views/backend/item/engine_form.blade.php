{{ Form::open(array('id'=>'EngineForm')) }}
    <div class="form-row">
        @php
        $hidden_id = "";
        if(!empty($Engine[0]['engine_id']))  {
            $hidden_id = $Engine[0]['engine_id'];
        }
        @endphp 
        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
        <div class="col-md-12">
            <div class="position-relative form-group">
                @php
                $engine_name = "";
                if(!empty($Engine[0]['engine_name']))  {
                    $engine_name = $Engine[0]['engine_name'];
                }
                @endphp
                <input name="engine_name" placeholder="Enter Engine Name *" type="text" class="form-control" value="{{$engine_name}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}