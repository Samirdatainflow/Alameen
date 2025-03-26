{{ Form::open(array('id'=>'changePassForm')) }} 
<div class="card mb-2">
    <div data-parent="#accordion" id="collapseOne" aria-labelledby="headingOne" class="collapse show" style="">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label for="N_password">Password </label>
                        <input name="N_password" id="N_password"  type="text" class="form-control" value="">
                        <input name="hidden_id" type="hidden1" value="">
                    </div>
                    <div class="position-relative form-group">
                        <label for="c_password">Confrim Password </label>
                        <input name="c_password" id="c_password" type="text" class="form-control" value="">
                        <input name="hidden_id" type="hidden" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><br>
<p class="text-center"><button type="submit" id="submit" class="btn-shadow btn btn-info" value="Submit"> Submit </button></p>
{{ Form::close() }}