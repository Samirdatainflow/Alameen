// Table
var CheckIn = $('#CheckInList').DataTable({
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
        "url": base_url+"/list-check-in",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'check_in_id', name: 'check_in_id', orderable: false, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'items', name: 'items', orderable: false, searchable: false},
        {data: 'good_quantity', name: 'good_quantity', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'barcode_status', name: 'barcode_status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<a type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info view-barcode-modal"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-barcode fa-w-20"></i></span>Barcode Scann</a> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New Check In</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="TableExport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function TableExport() {
    window.location.href = base_url+"/check-in-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-check-in",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Check In");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            check_in_form();
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
// Countries save
function check_in_form() {
    $("#CommonModal").find("#CheckInForm").validate({
        rules: {
            order_id: "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('input');
            if(last_tr.length > 0) {
                var x=0;
                $(last_tr).each(function(e){
                    console.log($(this).hasClass());
                    if($(this).val()=="" && !$(this).hasClass('part_name') && !$(this).hasClass('price') && !$(this).hasClass('good-quantity') && !$(this).hasClass('bad-quantity') && !$(this).hasClass('shortage-quantity') && !$(this).hasClass('supplier-id') && !$(this).hasClass('excess-quantity')) {
                        x=1;
                    }
                })
                if(x==1) {
                    swal("Warning!", "Enter data first", "warning");
                }else {
                    var formData = new FormData($('#CheckInForm')[0]);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url:base_url+"/save-check-in",  
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
                                $('#CheckInForm')[0].reset();
                                swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                                    $('#CommonModal').modal('hide');
                                    CheckIn.draw();
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
            }else {
                swal("Warning!", "Load data first", "warning");
            }
        }
    });
}
// 
$('body').on('keyup input', '.good-quantity', function() {
    _this = $(this);
    _this.closest('tr').find('.bad-quantity').val('');
    _this.closest('tr').find('.shortage-quantity').val('');
    _this.css('border-color', '#ced4da');
    var quantity = _this.closest('tr').find('.quantity').val();
    var good_quantity = _this.val();
    if(parseInt(good_quantity) > parseInt(quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }else {
        var c_shortage_quantity = parseInt(quantity) - parseInt(good_quantity);
        if(c_shortage_quantity > 0) {
            _this.closest('tr').find('.shortage-quantity').val(c_shortage_quantity);
        }else {
            _this.closest('tr').find('.shortage-quantity').val('');
        }
    }
    var bad_quantity = _this.closest('tr').find('.bad-quantity').val();
    if(bad_quantity < 1) {
        bad_quantity = 0;
    }
    var shortage_quantity = _this.closest('tr').find('.shortage-quantity').val();
    if(shortage_quantity < 1) {
        shortage_quantity = 0;
    }
    var cal_short_bad_good = parseInt(good_quantity) + (parseInt(bad_quantity) + parseInt(shortage_quantity));
    if(cal_short_bad_good> parseInt(quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }
});
$('body').on('keyup input', '.shortage-quantity', function() {
    _this = $(this);
    _this.css('border-color', '#ced4da');
    var quantity = _this.closest('tr').find('.quantity').val();
    var good_quantity = _this.closest('tr').find('.good-quantity').val();
    var bad_quantity = _this.closest('tr').find('.bad-quantity').val();
    if(bad_quantity < 1) {
        bad_quantity = 0;
    }
    var shortage_quantity = _this.val();
    if(shortage_quantity < 1) {
        shortage_quantity = 0;
    }
    var cal_short_bad = parseInt(bad_quantity) + parseInt(shortage_quantity);
    if(parseInt(shortage_quantity) > parseInt(quantity) || cal_short_bad> parseInt(quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }else {
        var c_good_quantity = parseInt(quantity) - cal_short_bad;
        _this.closest('tr').find('.good-quantity').val(c_good_quantity);
    }
    var excess_quanity = _this.closest('tr').find('.excess-quantity').val();
    if(parseInt(excess_quanity) > 0) {
        _this.val('');
    }
});
$('body').on('keyup input', '.bad-quantity', function() {
    _this = $(this);
    _this.css('border-color', '#ced4da');
    var quantity = _this.closest('tr').find('.quantity').val();
    var good_quantity = _this.closest('tr').find('.good-quantity').val();
    var shortage_quantity = _this.closest('tr').find('.shortage-quantity').val();
    if(shortage_quantity < 1) {
        shortage_quantity = 0;
    }
    var bad_quantity = _this.val();
    if(bad_quantity < 1) {
        bad_quantity = 0;
    }
    var cal_short_bad = parseInt(bad_quantity) + parseInt(shortage_quantity);
    if(parseInt(bad_quantity) > parseInt(quantity) || cal_short_bad > parseInt(quantity)) {
        _this.css('border-color', 'red');
        _this.val('');
    }else {
        var c_bad_quantity = parseInt(quantity) - cal_short_bad;
        _this.closest('tr').find('.good-quantity').val(c_bad_quantity);
    }
});
//
// $('body').on('keyup input', '.shortage-quantity', function() {
//     _this = $(this);
//     _this.css('border-color', '#ced4da');
//     var shortage_quanity = _this.val();
//     var excess_quanity = _this.closest('tr').find('.excess-quantity').val();
//     if(parseInt(excess_quanity) > 0) {
//         _this.val('');
//     }
// });
$('body').on('keyup input', '.excess-quantity', function() {
    _this = $(this);
    _this.css('border-color', '#ced4da');
    var excess_quanity = _this.val();
    var shortage_quanity = _this.closest('tr').find('.shortage-quantity').val();
    console.log("shortage_quanity", shortage_quanity);
    if(parseInt(shortage_quanity) > 0) {
        _this.val('');
    }
});
// 
$('body').on('click', '#get_order_details', function() {
    var order_id = $('#order_id').val();
    if(order_id == "") {
        swal("Sorry!", "Please Select Order ID", "warning");
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/check-in/get-order-details",  
            type: "POST",
            data:  {order_id:order_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                console.log(res);
                if(res["status"]) {
                    hideLoader();
                    $('#entryProductTbody').html('');
                    // var listData = '';
                    // for(i=0; i< res.data.length; i++) {
                    //     listData += '<tr><td>'+(i+1)+'</td><td><input type="hidden" name="product_id[]" value="'+res.data[i].product_id+'"><input type="text" class="form-control" value="'+res.data[i].part_name+'" readonly></td><td><input type="text" class="form-control" value="'+res.data[i].pmpno+'" readonly></td><td><input type="text" class="form-control" value="'+res.data[i].price+'" readonly></td><td><input type="number" class="form-control quantity" name="quantity[]" value="'+res.data[i].quantity+'" readonly></td><td><input type="number" class="form-control good-quantity" name="good_quantity[]"></td><td><input type="number" class="form-control bad-quantity" name="bad_quantity[]"><input type="hidden" class="form-control supplier-id" name="supplier_id[]" value="'+res.data[i].supplier_id+'"></td><td><select class="form-control" name="lot_name[]"><option value="">Select</option>';
                    //     //console.log(res.data[i].WmsLots.length);
                    //     if(res.data[i].WmsLots.length > 0) {
                    //         for(l=0; l<res.data[i].WmsLots; l++) {
                    //             console.log(l);
                    //             listData += '<option value=""></option>';
                    //         }
                    //     }
                    //     listData += '</select></td></tr>';
                    // }
                    $('#entryProductTbody').append(res.data);
                    $('#hidden_warehouse_id').val(res.warehouse_id);
                    $(".modal-dialog").addClass('modal-xl');
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
// Edit Country-->
$('body').on('click',"a.view-check-in",function(){
    // console.log('hi');
    var id = $(this).data('id');
    var check_in_id = $(this).data('check_in_id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-check-in",  
        type: "POST",
        data:  {id:id, check_in_id:check_in_id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Check In Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-xl');
                check_in_form();
            }else {
                hideLoader();
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
});
// view check in details
$('body').on('click',"a.view-check-in-details",function(){
    // console.log('hi');
    var id = $(this).data('id');
    var check_in_id = $(this).data('check_in_id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-check-in-details",  
        type: "POST",
        data:  {id:id, check_in_id:check_in_id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Check In Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-xl');
                check_in_form();
            }else {
                hideLoader();
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
});
// Delete Countries 
$("body").on("click", "a.delete-check-in", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
    var check_in_id = obj.data("check_in_id");
    swal({
        title: "Are you sure?",
        text: "You want to delete it.",
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
                url:base_url+"/delete-check-in",  
                type: "POST",
                data:  {id: id, check_in_id:check_in_id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    // console.log(res);
                    if(res["status"]) {
                        //DataTable4CylinderTypeTable.draw();
                        //$('#pageOverlay').css('display', 'none');
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            CheckIn.draw();
                        });
                    }else {
                        //$('#pageOverlay').css('display', 'none');
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    //$('#pageOverlay').css('display', 'none');
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                    //$('#pageOverlay').css('display', 'none');
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is safe :)", "error")
        }
    })
});

$('body').on('click', 'a.view-barcode-modal', function() {
    
    var check_in_id = $(this).data('check_in_id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/check-in/view-barcode-modal",  
        type: "POST",
        data:  {check_in_id:check_in_id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Scann Barcode");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                console.log("here-11");
                setTimeout(function (){
                    $('#barcode_no').focus();
                }, 1000);
                //$('#CommonModal #barcode_no').focus();
                $(".modal-dialog").removeClass('modal-xl');
            }else {
                hideLoader();
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
});


function submitForm() {
    return false;
}
// function saveBarcodeDetails() {
//     $('#barcode_no').val();
//     console.log('barcode_no', barcode_no);
// }
$("body").on('change', '#barcode_no', function() {
    
    var barcode_no = $(this).val();
    
    if(barcode_no != "") {
        
        $.ajax({
            
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/check-in/save-barcode-details-by-scann",  
            type: "POST",
            data: {barcode_no:barcode_no},
            dataType:"json", 
            
            beforeSend:function(){
                
                showLoader();
            },  
            success:function(res){
                
                hideLoader();
                if(res["status"]) {
                    
                    swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                        $('#CommonModal').modal('hide');
                        CheckIn.draw();
                    });
                    
                }else {
                    
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
    }else {
        swal("Sorry!", "Barcode not found!", "warning");
    }
});
$('body').on('click', 'a.download-barcode-modal', function() {
    
    var obj = $(this);
	var order_id = obj.data("order_id");
	var barcode_number = obj.data("barcode_number");
	var good_quentity = obj.data("good_quentity");
	console.log(good_quentity);
	$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/bining-advice/download-barcode-modal",
        type:'post',
        dataType:'json',
        data: {order_id:order_id, barcode_number:barcode_number, good_quentity:good_quentity},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // var urlCreator = window.URL || window.webkitURL;
            // var imageUrl = urlCreator.createObjectURL(res);
            // var tag = document.createElement('a');
            // tag.href = imageUrl;
            // tag.download = fileName;
            // document.body.appendChild(tag);
            // tag.click();
            // document.body.removeChild(tag);
            if(res['status']) {
                hideLoader();
                $('.modal-title').text('').text("Download Barcode");
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
        complete:function(res){
            hideLoader();
        }
    });
    
});
$('body').on('click', '#download_barcode', function() {
    var download_no = $('#download_no').val();
    var barcode_number = $('#barcode_number').val();
    //window.location.href=base_url+'/bining-advice/download-barcode?download_no='+download_no+'&barcode_number='+barcode_number;
    window.open(base_url+"/bining-advice/download-barcode?download_no="+download_no+"&barcode_number="+barcode_number, '_blank');
});
$('body').on('click', '.btn-cancel', function() {
    $('#CommonModal').modal('hide');
    CheckIn.draw();
    //window.location.reload();
});
