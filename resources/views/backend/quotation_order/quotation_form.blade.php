{{ Form::open(array('id'=>'OrderQuotationForm')) }}
    <div class="row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <input type="number" class="form-control" name="order_request_id" id="order_request_id" placeholder="Enter Order Request ID">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <input type="file" id="quotation_file" name="quotation_file" class="file-upload-default" accept=".jpg, .png, .pdf" />
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" id="quotation_file" name="quotation_file" disabled placeholder="Upload Quotation File" />
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-primary bg-blue text-white" type="button"> File </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%;" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Part Brand</th>
                        <th>Part No</th>
                        <th>Part Name</th>
                        <th>Unit</th>
                        <th>Manufacturer No</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="ProductTbody">
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}