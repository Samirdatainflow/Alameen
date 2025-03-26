{{ Form::open(array('id'=>'productRateForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Default Rate *</label>
                        @php
                        $default_rate = "";
                        if(!empty($product_rate_data[0]['default_rate']))  {
                            $default_rate = $product_rate_data[0]['default_rate'];
                        }
                        $hidden_id = "";
                        if(!empty($product_rate_data[0]['rate_id']))  {
                            $hidden_id = $product_rate_data[0]['rate_id'];
                        }
                        @endphp
                        <input name="default_rate" id="default_rate" placeholder="" type="number" class="form-control" value="{{$default_rate}}">
                        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
                    </div>
                    <div class="col-md-6">
                        <label>Level 1 *</label>
                        @php
                        $level_1 = "";
                        if(!empty($product_rate_data[0]['level_1']))  {
                            $level_1 = $product_rate_data[0]['level_1'];
                        }
                        @endphp              
                        <input name="level_1" id="level_1" placeholder="" type="number" class="form-control" value="{{$level_1}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Level 2 *</label>
                        @php
                        $level_2 = "";
                        if(!empty($product_rate_data[0]['level_2']))  {
                            $level_2 = $product_rate_data[0]['level_2'];
                        }
                        @endphp
                        <input name="level_2" id="level_2" placeholder="" type="number" class="form-control" value="{{$level_2}}">
                    </div>
                    <div class="col-md-6">
                        <label>Level 3 *</label>
                        @php
                        $level_3 = "";
                        if(!empty($product_rate_data[0]['level_3']))  {
                            $level_3 = $product_rate_data[0]['level_3'];
                        }
                        @endphp                
                        <input name="level_3" id="level_3" placeholder="" type="number" class="form-control" value="{{$level_3}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Level 4 *</label>
                        @php
                        $level_4 = "";
                        if(!empty($product_rate_data[0]['level_4']))  {
                            $level_4 = $product_rate_data[0]['level_4'];
                        }
                        @endphp 
                        <input name="level_4" id="level_4" placeholder="" type="number" class="form-control" value="{{$level_4}}">
                    </div>
                    <div class="col-md-6">
                        <label></label>
                        @php
                        $level_5 = "";
                        if(!empty($product_rate_data[0]['level_5']))  {
                            $level_5 = $product_rate_data[0]['level_5'];
                        }
                        @endphp                
                        <input name="level_5" id="level_5" placeholder="Level 5 *" type="number" class="form-control" value="{{$level_5}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Warehouse *</label>
                        <select class="form-control" name="warehouse_id" id="warehouse_id">
                            <option selected="" disabled="">Select </option>
                            @php
                            if(!empty($warehouse_id)) {
                                foreach($warehouse_id as $urData){
                                $sel = "";
                                if(!empty($product_rate_data[0]['warehouse_id']))  {
                                    
                                    if($product_rate_data[0]['warehouse_id'] == $urData['warehouse_id']) $sel = 'selected="selected';
                                }
                            @endphp
                            <option value="{{$urData['warehouse_id']}}" {{$sel}}>{{ $urData['name']}}</option>
                            @php
                                }
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Product *</label>
                        @php
                        //print_r($product_data);
                        @endphp
                        <select class="form-control selectpicker"  name="product_id" id="product_id" data-live-search="true" title="Search By Name Or Part No.">
                            @php
                            if(!empty($product_data)) {
                                foreach($product_data as $data){
                            @endphp
                            <option value="{{$data->product_id}}" selected="selected">{{$data->part_name}} ({{$data->pmpno}})</option>
                            @php
                                    }
                                }
                            @endphp
                        </select>
                    </div>
                </div>
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
        <div class="col-md-6">
            <div class="form-group">
                <label>Bulk Product Rate Create</label>
                <input type="file" id="product_rate_csv" name="product_rate_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_rate_csv" name="product_rate_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-product-rate" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}