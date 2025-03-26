{{ Form::open(array('id'=>'ItemManagementForm')) }}
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Item Create</label>
                <input type="file" id="item_management_csv" name="item_management_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="item_management_csv" name="item_management_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-item-management" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        {{-- <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> --}}
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}