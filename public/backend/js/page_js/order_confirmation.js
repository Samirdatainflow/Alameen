// List
var OrderConfirmation = $('#OrderConfirmationList').DataTable({
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
        "url": base_url+"/order-confirmation/list-order-confirmation",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'order_quotation_id', name: 'order_quotation_id', orderable: true, searchable: false},
        {data: 'order_request_id', name: 'order_request_id', orderable: false, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'confirm_details', name: 'confirm_details', orderable: false, searchable: false}
    ],
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$("#filter_supplier").on('keyup input',function(){
    OrderConfirmation.draw();
});
$('#ResetFilter').on('click', function(){
    $('#filter_supplier').val('');
    OrderConfirmation.draw();
})
//$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="addNew()" aria-expanded="false" class="btn-shadow btn btn-info" title="Add new"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Upload Quotation</button>');
// Add
function addNew() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/quotation-order/add-quotation",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Add Quotation");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $('.datetimepicker').datepicker({
                    format : 'dd/mm/yyyy',
                    todayHighlight: true,
                    autoclose: true,
                });
                $(".modal-dialog").addClass('modal-xl');
                //$("#order_date").attr('readonly', true);
                quotation_form();
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
}
function quotation_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#OrderRequestForm").validate({
        rules: {
            'supplier[]': "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('input');
            var x=0;
            $(last_tr).each(function(){
                if($(this).val()=="") {
                    x=1;
                }
            })
            if(x==1) {
                swal("Warning!", "Enter data first", "error");
            }else {
                var formData = new FormData($('#OrderRequestForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/quotation-order/save-order-request",  
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
                            $('#OrderRequestForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#CommonModal').modal('hide');
                                OrderRequest.draw();
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
// Order Request ID valid check 
$('body').on('change', '#order_request_id', function() {
    //
});
// View Order Details
$('body').on('click', 'a.view-quotation-order-details', function() {
	var obj = $(this);
	var id = obj.data("id");
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/order-confirmation/view-quotation-order-details",
        type:'post',
        dataType:'JSON',
        data: {id:id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("View Order Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                $(".modal-dialog").addClass('modal-lg');
            }else {
                hideLoader();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    });
});
// Delete Order
$("body").on("click", "a.confirm-quotation-order", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
    var status = obj.data("status");
    swal({
        title: "Are you sure?",
        text: "You want to chnage it!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes.",
        cancelButtonText: "No!",
        confirmButtonClass: "btn btn-success mr-5",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: !1
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/order-confirmation/chnage-confirmation",  
                type: "POST",
                data:  {id: id, status:status},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            OrderConfirmation.draw();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    //
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});