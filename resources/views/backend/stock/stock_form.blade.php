{{ Form::open(array('id'=>'StockForm')) }}
    <div class="form-row">
        @php
        $hidden_id = "";
        if(!empty($stock_data[0]['stock_id']))  {
            $hidden_id = $stock_data[0]['stock_id'];
        }
        @endphp
        <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Location *</label>
                <select class="form-control" name="location_id" id="location_id">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($location_id)) {
                        foreach($location_id as $urData){
                        $sel = "";
                        if(!empty($stock_data[0]['location_id']))  {
                            if($stock_data[0]['location_id'] == $urData['location_id']) $sel = 'selected="selected'; 
                        } 
                    @endphp
                    <option value="{{$urData['location_id']}}" {{$sel}}>{{ $urData['location_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>    
            </div>
        </div>
        <div class="col-md-6">
            <label>Warehouse *</label>
            <select class="form-control" name="warehouse_id" id="warehouse_id">
                <option selected="" disabled="">Select </option>
                <option value=""></option> 
                
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Part No *</label>
                @php
                $pmpno = "";
                if(!empty($stock_data[0]['pmpno']))  {
                    $pmpno = $stock_data[0]['pmpno'];
                }
                @endphp 
                <input name="pmpno" id="pmpno" placeholder="" type="text" class="form-control" value="{{$pmpno}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Product Id *</label>
                <input name="product_id" id="product_id" placeholder="" type="number" class="form-control" onkeydown="return false;">
            </div>
        </div>
        
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Product Name *</label>
                <select name="product_name" id="product_name" class="form-control">
                    <option value="">Select</option>
                    @php
                    if(sizeof($PartName) > 0) {
                        foreach($PartName as $pData) {
                    @endphp
                    <option value="{{$pData['part_name_id']}}">{{$pData['part_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                    <label>Unit *</label>
                   <select class="form-control" name="stock_units" id="stock_units">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($unit_id)) {
                        foreach($unit_id as $urData){
                        $sel = "";
                        if(!empty($stock_data[0]['stock_units']))  {
                            if($stock_data[0]['stock_units'] == $urData['unit_id']) $sel = 'selected="selected'; 
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
    
    <div class="form-row">
        <div class="col-md-6">
           <div class="position-relative form-group">
                <label>Lot *</label>
                <select class="form-control" name="lot_name" id="lot_name">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($lot_id)) {
                        foreach($lot_id as $urData){
                        $sel = "";
                        if(!empty($stock_data[0]['lot_name']))  {
                            if($stock_data[0]['lot_name'] == $urData['lot_id']) $sel = 'selected="selected'; 
                        } 
                    @endphp
                    <option value="{{$urData['lot_id']}}" {{$sel}}>{{ $urData['lot_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div> 
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Load *</label>
                <select class="form-control" name="unit_load" id="unit_load">
                    <option selected="" disabled="">Select </option>
                    @php
                    if(!empty($unit_load_id)) {
                        foreach($unit_load_id as $urData){
                        $sel = "";
                        if(!empty($stock_data[0]['unit_load']))  {
                            if($stock_data[0]['unit_load'] == $urData['unit_load_id']) $sel = 'selected="selected'; 
                        } 
                    @endphp
                    <option value="{{$urData['unit_load_id']}}" {{$sel}}>{{ $urData['unit_load_type']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>    
            </div> 
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                    <label>Quantity *</label>
                    @php
                    $qty = "";
                    if(!empty($stock_data[0]['qty']))  {
                        $qty = $stock_data[0]['qty'];
                    }
                    @endphp 
                <input name="qty" id="qty" placeholder="Enter Only Number" type="number" class="form-control" value="{{$qty}}">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label>Reserved Quantity *</label>
                @php
                $reserved_qty = "";
                if(!empty($stock_data[0]['reserved_qty']))  {
                    $reserved_qty = $stock_data[0]['reserved_qty'];
                }
                @endphp
                <input name="reserved_qty" id="reserved_qty" placeholder="Enter Only Number" type="number" class="form-control" value="{{$reserved_qty}}">
            </div>
        </div>
    </div>    
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}