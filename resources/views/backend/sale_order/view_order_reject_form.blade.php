{{ Form::open(array('id'=>'OrderRejectForm')) }}
<div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-12">
                        <textarea name="reason" class="form-control" placeholder="Enter Reason*"></textarea>
                        <input name="order_id" type="hidden" value="{{$order_id}}">
                    </div>
                    
                </div>
            </div>
        </div>
        <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
 {{ Form::close() }}