{{ Form::open(array('id'=>'PerformaInvoiceForm')) }}
    <div class="row">
        <div class="col-md-6">
            <div class="position-relative form-group">
                <select class="form-control selectpicker" name="supplier_id" id="supplier_id" data-live-search="true" title="Select Supplier*">
                   @php
                      if(!empty($supplier_data)) {
                        foreach($supplier_data as $data) {
                    @endphp
                    <option value="{{$data['supplier_id']}}">{{$data['full_name']}}</option>
                    @php
                        }
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <input type="number" class="form-control" name="order_request_id" placeholder="Enter Order Request ID">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <input type="file" id="invoice_file" name="invoice_file" class="file-upload-default" accept=".jpg, .png, .pdf" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="invoice_file" name="invoice_file" disabled placeholder="Upload Invoice File" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}