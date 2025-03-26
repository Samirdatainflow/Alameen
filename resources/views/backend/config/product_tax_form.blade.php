{{ Form::open(array('id'=>'ProductTaxForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
            	<label>Tax Name *</label>
                @php
                $tax_name = ""; 
                if(!empty($product_tax_data[0]['tax_name']))  {
                    $tax_name = $product_tax_data[0]['tax_name'];
                }
                $hidden_id = "";
                if(!empty($product_tax_data[0]['tax_id']))  {
                    $hidden_id = $product_tax_data[0]['tax_id'];
                }
                @endphp
                <input name="tax_name" id="tax_name" placeholder="" type="text" class="form-control" value="{{$tax_name}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
            	<label>Tax Rate *</label>
                @php
                $tax_rate = "";
                if(!empty($product_tax_data[0]['tax_rate']))  {
                    $tax_rate = $product_tax_data[0]['tax_rate'];
                }
                @endphp
                <input name="tax_rate" id="tax_rate" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="" type="text" class="form-control" value="{{$tax_rate}}">
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
            	<label>Tax Type *</label>
                @php
                $tax_type = "";
                if(!empty($product_tax_data[0]['tax_type']))  {
                    $tax_type = $product_tax_data[0]['tax_type'];
                }
                @endphp
                <input name="tax_type" id="tax_type" placeholder="" type="text" class="form-control" value="{{$tax_type}}">
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
            	<label>Tax Description *</label>
                @php
                $tax_description = "";
                if(!empty($product_tax_data[0]['tax_description']))  {
                    $tax_description = $product_tax_data[0]['tax_description'];
                }
                @endphp
                <input name="tax_description" id="tax_description" placeholder="" type="text" class="form-control" value="{{$tax_description}}">
            </div> 
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
            	<label>Warehouse *</label>
                <select class="form-control" name="warehouse_id" id="warehouse_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($warehouse_id)) {
                        foreach($warehouse_id as $urData){
                        $sel = "";
                        if(!empty($product_tax_data[0]['warehouse_id']))  {
                            
                            if($product_tax_data[0]['warehouse_id'] == $urData['warehouse_id']) $sel = 'selected="selected';
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
        <div class="col-md-6">
            <div class="form-group">
                <label>Bulk Product Tax Create</label>
                <input type="file" id="product_tax_csv" name="product_tax_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="product_tax_csv" name="product_tax_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-product-tax" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}