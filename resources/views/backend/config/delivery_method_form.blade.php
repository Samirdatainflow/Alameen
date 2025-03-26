{{ Form::open(array('id'=>'DeliveryMethodForm')) }} 
    <div class="row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Delivery Method*</label>
                @php
                $delivery_method = "";
                $hidden_id = "";
                @endphp
                @if(!empty($delivery_method_data))
                    @if(!empty($delivery_method_data[0]['delivery_method']))
                        @php
                        $delivery_method = $delivery_method_data[0]['delivery_method'];
                        $hidden_id = $delivery_method_data[0]['delivery_method_id'];
                        @endphp
                    @endif
                @endif
                <input name="delivery_method" placeholder="" type="text" class="form-control" value="{{$delivery_method}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Description *</label>
                @php
                $delivery_description = "";
                @endphp
                @if(!empty($delivery_method_data))
                    @if(!empty($delivery_method_data[0]['delivery_description']))
                        @php $delivery_description = $delivery_method_data[0]['delivery_description']; @endphp
                    @endif
                @endif
                <textarea class="form-control" name="delivery_description" placeholder="">{{$delivery_description}}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Warehouse *</label>
                <select class="form-control" name="warehouseid" onchange="chnageWarehouse(this.value)">
                    <option value=""> Select </option>
                    @if(!empty($warehouses_data))
                        @foreach($warehouses_data as $data)
                            @php
                            $sel = "";
                            @endphp
                            @if(!empty($delivery_method_data))
                                @if(!empty($delivery_method_data[0]['warehouseid']))
                                    @if($delivery_method_data[0]['warehouseid'] == $data['warehouse_id'])
                                    @php $sel = 'selected="selected"'; @endphp
                                    @endif
                                @endif
                            @endif
                            <option value="{{$data['warehouse_id']}}" {{$sel}}>{{$data['name']}}</option>
                        @endforeach
                    @endif
                    
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn-shadow btn btn-info" id="download_template" type="button"> Download Template </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Bulk Delivery Method Create</label>
                <input type="file" id="delivery_method_csv" name="delivery_method_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="delivery_method_csv" name="delivery_method_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-delivery-method" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}