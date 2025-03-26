{{ Form::open(array('id'=>'configLotsForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Lot Name *</label>
                @php
                $lot_name = "";
                if(!empty($lots_data[0]['lot_name']))  {
                    $lot_name = $lots_data[0]['lot_name'];
                }
                $hidden_id = "";
                if(!empty($lots_data[0]['lot_id']))  {
                    $hidden_id = $lots_data[0]['lot_id'];
                }
                @endphp
                <input name="lot_name" id="lot_name" placeholder="" type="text" class="form-control" value="{{$lot_name}}">
                <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group product-search">
                <label>Product *</label>
                <select class="form-control selectpicker" name="product_id" id="product_id" data-live-search="true" title="Search ">
                    @php
                    if(!empty($Products)) {
                        foreach($Products as $data){
                    @endphp
                    <option value="{{$data->product_id}}" selected="selected">{{$data->part_name}} ({{$data->pmpno}})</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>Quantity *</label>
                @php
                $qty = "";
                if(!empty($lots_data[0]['qty']))  {
                    $qty = $lots_data[0]['qty'];
                }
                @endphp 
                <input name="qty" id="qty" placeholder="Enter Number Only" type="number" class="form-control" value="{{$qty}}">
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
                <label>Bulk Lots Create</label>
                <input type="file" id="lots_csv" name="lots_csv" class="file-upload-default" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="lots_csv" name="lots_csv" disabled placeholder="Upload CSV" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn-shadow btn btn-info preview-multiple-lots" type="button"> Preview </button>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}