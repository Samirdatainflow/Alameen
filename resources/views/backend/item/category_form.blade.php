{{ Form::open(array('id'=>'itemCategoryForm')) }}
    <div class="form-row">
        <div class="col-md-12" style="display: none">
            <div class="position-relative form-group">
                <label></label>
                <select class="form-control" name="brand_id" id="brand_id" data-live-search="true" title="Search Model By Name . *">
                    @php
                    if(!empty($brand_data)) {
                        foreach($brand_data as $urData){
                    @endphp
                    <option value="{{$urData['brand_id']}}" selected="selected">{{$urData['brand_name']}}</option>
                    @php
                            }
                        }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Category Name *</label>
                @php
                $category_name = "";
                if(!empty($category_data[0]['category_name']))  {
                    $category_name = $category_data[0]['category_name'];
                }
                $hidden_id = "";
                if(!empty($category_data[0]['category_id']))  {
                    $hidden_id = $category_data[0]['category_id'];
                }
                @endphp
                <input name="category_name" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="30" placeholder="" type="text" class="form-control" value="{{$category_name}}"> 
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Category Description *</label>
                @php
                $category_description = "";
                if(!empty($category_data[0]['category_description']))  {
                    $category_description = $category_data[0]['category_description'];
                }
                @endphp
                <input name="category_description" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="100" placeholder="" type="text" class="form-control" value="{{$category_description}}">
            </div>
        </div>
        <div class="col-md-12" style="display: none">
            <div class="position-relative form-group">
                <label>Warehouse</label>
                <select class="form-control" name="warehouse_id" id="warehouse_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($warehouse_id)) {
                        foreach($warehouse_id as $urData){
                        $sel = "";
                        if(!empty($category_data[0]['warehouse_id']))  {
                            
                            if($category_data[0]['warehouse_id'] == $urData['warehouse_id']) $sel = 'selected="selected';
                        }
                    @endphp
                    <option value="{{$urData['warehouse_id']}}" {{$sel}}>{{ $urData['name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
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
                <label>Bulk Category Create</label>
                <input type="file" id="product_categories_csv" name="product_categories_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_categories_csv" name="product_categories_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-product-categories" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}