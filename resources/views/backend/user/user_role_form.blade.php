{{ Form::open(array('id'=>'userRollForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label for="first_name">Role Name *</label>
                @php
                $name = "";
                if(!empty($user_roll_data[0]['name']))  {
                    $name = $user_roll_data[0]['name'];
                }
                $hidden_id = "";
                if(!empty($user_roll_data[0]['user_role_id']))  {
                    $hidden_id = $user_roll_data[0]['user_role_id'];
                }
                @endphp
                <input name="name" placeholder="Enter role" type="text" class="form-control" value="{{$name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}