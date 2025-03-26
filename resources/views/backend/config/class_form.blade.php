{{ Form::open(array('id'=>'configClassForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Class Value *</label>
                @php
                $value = "";
                if(!empty($class_data[0]['value']))  {
                    $value = $class_data[0]['value'];
                }
                $hidden_id = "";
                if(!empty($class_data[0]['class_id']))  {
                    $hidden_id = $class_data[0]['class_id'];
                }
                @endphp
                <input name="value" id="value" placeholder="" type="text" class="form-control" value="{{$value}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Class Description *</label>
                @php
                $description = "";
                if(!empty($class_data[0]['description']))  {
                    $description = $class_data[0]['description'];
                }
                @endphp
                <input name="description" id="description" placeholder="" type="text" class="form-control" value="{{$description}}">
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Class Create</label>
                <input type="file" id="class_csv" name="class_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="class_csv" name="class_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-class" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}