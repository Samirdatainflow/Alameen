{{ Form::open(array('id'=>'itemGroupForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Group Name *</label>
                @php
                $group_name = "";
                if(!empty($group_data[0]['group_name']))  {
                    $group_name = $group_data[0]['group_name'];
                }
                $hidden_id = "";
                if(!empty($group_data[0]['group_id']))  {
                    $hidden_id = $group_data[0]['group_id'];
                }
                @endphp
                <input name="group_name" id="group_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="" type="text" class="form-control" value="{{$group_name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
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
                <label>Bulk Group Create</label>
                <input type="file" id="product_group_csv" name="product_group_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_group_csv" name="product_group_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-group" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}