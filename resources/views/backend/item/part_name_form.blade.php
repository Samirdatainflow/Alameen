{{ Form::open(array('id'=>'PartNameForm')) }}
    <div class="form-row">
        @php
        $hidden_id = "";
        if(!empty($PartBrand[0]['part_name_id']))  {
            $hidden_id = $PartBrand[0]['part_name_id'];
        }
        @endphp 
        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Part Name *</label>
                @php
                $part_name = "";
                if(!empty($PartBrand[0]['part_name']))  {
                    $part_name = $PartBrand[0]['part_name'];
                }
                @endphp
                <input name="part_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="" type="text" class="form-control" value="{{$part_name}}">
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
                <label>Bulk Part Name Create</label>
                <input type="file" id="part_name_csv" name="part_name_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="part_name_csv" name="part_name_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-part-name" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}