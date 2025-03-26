{{ Form::open(array('id'=>'OrderReturnForm')) }}
    <div class="row" id="OrderRequestSection">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <select class="form-control" id="supplier_id" name="supplier_id">
                    <option value="">Select Supplier</option>
                    @php
                    if(!empty($supplierData)) {
                    foreach($supplierData as $sup) {
                    @endphp
                    <option value="{{$sup['supplier_id']}}">{{$sup['full_name']}}</option>
                    @php
                    }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" id="get_order_details" class="btn-shadow btn btn-info" value="Submit"> Load </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5 id="purchasedDate"></h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Check Box</th>
                        <th>Category</th>
                        <th>Purchase Order ID</th>
                        <th>Date of Purchase Invoice</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="entryProductTbody"></tbody>
            </table>
        </div>
    </div>
    <!--<div class="row">-->
    <!--    <div class="col-md-12">-->
    <!--        <div class="position-relative form-group">-->
    <!--            <label>Note*</label>-->
    <!--            <textarea class="form-control" name="note"></textarea>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <br>
    <!--<div class="row">-->
    <!--    <div class="col-md-6 mb-3">-->
    <!--        <div class="form-group">-->
    <!--            <input type="file" name="return_files[]" class="file-upload-default" accept=".jpg, .png, .pdf" multiple="" />-->
    <!--            <div class="input-group col-xs-12">-->
    <!--                <input type="text" class="form-control file-upload-info" id="return_files" disabled placeholder="Upload Files" />-->
    <!--                <span class="input-group-append">-->
    <!--                    <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>-->
    <!--                </span>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <br>
    <p class="text-right">
        <button type="submit" class="btn-shadow btn btn-info"> Save </button>
        <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}