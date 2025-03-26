
// Table
var returnTable = $('#returnTable').DataTable({
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
        "url": base_url+"/list-of-returns",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'return_id', name: 'return_id', orderable: true, searchable: false},
        {data: 'return_date', name: 'return_date', orderable: true, searchable: false},
        {data: 'return_type', name: 'return_type', orderable: false, searchable: false},
        {data: 'delivery_id', name: 'delivery_id', orderable: false, searchable: false},
        {data: 'sale_order_id', name: 'sale_order_id', orderable: false, searchable: false},
        {data: 'user', name: 'user', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_returns" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Returns</button> <button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportReturnsManagementTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportReturnsManagementTable(){
    window.location.href = base_url+"/returns/returns-export-table";
}

function show_form(){
     $.ajax({
        url:base_url+"/returns-form",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Returns");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $('.datetimepicker').datepicker({
                    format : 'dd/mm/yyyy',
                    todayHighlight: true,
                    autoclose: true,
                });
            save_returns_form();
            // }
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
function save_returns_form() {
    $("#CommonModal").find("#returnsForm").validate({
        rules: {
            return_type: "required",
            delivery_id: "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductRow tr').find('input');
            var x=0;
            $(last_tr).each(function(){
                if($(this).val()=="" && !$(this).hasClass('pmrprc') && !$(this).hasClass('good-quantity') && !$(this).hasClass('bad-quantity') && !$(this).hasClass('reason') && !$(this).hasClass('remarks')) {
                    x=1;
                }
            })
            if(x==1) {
                swal("Warning!", "Enter data first", "error");
            }else {
                var formData = new FormData($('#returnsForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/returns/save-returns",  
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
                            $('#returnsForm')[0].reset();
                            swal({title: "success!", text: res["msg"], type: "success"}).then(function() {
                                $('#CommonModal').modal('hide');
                                returnTable.draw();
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
        }
    });
}

$('body').on('change', '#return_type', function() {
    var return_type = $(this).val();
    if(return_type !== "") {
        $('#DeliveryIdSection').css('display', 'block');
    }else {
        $('#DeliveryIdSection').css('display', 'none');
    }
});

$('body').on('change', '#sale_order_id', function() {
    
    $('.returnDetails').remove();
    $('#findDetails').css('display', 'block');
});

$('body').on('change', '#delivery_id', function() {
    
    $('.returnDetails').remove();
    $('#findDetails').css('display', 'block');
    var delivery_id = $(this).val();
    
    if(delivery_id !== "") {
        
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            url:base_url+"/returns/get-sales-order-ids-by-delivery",  
            type: "POST",
            data:  {delivery_id:delivery_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },
            
            success:function(res){
                
                hideLoader();
                
                if(res['status']) {
                    
                    var data = "";
                    for(i=0; i<res.data.length; i++){
                        data += '<option value="'+res.data[i]+'">'+res.data[i]+'</option>';
                    }
                    $("#sale_order_id").html(data);
                    $('#SaleOrderIdSection').css('display', 'block');
                    
                }else {
                    
                    $('#SaleOrderIdSection').css('display', 'none');
                    swal("Sorry!", res['message'], "error");
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
    }else {
        $('#SaleOrderIdSection').css('display', 'none');
    }
});

$('body').on('click', '#find_details', function(){
    
    var return_type = $('#return_type').val();
    var delivery_id = $('#delivery_id').val();
    var sale_order_id = $('#sale_order_id').val();
    
    if(return_type == "") {
        
        swal("Warning!", 'Please select a return type', "error");
    }else if(delivery_id == "") {
        
        swal("Warning!", 'Please enter a delivery id', "error");
    }else if(sale_order_id == "") {
        
        swal("Warning!", 'Please select a sale order id', "error");
    }else {
        
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            url:base_url+"/returns/get-order-details",  
            type: "POST",
            data:  {sale_order_id:sale_order_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },
            
            success:function(res){
                hideLoader();
                if(res['status']) {
                    //$(".first-part").html("");
                    $('#deliverie_details').after(res['message']);
                    $('#findDetails').css('display', 'none');
                }else {
                    swal("Sorry!", res['message'], "error");
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
// View Returns
$('body').on('click', '.view-returns', function(){
    var return_id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/returns/view-returns",  
        type: "POST",
        data:  {return_id:return_id},
        dataType:"html", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("View Returns");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
        },
        error: function(e) {
            hideLoader();
            swal("Opps!", "There is an error", "error");
        },
        complete: function(c) {
            hideLoader();
        }
    });
});
$('body').on('keyup input', '.good-quantity', function() {
    _this = $(this);
    _this.closest('tr').find('.bad-quantity').val('');
    _this.css('border-color', '#ced4da');
    var received_quantity = _this.closest('tr').find('.received-quantity').val();
    var good_quantity = _this.val();
    if(parseInt(good_quantity) > parseInt(received_quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }else {
        var bad_quantity = parseInt(received_quantity) - parseInt(good_quantity);
        _this.closest('tr').find('.bad-quantity').val(bad_quantity);
    }
});

$('body').on('keyup input', '.bad-quantity', function() {
    _this = $(this);
    _this.closest('tr').find('.good-quantity').val('');
    _this.css('border-color', '#ced4da');
    var received_quantity = _this.closest('tr').find('.received-quantity').val();
    var bad_quantity = _this.val();
    if(parseInt(bad_quantity) > parseInt(received_quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }else {
        var good_quantity = parseInt(received_quantity) - parseInt(bad_quantity);
        _this.closest('tr').find('.good-quantity').val(good_quantity);
    }
});