{{ Form::open(array('id'=>'BinningLocationForm')) }}
    <div class="row">
        <input type="hidden" name="hidden_id" value="{{$order_id}}">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px">Product Name</th>
                        <th style="width: 130px">Part No</th>
                        <th style="width: 90px">Quantity</th>
                        <th>Location</th>
                        <th>Zone</th>
                        <th>Row</th>
                        <th>Rack</th>
                        <th>Plate</th>
                        <th>Place</th>
                        {{-- <th>Max Capacity</th>
                        <th>Remaining Capacity</th> --}}
                    </tr>
                </thead>
                <tbody id="entryProductTbody">
                @php
                //print_r($BinningLocationDetails); exit();
                $i= 1;
                if(!empty($BinningLocationDetails)) {
                    foreach($BinningLocationDetails as $data) {
                @endphp
                    <tr>
                        <td>{{$data['part_name']}}
                            {{-- <input type="hidden" class="product-id" name="product_id[]" value="{{$data['product_id']}}">
                            <input type="text" class="form-control part-name" value="{{$data['part_name']}}" readonly> --}}
                        </td>
                        <td>{{$data['pmpno']}}
                            {{-- <input type="text" class="form-control pmpno" value="{{$data['pmpno']}}" readonly> --}}
                        </td>
                        <td>{{$data['quantity']}}
                            {{-- <input type="number" class="form-control quantity" name="quantity[]" value="{{$data['quantity']}}" readonly> --}}
                        </td>
                        <td>{{$data['location_name']}}
                            {{-- <select class="form-control location-id" name="location_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($Location)) {
                                    foreach($Location as $ldata) {
                                        $sel = "";
                                        if($ldata['location_id'] == $data['location_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$ldata['location_id']}}" {{$sel}}>{{$ldata['location_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                        </td>
                        <td>{{$data['zone_name']}}
                            {{-- <select class="form-control zone-id" name="zone_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($data['listZone'])) {
                                    foreach($data['listZone'] as $zone) {
                                        $sel = "";
                                        if($zone['zone_id'] == $data['zone_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$zone['zone_id']}}" {{$sel}}>{{$zone['zone_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                        </td>
                        <td>{{$data['row_name']}}
                            {{-- <select class="form-control row-id" name="row_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($data['listRow'])) {
                                    foreach($data['listRow'] as $row) {
                                        $sel = "";
                                        if($row['row_id'] == $data['row_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$row['row_id']}}" {{$sel}}>{{$row['row_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                        </td>
                        <td>{{$data['rack_name']}}
                            {{-- <select class="form-control rack-id" name="rack_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($data['listRack'])) {
                                    foreach($data['listRack'] as $rack) {
                                        $sel = "";
                                        if($rack['rack_id'] == $data['rack_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$rack['rack_id']}}" {{$sel}}>{{$rack['rack_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                        </td>
                        <td>{{$data['plate_name']}}
                            {{-- <select class="form-control plate-id" name="plate_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($data['listPlate'])) {
                                    foreach($data['listPlate'] as $plate) {
                                        $sel = "";
                                        if($plate['plate_id'] == $data['plate_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$plate['plate_id']}}" {{$sel}}>{{$plate['plate_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                        </td>
                        <td>{{$data['place_name']}}
                            {{-- <select class="form-control place-id" name="place_id[]">
                                <option value="">Select</option>
                                @php
                                if(!empty($data['listPlace'])) {
                                    foreach($data['listPlace'] as $place) {
                                        $sel = "";
                                        if($place['place_id'] == $data['place_id']) $sel='selected="selected"';
                                @endphp
                                    <option value="{{$place['place_id']}}" {{$sel}}>{{$place['place_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select> --}}
                            {{-- <input type="hidden" class="hidden-position" name="hidden_position" value="{{$data['place_id']}}"> --}}
                        </td>
                        {{-- <td>
                            <span class="max-capacity">{{$data['max_capacity']}}</span>
                        </td>
                        <td>
                            <span class="remaining-capacity">{{$data['remaining_capacity']}}</span>
                        </td> --}}
                    </tr>
                @php
                    }
                }
                @endphp
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <p class="text-right">
        {{-- <button type="submit" class="btn-shadow btn btn-info"> Update </button> --}}
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}