@php
if(!empty($LocationDetails)) {
@endphp
<div class="row">
    <div class="col-md-12">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Part No</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Zone</th>
                    <th>Row</th>
                    <th>Rack</th>
                    <th>Plate</th>
                    <th>Place</th>
                </tr>
            </thead>
            <tbody id="entryProductTbody">
                @php
                if(!empty($LocationDetails)) {
                    foreach($LocationDetails as $data) {
                @endphp
                    <tr>
                        <td>{{$data->part_name}}</td>
                        <td>{{$data->pmpno}}</td>
                        <td>{{$data->quantity}}</td>
                        <td>{{$data->location_name}}</td>
                        <td>{{$data->zone_name}}</td>
                        <td>{{$data->row_name}}</td>
                        <td>{{$data->rack_name}}</td>
                        <td>{{$data->plate_name}}</td>
                        <td>{{$data->place_name}}</td>
                    </tr>
                @php
                    }
                }else {
                    @endphp
                    <tr>
                        <td colspan="9">No location found of this product...</td>
                    </tr>
                    @php
                }
                @endphp
            </tbody>
        </table>
    </div>
</div>
<br>
<p class="text-right">
    <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>
@php
}else{
@endphp
{{ Form::open(array('id'=>'BinningLocationForm')) }}
<div class="row">
    <div class="col-md-12">
        <table style="width: 100%;" class="table table-hover table-striped table-bordered" id="biningTable">
            <thead>
                <tr>
                    <!--<th>Product Name</th>-->
                    <!--<th>Part No</th>-->
                    <!--<th>Quantity</th>-->
                    <th>Location</th>
                    <th>Zone</th>
                    <th>Row</th>
                    <th>Rack</th>
                    <th>Plate</th>
                    <th>Place</th>
                    <th>Max Capacity</th>
                </tr>
            </thead>
            <tbody id="entryProductTbody">
                @php
                foreach($binningData as $data) {
                @endphp
                    <tr>
                        <input type="hidden" class="product-id" name="product_id" value="{{$data['product_id']}}">
                        <input type="hidden" class="form-control quantity" name="quantity" value="{{$data['quantity']}}" readonly>
                        <td>
                            <select class="form-control" name="location_id">
                                <option value="">Select</option>
                                @php
                                if(!empty($Location)) {
                                    foreach($Location as $ldata) {
                                        $sel = "";
                                        if(!empty($data['location_id'])) {
                                            if($ldata['location_id'] == $data['location_id']) $sel='selected="selected"';
                                        }
                                @endphp
                                    <option value="{{$ldata['location_id']}}" {{$sel}}>{{$ldata['location_name']}}</option>
                                @php
                                    }
                                }
                                @endphp
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="zone_id" value="{{$data['zone_id']}}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="row_id" value="{{$data['row_id']}}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="rack_id" value="{{$data['rack_id']}}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="plate_id" value="{{$data['plate_id']}}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="place_id" value="{{$data['place_id']}}">
                        </td>
                        <td>
                            <input type="number" name="max_capacity" class="form-control auto-fill-max-capacity" value="">
                        </td>
                    </tr>
                @php
                }
                @endphp
            </tbody>
        </table>
    </div>
</div>
<br>
<p class="text-right">
    <button type="button" class="btn-shadow btn btn-info" id="ConfirmAutoFillBinning"> Save Location </button>
    <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
</p>
{{ Form::close() }}
@php
}
@endphp