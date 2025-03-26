var OutstandingTable = $('#OutstandingTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-sale-order-outstanding",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#outstanding_filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'client_name', name: 'client_name', orderable: false, searchable: false},
        {data: 'date_of_invoice', name: 'date_of_invoice', orderable: false, searchable: false},
        {data: 'invoice_amount', name: 'invoice_amount', orderable: false, searchable: false},
        {data: 'invoice_no', name: 'invoice_no', orderable: false, searchable: false},
        {data: 'due_amount', name: 'due_amount', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$('#OutstandingTable_wrapper .toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="AddPayment()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add payment </button> <button type="button" style="margin: 2px;" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportOutstandingTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

$("#outstanding_filter_customer").on('change',function(){
    OutstandingTable.draw();
});

$('body').on('click', 'a.rest-outstanding', function() {
    $('#outstanding_filter_customer').val('');
    OutstandingTable.draw();
});

function ExportOutstandingTable() {
    
    var filter_customer = $('#outstanding_filter_customer').val();
    window.location.href = base_url+"/sale-order-outstanding-export?filter_customer="+filter_customer;
}

function AddPayment(){
    $.ajax({
        url:base_url+"/add-outstanding-payment",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Payment");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $('.selectpicker').selectpicker().change(function(){
                $(this).valid()
            });
            add_payment_form();
        },
        error:function(){
            swal({
              title: "Sorry!",
              text: "There is an error",
              type: "error" // type can be error/warning/success
            });
        },
        complete:function(){
            hideLoader();
        }
    })
}

function add_payment_form() {
    
    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        endDate: "today"
    }).change(function(){
        $(this).valid()
    });
    
    $("#CommonModal").find("#CreateNewPayment").validate({
        
        rules: {
            client_id: "required",
            payment_mode: "required",
            reference_number: "required",
            payment_date: "required",
        },
        
        errorPlacement: function(error, element) {
            
            if (element.attr("name") == "client_id") {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            
            var formData = new FormData($('#CreateNewPayment')[0]);
            
            $.ajax({
                
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-outstanding-payment",  
                type: "POST",
                data:  formData,
                contentType: false,
                cache: false,
                processData:false, 
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    if(res["status"]) {
                        hideLoader();
                        $('#CreateNewPayment')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            saleOrderTable.draw();
                        });
                    }else {
                        hideLoader();
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    hideLoader();
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    hideLoader();
                }
            });  
        }
    });
}

$('body').on('click', '#get_customer_invoice_details', function() {
    
    var client_id = $('#client_id').val();
    console.log("client_id", client_id);
    if(client_id === "" || client_id === null) {
        
        swal("Sorry!", "Please Select A Customer!", "warning");
    }else {
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-customer-invoice-details",  
            type: "POST",
            data:  {client_id:client_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                
                if(res["status"]) {
                    
                    hideLoader();
                    $('#entryProductTbody').html('');
                    var listData = '';
                    for(i=0; i< res.data.length; i++) {
                        listData += '<tr><td><input type="hidden" name="sale_order_id[]" value="'+res.data[i].sale_order_id+'"><input type="text" class="form-control" name="invoice_date[]" value="'+res.data[i].invoice_date+'" readonly></td><td><input type="text" name="invoice_no[]" class="form-control" value="'+res.data[i].invoice_no+'" readonly></td><td><input type="number" class="form-control" name="invoice_amount[]" value="'+res.data[i].grand_total+'" readonly></td><td><input type="number" class="form-control due-amount max-amount'+i+'" name="due_amount[]" value="'+res.data[i].due_amount+'" readonly></td><td><input type="number" class="form-control pay-amount enter-amount'+i+'" name="pay[]"></td></tr>';
                    }
                    $('#entryProductTbody').append(listData);
                    $('.hide-amount-field').css('display', 'block');
                    $('#total_invoice_amount').val(res["totalDueAmunt"]);
                    $('.hide-section').css('display', 'flex');
                }else {
                    hideLoader();
                    swal("Sorry!", res.msg, "warning");
                }
            },
            error: function(e) {
                hideLoader();
                swal("Opps!", "There is an error", "error");
            },
            complete: function(c) {
                hideLoader();
            }
        });
    }
});


$('body').on('keyup input', '#balance_amount', function()
{
    var total_invoice_amount = $('#total_invoice_amount').val();
    total_invoice_amount = parseFloat(total_invoice_amount);
    var balance_amount = $(this).val();
    var currentBalance = parseFloat(balance_amount);
    
    console.log("balance_amount", balance_amount);
    if(currentBalance > total_invoice_amount)
    {
        $(this).val(total_invoice_amount)
    }
    var lineItem = $('#entryProductTbody .due-amount').length;
    
    if(lineItem > 0)
    {
        for(var i=0; i<lineItem; i++)
        {
            var lineDue = $('#entryProductTbody .max-amount'+i).val();
            lineDue = parseFloat(lineDue);
            
            if(currentBalance > 0)
            {
                if(currentBalance > lineDue)
                {
                    lineDue = lineDue.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(lineDue);
                    currentBalance = currentBalance - lineDue;
                }
                else
                {
                    currentBalance = currentBalance.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(currentBalance);
                    currentBalance = currentBalance - lineDue;
                }
            }
            else
            {
                $('#entryProductTbody .enter-amount'+i).val('');
            }
            
        }
    }
    
});

$('body').on('keyup input', '#partial_balance_amount', function()
{
    var total_invoice_amount = $('#partial_total_invoice_amount').val();
    total_invoice_amount = parseFloat(total_invoice_amount);
    var balance_amount = $(this).val();
    var currentBalance = parseFloat(balance_amount);
    
    console.log("balance_amount", balance_amount);
    if(currentBalance > total_invoice_amount)
    {
        $(this).val(total_invoice_amount)
    }
    var lineItem = $('#entryProductTbody .due-amount').length;
    console.log("lineItem", lineItem);
    if(lineItem > 0)
    {
        for(var i=0; i<lineItem; i++)
        {
            var lineDue = $('#entryProductTbody .max-amount'+i).val();
            lineDue = parseFloat(lineDue);
            
            if(currentBalance > 0)
            {
                if(currentBalance > lineDue)
                {
                    lineDue = lineDue.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(lineDue);
                    currentBalance = currentBalance - lineDue;
                }
                else
                {
                    currentBalance = currentBalance.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(currentBalance);
                    currentBalance = currentBalance - lineDue;
                }
            }
            else
            {
                $('#entryProductTbody .enter-amount'+i).val('');
            }
            
        }
    }
    
});

$('body').on('keyup input', '#receipt_balance_amount', function()
{
    var total_invoice_amount = $('#receipt_total_invoice_amount').val();
    total_invoice_amount = parseFloat(total_invoice_amount);
    var balance_amount = $(this).val();
    var currentBalance = parseFloat(balance_amount);
    
    console.log("balance_amount", balance_amount);
    if(currentBalance > total_invoice_amount)
    {
        $(this).val(total_invoice_amount)
    }
    var lineItem = $('#entryProductTbody .due-amount').length;
    console.log("lineItem", lineItem);
    if(lineItem > 0)
    {
        for(var i=0; i<lineItem; i++)
        {
            var lineDue = $('#entryProductTbody .max-amount'+i).val();
            lineDue = parseFloat(lineDue);
            
            if(currentBalance > 0)
            {
                if(currentBalance > lineDue)
                {
                    lineDue = lineDue.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(lineDue);
                    currentBalance = currentBalance - lineDue;
                }
                else
                {
                    currentBalance = currentBalance.toFixed(3);
                    $('#entryProductTbody .enter-amount'+i).val(currentBalance);
                    currentBalance = currentBalance - lineDue;
                }
            }
            else
            {
                $('#entryProductTbody .enter-amount'+i).val('');
            }
            
        }
    }
    
});

$(document).on('keyup paste','.pay-amount',function() {
    
    _this = $(this);
    var pay_amount = $(this).parents('tr').find('.pay-amount').val();
    var due_amount = $(this).parents('tr').find('.due-amount').val();
    var invoice_amount = $(this).parents('tr').find('.invoice-amount').val();
    pay_amount = parseFloat(pay_amount);
    due_amount = parseFloat(due_amount);
    
    
    if(pay_amount !== "" && pay_amount > 0 ) {
        
        if(due_amount !== "" && due_amount > 0) {
            
            if(pay_amount > due_amount) {
                _this.val('');
            }
        }else {
            
            if(pay_amount > invoice_amount) {
                _this.val('');
            }
        }
    }

});


var PartialOutstandingTable = $('#PartialOutstandingTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-sale-order-partial-outstanding",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#partial_outstanding_filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'client_name', name: 'client_name', orderable: false, searchable: false},
        {data: 'date_of_invoice', name: 'date_of_invoice', orderable: false, searchable: false},
        {data: 'invoice_amount', name: 'invoice_amount', orderable: false, searchable: false},
        {data: 'invoice_number', name: 'invoice_number', orderable: false, searchable: false},
        {data: 'due_amount', name: 'due_amount', orderable: false, searchable: false},
        {data: 'payment_date', name: 'payment_date', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$('#PartialOutstandingTable_wrapper .toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="AddPartialPayment()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add payment </button> <button type="button" style="margin: 2px;" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportPartialOutstandingTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

$("#partial_outstanding_filter_customer").on('change',function(){
    PartialOutstandingTable.draw();
});

$('body').on('click', 'a.rest-partial-outstanding', function() {
    $('#partial_outstanding_filter_customer').val('');
    PartialOutstandingTable.draw();
});

function ExportPartialOutstandingTable() {
    
    var filter_customer = $('#partial_outstanding_filter_customer').val();
    window.location.href = base_url+"/sale-order-partial-outstanding-export?filter_customer="+filter_customer;
}

function AddPartialPayment(){
    $.ajax({
        url:base_url+"/add-partial-outstanding-payment",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Payment");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $('.selectpicker').selectpicker().change(function(){
                $(this).valid()
            });
            add_partial_payment_form();
        },
        error:function(){
            swal({
              title: "Sorry!",
              text: "There is an error",
              type: "error" // type can be error/warning/success
            });
        },
        complete:function(){
            hideLoader();
        }
    })
}

$('body').on('click', '#get_customer_partial_details', function() {
    
    var client_id = $('#client_id').val();
    console.log("client_id", client_id);
    if(client_id === "" || client_id === null) {
        
        swal("Sorry!", "Please Select A Customer!", "warning");
    }else {
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-customer-partial-details",  
            type: "POST",
            data:  {client_id:client_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                
                if(res["status"]) {
                    
                    hideLoader();
                    $('#entryProductTbody').html('');
                    var listData = '';
                    for(i=0; i< res.data.length; i++) {
                        listData += '<tr><td><input type="hidden" name="sale_order_id[]" value="'+res.data[i].sale_order_id+'"><input type="text" class="form-control" name="invoice_date[]" value="'+res.data[i].invoice_date+'" readonly></td><td><input type="text" name="invoice_no[]" class="form-control" value="'+res.data[i].invoice_no+'" readonly></td><td><input type="number" class="form-control" name="invoice_amount[]" value="'+res.data[i].grand_total+'" readonly></td><td><input type="number" class="form-control due-amount max-amount'+i+'" name="due_amount[]" value="'+res.data[i].due_amount+'" readonly></td><td><input type="number" class="form-control pay-amount enter-amount'+i+'" name="pay[]"></td></tr>';
                    }
                    $('#entryProductTbody').append(listData);
                    $('.hide-amount-field').css('display', 'block');
                    $('#partial_total_invoice_amount').val(res["totalDueAmunt"]);
                    $('.hide-section').css('display', 'flex');
                }else {
                    hideLoader();
                    swal("Sorry!", res.msg, "warning");
                }
            },
            error: function(e) {
                hideLoader();
                swal("Opps!", "There is an error", "error");
            },
            complete: function(c) {
                hideLoader();
            }
        });
    }
});

function add_partial_payment_form() {
    
    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        endDate: "today"
    }).change(function(){
        $(this).valid()
    });
    
    $("#CommonModal").find("#CreatePartialOutstanding").validate({
        
        rules: {
            client_id: "required",
            payment_mode: "required",
            reference_number: "required",
            payment_date: "required",
        },
        
        errorPlacement: function(error, element) {
            
            if (element.attr("name") == "client_id") {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            
            var formData = new FormData($('#CreatePartialOutstanding')[0]);
            
            $.ajax({
                
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-partial-outstanding-payment",  
                type: "POST",
                data:  formData,
                contentType: false,
                cache: false,
                processData:false, 
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    if(res["status"]) {
                        hideLoader();
                        $('#CreatePartialOutstanding')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            PartialOutstandingTable.draw();
                        });
                    }else {
                        hideLoader();
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    hideLoader();
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    hideLoader();
                }
            });  
        }
    });
}

var ReceiptTable = $('#ReceiptTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-sale-order-receipt",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#receipt_filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'client_name', name: 'client_name', orderable: false, searchable: false},
        {data: 'date_of_invoice', name: 'date_of_invoice', orderable: false, searchable: false},
        {data: 'invoice_number', name: 'invoice_number', orderable: false, searchable: false},
        {data: 'invoice_amount', name: 'invoice_amount', orderable: false, searchable: false},
        {data: 'pay_amount', name: 'pay_amount', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false}
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$("#receipt_filter_customer").on('change',function(){
    ReceiptTable.draw();
});

$('body').on('click', 'a.rest-receipt', function() {
    $('#receipt_filter_customer').val('');
    ReceiptTable.draw();
});

$('#ReceiptTable_wrapper .toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="AddReceiptPayment()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add payment </button> <button type="button" style="margin: 2px;" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportReceiptTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportReceiptTable() {
    
    var filter_customer = $('#receipt_filter_customer').val();
    window.location.href = base_url+"/sale-order-receipt-export?filter_customer="+filter_customer;
}

function AddReceiptPayment(){
    $.ajax({
        url:base_url+"/add-sale-order-receipt",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Payment");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $('.selectpicker').selectpicker().change(function(){
                $(this).valid()
            });
            add_receipt_payment_form();
        },
        error:function(){
            swal({
              title: "Sorry!",
              text: "There is an error",
              type: "error" // type can be error/warning/success
            });
        },
        complete:function(){
            hideLoader();
        }
    })
}

$('body').on('click', '#get_customer_receipt_details', function() {
    
    var client_id = $('#client_id').val();
    console.log("client_id", client_id);
    if(client_id === "" || client_id === null) {
        
        swal("Sorry!", "Please Select A Customer!", "warning");
    }else {
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-customer-receipt-details",  
            type: "POST",
            data:  {client_id:client_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                
                if(res["status"]) {
                    
                    hideLoader();
                    $('#entryProductTbody').html('');
                    var listData = '';
                    for(i=0; i< res.data.length; i++) {
                        listData += '<tr><td><input type="hidden" name="sale_order_id[]" value="'+res.data[i].sale_order_id+'"><input type="text" class="form-control" name="invoice_date[]" value="'+res.data[i].invoice_date+'" readonly></td><td><input type="text" name="invoice_no[]" class="form-control" value="'+res.data[i].invoice_no+'" readonly></td><td><input type="number" class="form-control invoice-amount" name="invoice_amount[]" value="'+res.data[i].grand_total+'" readonly></td><td><input type="number" class="form-control due-amount max-amount'+i+'" name="due_amount[]" value="'+res.data[i].due_amount+'" readonly></td><td><input type="number" class="form-control pay-amount enter-amount'+i+'" name="pay[]"></td></tr>';
                    }
                    $('#entryProductTbody').append(listData);
                    $('.hide-amount-field').css('display', 'block');
                    $('#receipt_total_invoice_amount').val(res["totalDueAmunt"]);
                    $('.hide-section').css('display', 'flex');
                }else {
                    hideLoader();
                    swal("Sorry!", res.msg, "warning");
                }
            },
            error: function(e) {
                hideLoader();
                swal("Opps!", "There is an error", "error");
            },
            complete: function(c) {
                hideLoader();
            }
        });
    }
});

function add_receipt_payment_form() {
    
    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        endDate: "today"
    }).change(function(){
        $(this).valid()
    });
    
    $("#CommonModal").find("#CreateReceipt").validate({
        
        rules: {
            client_id: "required",
            payment_mode: "required",
            reference_number: "required",
            payment_date: "required",
        },
        
        errorPlacement: function(error, element) {
            
            if (element.attr("name") == "client_id") {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            
            var formData = new FormData($('#CreateReceipt')[0]);
            
            $.ajax({
                
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-sale-order-receipt-payment",  
                type: "POST",
                data:  formData,
                contentType: false,
                cache: false,
                processData:false, 
                dataType:"json", 
                beforeSend:function(){  
                    showLoader();
                },  
                success:function(res){
                    if(res["status"]) {
                        hideLoader();
                        $('#CreateReceipt')[0].reset();
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#CommonModal').modal('hide');
                            ReceiptTable.draw();
                        });
                    }else {
                        hideLoader();
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    hideLoader();
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    hideLoader();
                }
            });  
        }
    });
}

$('body').on('click', 'a.print-receipt-slip', function() {
    var obj = $(this);
    var sales_receipt_id = obj.data("sales_receipt_id");
    window.location.href = base_url+"/print-sale-order-receipt-slip?id="+sales_receipt_id;
});









