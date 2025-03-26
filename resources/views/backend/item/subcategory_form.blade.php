{{ Form::open(array('id'=>'itemSubCategoryForm')) }}
    <div class="form-row">
        @php
        $hidden_id = "";
        if(!empty($sub_category_data[0]['sub_category_id']))  {
            $hidden_id = $sub_category_data[0]['sub_category_id'];
        }
        @endphp 
        <input name="hidden_id" id="hidden_id" type="hidden" value="{{$hidden_id}}">
        <div class="col-md-12" style="display: none">
            <div class="position-relative form-group model-search">
                <label></label>
                <select class="form-control" name="brand_id" id="brand_id" data-live-search="true" title="Search Model By Name . *">
                    @php
                    if(!empty($model_data)) {
                        foreach($model_data as $urData){
                    @endphp
                    <option value="{{$urData['brand_id']}}" selected="selected">{{$urData['brand_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12 csv-upload">
            <div class="position-relative form-group category-search">
                <label>Category *</label>
                <select name="category_id" class="form-control" id="category_id" data-live-search="true" title="Search">
					@php
					if(!empty($category_data)) {
						foreach($category_data as $cData){
					@endphp
						<option value="{{$cData['category_id']}}" selected="selected">{{$cData['category_name']}}</option>
					@php
						}
					}
					@endphp
                </select>
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Sub Category Name *</label>
                @php
                $sub_category_name = "";
                if(!empty($sub_category_data[0]['sub_category_name']))  {
                    $sub_category_name = $sub_category_data[0]['sub_category_name'];
                }
                @endphp
                <input name="sub_category_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="" type="text" class="form-control" value="{{$sub_category_name}}">
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
                <label>Bulk Sub Category Create</label>
                <input type="file" id="product_sub_category_csv" name="product_sub_category_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_sub_category_csv" name="product_sub_category_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-sub-category" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}