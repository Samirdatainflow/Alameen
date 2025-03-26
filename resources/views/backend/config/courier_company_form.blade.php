{{ Form::open(array('id'=>'configCourierCompanyForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Company Name *</label>
                @php
                $company_name = "";
                if(!empty($courier_company_data[0]['company_name']))  {
                    $company_name = $courier_company_data[0]['company_name'];
                }
                $hidden_id = "";
                if(!empty($courier_company_data[0]['courier_company_id']))  {
                    $hidden_id = $courier_company_data[0]['courier_company_id'];
                }
                @endphp
                <input name="company_name" id="company_name" placeholder="" type="text" class="form-control" value="{{$company_name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}