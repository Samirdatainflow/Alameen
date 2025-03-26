{{ Form::open(array('id'=>'quantityOnHandForm')) }}
    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Part No</label>
                <input name="part_no" id="view_part_no" type="text" class="form-control" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Part Name</label>
                <input name="part_no" id="view_part_nname" type="text" class="form-control" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label>Quantity *</label>
                <input name="current_stock" id="current_stock" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" placeholder="Enter quantity" type="number" class="form-control">
                <input name="product_id" type="hidden" id="product_id" value="{{$product_id}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="biningTable" style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Zone</th>
                        <th>Row</th>
                        <th>Rack</th>
                        <th>Level</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                    <tr>
                        <td>
                            <select class="form-control location-id valid" name="location_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($Location)) {
                                    foreach ($Location as $loc) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['location_id'])) {
                                                if($BinningLocationDetails[0]['location_id'] == $loc['location_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$loc['location_id']}}" {{$sel}}>{{$loc['location_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <select class="form-control zone-id valid" name="zone_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($ZoneMaster)) {
                                    foreach ($ZoneMaster as $zon) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['zone_id'])) {
                                                if($BinningLocationDetails[0]['zone_id'] == $zon['zone_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$zon['zone_id']}}" {{$sel}}>{{$zon['zone_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <select class="form-control row-id valid" name="row_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($RowData)) {
                                    foreach ($RowData as $ro) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['row_id'])) {
                                                if($BinningLocationDetails[0]['row_id'] == $ro['row_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$ro['row_id']}}" {{$sel}}>{{$ro['row_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <select class="form-control rack-id valid" name="rack_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($RackData)) {
                                    foreach ($RackData as $rac) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['rack_id'])) {
                                                if($BinningLocationDetails[0]['rack_id'] == $rac['rack_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$rac['rack_id']}}" {{$sel}}>{{$rac['rack_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <select class="form-control plate-id valid" name="plate_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($PlateData)) {
                                    foreach ($PlateData as $pla) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['plate_id'])) {
                                                if($BinningLocationDetails[0]['plate_id'] == $pla['plate_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$pla['plate_id']}}" {{$sel}}>{{$pla['plate_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <select class="form-control place-id valid" name="place_id" aria-invalid="false">
                                <option value="">Select</option>
                                @php
                                if(!empty($PlaceData)) {
                                    foreach ($PlaceData as $plac) {
                                        $sel = "";
                                        if(sizeof($BinningLocationDetails) > 0) {
                                            if(!empty($BinningLocationDetails[0]['place_id'])) {
                                                if($BinningLocationDetails[0]['place_id'] == $plac['place_id']) $sel = 'selected="selected"';
                                            }
                                        }
                                    @endphp
                                        <option value="{{$plac['place_id']}}" {{$sel}}>{{$plac['place_name']}}</option>
                                    @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}