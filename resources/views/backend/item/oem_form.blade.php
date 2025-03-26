{{ Form::open(array('id'=>'itemOemForm')) }}
    <div class="form-row">
        @php
        $hidden_id = "";
        if(!empty($oem_data[0]['oem_id']))  {
            $hidden_id = $oem_data[0]['oem_id'];
        }
        @endphp 
        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
        <div class="col-md-12">
            <div class="position-relative form-group model-search">
                <select class="form-control" name="brand_id" id="brand_id" data-live-search="true" title="Search Model By Name . *">
                    @php
                    if(!empty($model_data)) {
                        foreach($model_data as $data){
                    @endphp
                    <option value="{{$data['brand_id']}}" selected="selected">{{$data['brand_name']}}</option>
                    @php
                            }
                        }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <!-- @php
            //print_r($category_data);
            @endphp -->

            <div class="position-relative form-group ">
                <select name="category_id" class="form-control" id="category_id">
                    <option value="">Select Category *</option>
                    @php
                    if(!empty($category_data)) {
                        foreach($category_data as $data){
                    @endphp
                        <option value="{{$data['category_id']}}" selected="selected">{{$data['category_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div> 
        </div>
        <div class="col-md-12">
            <!-- @php
            //print_r($subcategory_data);
            @endphp -->
            <div class="position-relative form-group">
                <select name="sub_category_id" class="form-control" id="sub_category_id">
                    <option value="">Select Sub Category *</option>
                    @php
                    if(!empty($subcategory_data)) {
                        foreach($subcategory_data as $data){
                    @endphp
                        <option value="{{$data['sub_category_id']}}" selected="selected">{{$data['sub_category_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                @php
                $oem_no = "";
                if(!empty($oem_data[0]['oem_no']))  {
                    $oem_no = $oem_data[0]['oem_no'];
                }
                @endphp
                <input name="oem_no" placeholder="Enter Oem Number *" type="text" class="form-control" value="{{$oem_no}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                @php
                $oem_details = "";
                if(!empty($oem_data[0]['oem_details']))  {
                    $oem_details = $oem_data[0]['oem_details'];
                }
                @endphp
                <input name="oem_details" placeholder="Enter Oem Details *" type="text" class="form-control" value="{{$oem_details}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}