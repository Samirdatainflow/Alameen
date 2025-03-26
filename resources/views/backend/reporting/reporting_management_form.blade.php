{{ Form::open(array('id'=>'userReportingForm')) }} 
    <div class="row">
        @php
        $hidden_id = "";
        if(!empty($ReportingManagementData)) {
            $hidden_id = $ReportingManagementData[0]['reporting_management_id'];
        }
        @endphp
        <input type="hidden" name="hidden_id" value="{{$hidden_id}}">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Reporting Manager">Reporting Manager Name*
                <select class="form-control selectpicker" name="reporting_manager_id[]" id="reporting_manager_id" data-live-search="true" title="Select" multiple>
                    
                    @php
                    if(!empty($UsersData)) {
                        foreach($UsersData as $udata){
                        
                        $sel = "";
                        if(!empty($ReportingManagementData[0]['reporting_manager_id'])) {
                            
                            $reporting_managerIds = unserialize($ReportingManagementData[0]['reporting_manager_id']);
                            if (in_array($udata['user_id'], $reporting_managerIds)) {
                                $sel = 'selected="selected"';
                            }
                        }
                        
                    @endphp
                    <option value="{{$udata['user_id']}}" {{$sel}}>{{$udata['first_name']}} {{$udata['last_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label for="Reporting Manager">Reporting Name*
                <select class="form-control selectpicker" name="reporting_id[]" id="reporting_id" data-live-search="true" title="Select" multiple>
                    @php
                    if(!empty($UsersData)) {
                        foreach($UsersData as $udata){
                        
                        $sel = "";
                        if(!empty($ReportingManagementData[0]['reporting_id'])) {
                            
                            $reporting_managerIds = unserialize($ReportingManagementData[0]['reporting_id']);
                            if (in_array($udata['user_id'], $reporting_managerIds)) {
                                $sel = 'selected="selected"';
                            }
                        }
                        
                    @endphp
                    <option value="{{$udata['user_id']}}" {{$sel}}>{{$udata['first_name']}} {{$udata['last_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}