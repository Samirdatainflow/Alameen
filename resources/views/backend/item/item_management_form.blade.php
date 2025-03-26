{{ Form::open(array('id'=>'ItemManagementForm')) }}
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group part-brand-search">
                <label for="Part Brand">Part Brand* <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-part-brand">Add NEW</a></label>
                <select class="form-control selectpicker" name="part_brand_id" id="part_brand_id" data-live-search="true" title="Search">
                    @php
                    if(!empty($PartBrand)) {
                        foreach($PartBrand as $pb_data){
                    @endphp
                    <option value="{{$pb_data['part_brand_id']}}" selected="selected">{{$pb_data['part_brand_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>

            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Part No *</label>
                @php
                $pmpno = "";
                if(!empty($item_data[0]['pmpno']))  {
                    $pmpno = $item_data[0]['pmpno'];
                }
                $hidden_id = "";
                if(!empty($item_data[0]['product_id']))  {
                    $hidden_id = $item_data[0]['product_id'];
                }
                @endphp 
                <input name="pmpno" id="pmpno" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="100" placeholder="Part No" type="text" class="form-control" value="{{$pmpno}}" style="text-transform: uppercase;">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Alternate Part No *</label>
                @php
                $alternate_part_no = "";
                if(!empty($item_data[0]['alternate_part_no']))  {
                    $alternate_part_no = $item_data[0]['alternate_part_no'];
                }
                @endphp 
                <input name="alternate_part_no" id="alternate_part_no" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="100" placeholder="Alternate Part No" type="text" class="form-control" value="{{$alternate_part_no}}" style="text-transform: uppercase;">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group part-name-search">
                <label for="">Part Name * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-part-name">Add NEW</a></label>
                <select class="form-control selectpicker" name="part_name_id" id="part_name_id" data-live-search="true" title="Search ">
                    @php
                    if(!empty($PartName)) {
                        foreach($PartName as $pn_data){
                    @endphp
                    <option value="{{$pn_data['part_name_id']}}" selected="selected">{{$pn_data['part_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Product Description </label>
                @php
                $product_desc = "";
                if(!empty($item_data[0]['product_desc']))  {
                    $product_desc = $item_data[0]['product_desc'];
                }
                @endphp
                <textarea placeholder="Product Description" type="text" class="form-control" name="product_desc">{{$product_desc}}</textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group car-manufacture-search">
                <label for="">Car Manufacture * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-car-manufacture">Add NEW</a></label>
                <select class="form-control selectpicker" name="car_manufacture_id" id="car_manufacture_id" data-live-search="true" title="Search" onchange="changeCarManufacture(this.value)">
                    @php
                    if(!empty($CarManufacture)) {
                        foreach($CarManufacture as $cm_data){
                    @endphp
                    <option value="{{$cm_data['car_manufacture_id']}}" selected="selected">{{$cm_data['car_manufacture']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4" style="display: none">
            <div class="position-relative form-group car-name-search">
                <select class="form-control selectpicker" name="car_name_id" id="car_name_id" data-live-search="true" title="Search Car Name *">
                    @php
                    if(!empty($CarName)) {
                        foreach($CarName as $cn_data){
                    @endphp
                    <option value="{{$cn_data['car_name_id']}}" selected="selected">{{$cn_data['car_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Car Model * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-car-model">Add NEW</a></label>
                <select class="form-control selectpicker list-car-model" multiple name="car_model_id[]" id="car_model_id" multiple data-live-search="true" title="Select">
                    @php
                    if(!empty($CarModel)) {
                        foreach($CarModel as $data) {
                            $sel = "";
                            if(!empty($item_data[0]['car_model'])) {
                                if(in_array($data['brand_id'],explode(',',$item_data[0]['car_model']))) $sel = 'selected="selected'; 
                            }
                    @endphp
                            <option value="{{$data['brand_id']}}" {{$sel}}>{{$data['brand_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">From Year</label>
                <select class="form-control" name="from_year">
                    <option value="">Select Year</option>
                    @php
                    $fromYear=(int)date('Y');
                    $toYear = "2000";
                    for(; $fromYear >= $toYear; $fromYear--) {
                        $sel = "";
                        if(!empty($item_data[0]['from_year']))  {
                            if($item_data[0]['from_year'] == $fromYear) $sel = 'selected="selected"';
                        }
                    @endphp
                    <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                    @php
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">From Month</label>
                <select class="form-control" name="from_month">
                    <option value="">Select Month</option>
                    @php
                    $MonthArray = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
                    foreach($MonthArray as $k=>$v) {
                    $sel = "";
                    if(!empty($item_data[0]['from_month']))  {
                        if($item_data[0]['from_month'] == $k) $sel = 'selected="selected"';
                    }
                    @endphp
                    <option value="{{$k}}" {{$sel}}>{{$v}}</option>
                    @php
                    }
                    @endphp
                    
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">To Year</label>
                <select class="form-control" name="to_year">
                    <option value="">Select Year</option>
                    @php
                    $fromYear = date("Y",strtotime("+4 year"));
                    $toYear = "2000";
                    for(; $fromYear >= $toYear; $fromYear--) {
                        $sel = "";
                        if(!empty($item_data[0]['to_year']))  {
                            if($item_data[0]['to_year'] == $fromYear) $sel = 'selected="selected"';
                        }
                    @endphp
                    <option value="{{$fromYear}}" {{$sel}}>{{$fromYear}}</option>
                    @php
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">To Month</label>
                <select class="form-control" name="to_month">
                    <option value="">Select Month</option>
                    @php
                    $MonthArray = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
                    foreach($MonthArray as $k=>$v) {
                    $sel = "";
                    if(!empty($item_data[0]['to_month']))  {
                        if($item_data[0]['to_month'] == $k) $sel = 'selected="selected"';
                    }
                    @endphp
                    <option value="{{$k}}" {{$sel}}>{{$v}}</option>
                    @php
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group category-search">
                <label for="">Category * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-category">Add NEW</a></label>
                <select class="form-control selectpicker" name="ct" id="ct" data-live-search="true" title="Search" onchange="changeCategory(this.value)">
                    @php
                    if(!empty($category_data)) {
                        foreach($category_data as $urData){
                    @endphp
                        <option value="{{$urData['category_id']}}" selected="selected">{{$urData['category_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Sub Category * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-sub-category">Add NEW</a></label>
                <select class="form-control list-sub-category" name="sct" id="sct" onchange="changeSubCategory(this.value)">
                    <option value="">Select</option>
                    @php
                    if(!empty($subcategory_id)) {
                        foreach($subcategory_id as $subcate){
                        $sel = "";
                        if(!empty($item_data[0]['sct']))  {
                            if($item_data[0]['sct'] == $subcate['sub_category_id']) $sel = 'selected="selected'; 
                        } 
                    @endphp
                    <option value="{{$subcate['sub_category_id']}}" {{$sel}}>{{ $subcate['sub_category_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Group * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-group">Add NEW</a></label>
                <select class="form-control" name="gr" id="gr">
                    <option selected="" disabled="">Select</option>
                    @php
                    if(!empty($group_id)) {
                        foreach($group_id as $urData){ 
                        $sel = "";
                        if(!empty($item_data[0]['gr']))  {
                            
                            if($item_data[0]['gr'] == $urData['group_id']) $sel = 'selected="selected'; 
                            } 
                    @endphp
                    <option value="{{$urData['group_id']}}" {{$sel}}>{{ $urData['group_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Unit * <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-unit">Add NEW</a></label>
                <select class="form-control" name="unit" id="unit">
                    <option selected="" disabled="">Select</option>
                    @php
                    if(!empty($unit_id)) {
                        foreach($unit_id as $urData){
                        $sel = "";
                        if(!empty($item_data[0]['unit']))  {
                            if($item_data[0]['unit'] == $urData['unit_id']) $sel = 'selected="selected'; 
                        }
                    @endphp
                    <option value="{{$urData['unit_id']}}" {{$sel}}>{{ $urData['unit_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row" style="display:none">
        <div class="col-md-12">
            @php
            if(!empty($Engine)) {
                foreach($Engine as $eng) {
                @endphp
                <a href="javascript::void(0)" class="badge badge-success remove-engine" data-id="{{$eng['engine_id']}}">{{$eng['engine_name']}} <i class="fa fa-close"></i></a>
                @php
                }
            }
            @endphp
            <p>* Please give ',' or press enter after each Engine.</p>
            <div class="position-relative form-group">
                <textarea name="engine" id="engine" placeholder="Engine " class="form-control input-tags" data-role="tagsinput" style="width: 100%"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @php
            if(!empty($ChassisModel)) {
                foreach($ChassisModel as $cmd) {
                @endphp
                <a href="javascript::void(0)" class="badge badge-success remove-chassis-model" data-id="{{$cmd['chassis_model_id']}}">{{$cmd['chassis_model']}} <i class="fa fa-close"></i></a>
                @php
                }
            }
            @endphp
            <p>* Please give ',' or press enter after each Chassis / Model.</p>
            <div class="position-relative form-group">
                <textarea name="chassis_model" id="chassis_model" placeholder="Engine " class="form-control input-tags" data-role="tagsinput" style="width: 100%"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @php
            if(!empty($ManufacturingNo)) {
                foreach($ManufacturingNo as $man) {
                @endphp
                <a href="javascript::void(0)" class="badge badge-success remove-manufacturing-no" data-id="{{$man['manufacturing_no_id']}}">{{$man['manufacturing_no']}} <i class="fa fa-close"></i></a>
                @php
                }
            }
            @endphp
            <p>* Please give ',' or press enter after each Manufacturer no.</p>
            <div class="position-relative form-group">
                @php
                $manfg_no = "";
                if(!empty($item_data[0]['manfg_no']))  {
                    $manfg_no = $item_data[0]['manfg_no'];
                }
                @endphp
                <textarea name="manfg_no" id="manfg_no" placeholder="Manufacturer No" class="form-control input-tags">{{$manfg_no}}</textarea>
            </div>
        </div>
    </div>
    <div class="row" style="display:none">
        <div class="col-md-12">
            @php
            if(!empty($AlternatePartNo)) {
                foreach($AlternatePartNo as $alt) {
                @endphp
                <a href="javascript::void(0)" class="badge badge-success remove-alternate-no" data-id="{{$alt['alternate_part_no_id']}}">{{$alt['alternate_no']}} <i class="fa fa-close"></i></a>
                @php
                }
            }
            @endphp
            <p>* Please give ',' or press enter after each Alternate part no.</p>
            <div class="position-relative form-group">
                @php
                $altn_part = "";
                if(!empty($item_data[0]['altn_part']))  {
                    $altn_part = $item_data[0]['altn_part'];
                }
                @endphp
                <textarea name="altn_part" id="altn_part" placeholder="Alternate Part No"  class="form-control input-tags" >{{$altn_part}}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Selling Price </label>
                @php
                $selling_price = "";
                if(!empty($item_data[0]['selling_price']))  {
                    $selling_price = $item_data[0]['selling_price'];
                }
                @endphp                 
                <input name="selling_price" id="selling_price" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Selling Price" type="number" class="form-control" value="{{$selling_price}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Retail Price </label>
                @php
                $pmrprc = "";
                if(!empty($item_data[0]['pmrprc']))  {
                    $pmrprc = $item_data[0]['pmrprc'];
                }
                @endphp                 
                <input name="pmrprc" id="pmrprc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Retail Price" type="number" class="form-control" value="{{$pmrprc}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Mark Up % </label>
                @php
                $mark_up = "";
                if(!empty($item_data[0]['mark_up']))  {
                    $mark_up = $item_data[0]['mark_up'];
                }
                @endphp
                <input name="mark_up" id="mark_up" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Mark Up %" type="number" class="form-control" max="100" value="{{$mark_up}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">LC Price </label>
                @php
                $lc_price = "";
                if(!empty($item_data[0]['lc_price']))  {
                    $lc_price = $item_data[0]['lc_price'];
                }
                @endphp
                <input name="lc_price" id="lc_price" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="LC Price" type="number" class="form-control" value="{{$lc_price}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">LC Price Date </label>
                @php
                $lc_date = "";
                if(!empty($item_data[0]['lc_date']))  {
                    if($item_data[0]['lc_date'] > 0) $lc_date = $item_data[0]['lc_date'];
                }
                @endphp
                <input name="lc_date" id="lc_date" placeholder="LC Price Date" type="text" readonly="readonly" class="form-control datepicker" value="{{$lc_date}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Previous LC Price </label>
                @php
                $prvious_lc_price = "";
                if(!empty($item_data[0]['prvious_lc_price']))  {
                    $prvious_lc_price = $item_data[0]['prvious_lc_price'];
                }
                @endphp
                <input name="prvious_lc_price" id="prvious_lc_price" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Previous LC Price" type="number" class="form-control" value="{{$prvious_lc_price}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Previous LC Price Date </label>
                @php
                $prvious_lc_date = "";
                if(!empty($item_data[0]['prvious_lc_date']))  {
                    if($item_data[0]['prvious_lc_date'] > 0) $prvious_lc_date = $item_data[0]['prvious_lc_date'];
                }
                @endphp
                <input name="prvious_lc_date" id="prvious_lc_date" placeholder="Previous LC Price Date" type="text" readonly="readonly" class="form-control datepicker" value="{{$prvious_lc_date}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Minimum Order Qty *</label>
                @php
                $moq = "";
                if(!empty($item_data[0]['moq']))  {
                    $moq = $item_data[0]['moq'];
                }
                @endphp
                <input name="moq" id="moq" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Minimum Order Qty" type="number" class="form-control" value="{{$moq}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Country of Origin <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-country">Add NEW</a></label>
                <select class="form-control" name="country_id" id="country_id">
                    <option selected="" disabled="">Select</option>
                    @php
                    if(!empty($country_id)) {
                        foreach($country_id as $urData){ 
                        $sel = "";
                        if(!empty($item_data[0]['country_of_origin']))  {
                            if($item_data[0]['country_of_origin'] == $urData['country_id']) $sel = 'selected="selected"';
                        } 
                    @endphp
                    <option value="{{$urData['country_id']}}" {{$sel}}>{{ $urData['country_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Supplier </label>
                <select class="form-control selectpicker" name="supplier_id[]" id="supplier_id" multiple data-live-search="true" title="Select">
                    @php
                    if(!empty($supplier_id)) {
                        foreach($supplier_id as $urData){ 
                        $sel = "";
                        if(!empty($item_data[0]['supplier_id']))  {
                            if(in_array($urData['supplier_id'],explode(',',$item_data[0]['supplier_id']))) $sel = 'selected="selected'; 
                        }
                        // if(!empty($item_data[0]['supplier_id']))  {
                        //     if($item_data[0]['supplier_id'] == $urData['supplier_id']) $sel = 'selected="selected"';
                        // } 
                    @endphp
                    <option value="{{$urData['supplier_id']}}" {{$sel}}>{{ $urData['full_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Supplier Currency <a href="javascript:void(0)" class="badge badge-pill badge-info ml-2 new-supplier-currency">Add NEW</a></label>
                @php
                $supplier_currency = "";
                if(!empty($item_data[0]['supplier_currency']))  {
                    $supplier_currency = $item_data[0]['supplier_currency'];
                }
                $selected_currency='';
                @endphp
                <select name="supplier_currency" id="supplier_currency" class="form-control">
                    <option value="">Select supplier currency</option>
                    @foreach($currency as $cur)
                        @php $sel = ""; @endphp
                        @if($cur['currency_id'] == $supplier_currency)
                         @php $sel = 'selected="selected"'; @endphp
                        @endif
                    <option value="{{$cur['currency_id']}}" {{$sel}}>{{$cur['currency_code']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4" style="display: none">
            <div class="position-relative form-group">
                <label for="">Re Order Level</label>
                @php
                $re_order_level = "";
                if(!empty($item_data[0]['re_order_level']))  {
                    $re_order_level = $item_data[0]['re_order_level'];
                }
                @endphp               
                <input name="re_order_level" id="re_order_level" placeholder="Re Order Level" type="text" class="form-control" value="{{$re_order_level}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Reorder Qty</label>
                @php
                $no_re_order = "";
                if(!empty($item_data[0]['no_re_order']))  {
                    $no_re_order = $item_data[0]['no_re_order'];
                }
                @endphp
                <input name="no_re_order" id="no_re_order" placeholder="No Of Reorder" type="number" class="form-control" value="{{$no_re_order}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Stop Sale Quantity</label>
                @php
                $stop_sale = "";
                if(!empty($item_data[0]['stop_sale']))  {
                    $stop_sale = $item_data[0]['stop_sale'];
                }
                @endphp
                <input name="stop_sale" id="stop_sale" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Stop Sale Quantity" type="number" class="form-control" value="{{$stop_sale}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Warehouses</label>
                <select class="form-control selectpicker" name="warehouse_id[]" id="warehouse_id" multiple data-live-search="true" title="Select">
                    @php
                    if(!empty($warehouse_id)) {
                        foreach($warehouse_id as $urData){
                        $sel = "";
                        if(!empty($item_data[0]['warehouse_id']))  {
                            if(in_array($urData['warehouse_id'],explode(',',$item_data[0]['warehouse_id']))) $sel = 'selected="selected'; 
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
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Alert stock</label>
                @php
                $stock_alert = "";
                if(!empty($item_data[0]['stock_alert']))  {
                    $stock_alert = $item_data[0]['stock_alert'];
                }
                @endphp
                <input name="stock_alert" id="stock_alert" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Alert stock" type="number" class="form-control" value="{{$stock_alert}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Reserved Qty</label>
                @php
                $reserved_qty = "";
                if(!empty($item_data[0]['reserved_qty']))  {
                    $reserved_qty = $item_data[0]['reserved_qty'];
                }
                @endphp
                <input name="reserved_qty" id="reserved_qty" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Reserved Qty" type="number" class="form-control" value="{{$reserved_qty}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Allocation Qty</label>
                @php
                $allocation_qty = "";
                if(!empty($item_data[0]['allocation_qty']))  {
                    $allocation_qty = $item_data[0]['allocation_qty'];
                }
                @endphp
                <input name="allocation_qty" id="allocation_qty" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Allocation Qty" type="number" class="form-control" value="{{$allocation_qty}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Last Month Stock</label>
                @php
                $last_month_stock = "";
                if(!empty($item_data[0]['last_month_stock']))  {
                    $last_month_stock = $item_data[0]['last_month_stock'];
                }
                @endphp
                <input name="last_month_stock" id="last_month_stock" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Last Month Stock" type="number" class="form-control" value="{{$last_month_stock}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Quantity In Transit</label>
                @php
                $qty_in_transit = "";
                if(!empty($item_data[0]['qty_in_transit']))  {
                    $qty_in_transit = $item_data[0]['qty_in_transit'];
                }
                @endphp
                <input name="qty_in_transit" id="qty_in_transit" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Quantity In Transit" type="number" class="form-control" value="{{$qty_in_transit}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Quantity On Order</label>
                @php
                $qty_on_order = "";
                if(!empty($item_data[0]['qty_on_order']))  {
                    $qty_on_order = $item_data[0]['qty_on_order'];
                }
                @endphp
                <input name="qty_on_order" id="qty_on_order" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Quantity On Order" type="number" class="form-control" value="{{$qty_on_order}}">
            </div>
        </div>
        @php
        if($hidden_id == "") {
        @endphp
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="">Quantity On Hand *</label>
                @php
                $current_stock = "";
                if(!empty($item_data[0]['current_stock']))  {
                    $current_stock = $item_data[0]['current_stock'];
                }
                @endphp
                <input name="current_stock" id="current_stock" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Quantity On Hand" type="number" class="form-control" value="{{$current_stock}}">
            </div>
        </div>
        @php
        }
        @endphp
    </div>
    <div class="row csv-upload">
        <div class="col-md-4">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row csv-upload">
        <div class="col-md-4">
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
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit">Save</button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}