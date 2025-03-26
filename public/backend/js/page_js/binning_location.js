// Table
var BinningLocation = $('#BinningLocationList').DataTable({
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
        "url": base_url+"/list-binning-location",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'binning_location_id', name: 'binning_location_id', orderable: false, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'quantity', name: 'quantity', orderable: false, searchable: false},
        {data: 'details', name: 'details', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Bining Location</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="TableExport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function TableExport() {
    window.location.href = base_url+"/binning-location-export";
}
function show_form(){
    $.ajax({
        url:base_url+"/add-binning-location",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Bining Location");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            bining_location_form();
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
function bining_location_form() {
    $("#CommonModal").find("#BinningLocationForm").validate({
        rules: {
            order_id: "required",
        },
        submitHandler: function() {
            var last_tr = $('body #entryProductTbody tr').find('select');
            if(last_tr.length > 0) {
                var x=0;
                $(last_tr).each(function(){
                    if($(this).val()=="") {
                        x=1;
                    }
                })
                if(x==1) {
                    swal("Warning!", "Enter data first", "warning");
                }else {
                    var formData = new FormData($('#BinningLocationForm')[0]);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url:base_url+"/save-binning-location",  
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
                                $('#BinningLocationForm')[0].reset();
                                swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                                    $('#CommonModal').modal('hide');
                                    BinningLocation.draw();
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
$('body').on('click', '#get_order_details', function() {
    var order_id = $('#order_id').val();
    if(order_id == "") {
        swal("Sorry!", "Please Select Order ID", "warning");
    }else {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/binning-location/get-order-details",  
            type: "POST",
            data:  {order_id:order_id},
            dataType:"json", 
            beforeSend:function(){  
                showLoader();
            },  
            success:function(res){
                if(res["status"]) {
                    hideLoader();
                    $('#entryProductTbody').html('');
                    $('#entryProductTbody').append(res.message);
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
$('body').on('click',"a.view-binning-location",function(){
    // console.log('hi');
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-binning-location",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Binning Location Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                $(".modal-dialog").addClass('modal-xl');
                bining_location_form();
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
$("body").on("click", "a.delete-binning-location", function(e) {                   
    var obj = $(this);
    var id = obj.data("id");
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
                url:base_url+"/delete-binning-location",  
                type: "POST",
                data:  {id: id},
                beforeSend:function(){  
                    //$('#pageOverlay').css('display', 'block');
                },  
                success:function(res){
                    // console.log(res);
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            BinningLocation.draw();
                        });
                    }else {
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
// Get Zone By Location
$('body').on('change', '.location-id', function() {
    _this = $(this);
    var id = _this.val();
    $.ajax({
        url:base_url+"/get-zone-by-location",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res.status) {
                hideLoader();
                _this.closest("tr").find('.zone-id option:not(:first)').remove();
                $(res.data).each(function(i){
                    _this.closest("tr").find('.zone-id').append("<option value='"+res.data[i]['zone_id']+"'>"+res.data[i]['zone_name']+"</option>");
                })
            }else{
                hideLoader();
                _this.closest("tr").find('.zone-id option:not(:first)').remove();
                swal({title: "Sorry!",text: res.msg, type: "warning"});
            }
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
});
// Get Row By Zone
$('body').on('change', '.zone-id', function() {
    _this = $(this);
    var id = _this.val();
    $.ajax({
        url:base_url+"/get-row-by-zone",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res.status) {
                hideLoader();
                _this.closest("tr").find('.row-id option:not(:first)').remove();
                $(res.data).each(function(i){
                    _this.closest("tr").find('.row-id').append("<option value='"+res.data[i]['row_id']+"'>"+res.data[i]['row_name']+"</option>");
                })
            }else{
                hideLoader();
                _this.closest("tr").find('.row-id option:not(:first)').remove();
                swal({title: "Sorry!",text: res.msg, type: "warning"});
            }
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
});
// Get Rack By Row
$('body').on('change', '.row-id', function() {
    _this = $(this);
    var id = _this.val();
    $.ajax({
        url:base_url+"/get-rack-by-row",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res.status) {
                hideLoader();
                _this.closest("tr").find('.rack-id option:not(:first)').remove();
                $(res.data).each(function(i){
                    _this.closest("tr").find('.rack-id').append("<option value='"+res.data[i]['rack_id']+"'>"+res.data[i]['rack_name']+"</option>");
                })
            }else{
                hideLoader();
                _this.closest("tr").find('.rack-id option:not(:first)').remove();
                swal({title: "Sorry!",text: res.msg, type: "warning"});
            }
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
});
// Get Plate By Rack
$('body').on('change', '.rack-id', function() {
    _this = $(this);
    var id = _this.val();
    $.ajax({
        url:base_url+"/get-plate-by-rack",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res.status) {
                hideLoader();
                _this.closest("tr").find('.plate-id option:not(:first)').remove();
                $(res.data).each(function(i){
                    _this.closest("tr").find('.plate-id').append("<option value='"+res.data[i]['plate_id']+"'>"+res.data[i]['plate_name']+"</option>");
                })
            }else{
                hideLoader();
                _this.closest("tr").find('.plate-id option:not(:first)').remove();
                swal({title: "Sorry!",text: res.msg, type: "warning"});
            }
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
});
// Get Place By Plate
$('body').on('change', '.plate-id', function() {
    _this = $(this);
    var id = _this.val();
    $.ajax({
        url:base_url+"/get-place-by-plate",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            if(res.status) {
                hideLoader();
                _this.closest("tr").find('.place-id option:not(:first)').remove();
                $(res.data).each(function(i){
                    _this.closest("tr").find('.place-id').append("<option value='"+res.data[i]['place_id']+"'>"+res.data[i]['place_name']+"</option>");
                })
            }else{
                hideLoader();
                _this.closest("tr").find('.place-id option:not(:first)').remove();
                swal({title: "Sorry!",text: res.msg, type: "warning"});
            }
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
});
// Gem capacity By Place
$('body').on('change', '.place-id', function() {
    _this = $(this);
    var id = _this.val();
    var product_id = _this.closest("tr").find('.product-id').val();
    var part_name = _this.closest("tr").find('.part-name').val();
    var pmpno = _this.closest("tr").find('.pmpno').val();
    var quantity = _this.closest("tr").find('.quantity').val();
    var location_id = _this.closest("tr").find('.location-id').val();
    var zone_id = _this.closest("tr").find('.zone-id').val();
    var row_id = _this.closest("tr").find('.row-id').val();
    var rack_id = _this.closest("tr").find('.rack-id').val();
    var plate_id = _this.closest("tr").find('.plate-id').val();
    var place_id = _this.closest("tr").find('.place-id').val();
    var hidden_position = _this.closest("tr").find('.hidden-position').val();
    if(hidden_position !="" && hidden_position == place_id) {
        return true;
    }else {
        if(!checkAlreadyExistsLocation(_this, place_id)) {
            $.ajax({
                url:base_url+"/get-capacity-by-place",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                data:{id:id, product_id:product_id, part_name:part_name, pmpno:pmpno, quantity:quantity, location_id:location_id, zone_id:zone_id, row_id:row_id, rack_id:rack_id, plate_id:plate_id, place_id:place_id, part_name:part_name},
                dataType:'json',
                beforeSend:function(){
                    showLoader();
                },
                success:function(res){
                    hideLoader();
                    if(res.status) {
                        _this.closest("tr").find('.max-capacity').html('').html(res.max_capacity);
                        _this.closest("tr").find('.hidden-max-capacity').val(res.max_capacity);
                        _this.closest("tr").find('.quantity').val(res.accep_quantity);
                        _this.closest("tr").after(res.newLine);
                    }else{
                       _this.val('');
                        swal({title: "Sorry!",text: res.msg, type: "warning"});
                    }
                },
                error:function(){
                    hideLoader();
                },
                complete:function(){
                    hideLoader();
                }
            })
        }else {
            _this.val("");
            swal("Warning!", "Sorry! You have already added this location", "error");
        }
    }
});
function checkAlreadyExistsLocation(_this,place_id)
{
  var last_tr=$('table#biningTable tr').find('.place-id');
  var r=0;
  console.log("Here", last_tr);
  $(last_tr).not(_this).each(function(){
    console.log($(this).val()+" - place_id: "+place_id);
    if($(this).val()==place_id)
    {
      r=1;
    }
  });
  return r;
}
// Save Binning
$(document).on('click', '#SaveBinning', function(e){
    var biningTable= $('table#biningTable tbody tr');
    if(biningTable.length > 0) {
        var form = $('#BinningLocationForm');
        e.preventDefault();
        var formData = form.serializeArray();
        formData.push({ name: "bining_status", value: "SaveBinning" });
        $.ajax({
            url : base_url+"/save-binning-location", 
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            dataType:'json',
            beforeSend:function(){  
                $("#loader").css("display","block");
            },
            success: function(res){
                if(res['status'])
                {
                    swal({
                        title: 'Success',
                        text: res['msg'],
                        icon: 'success',
                        type:'success',
                    }).then(function() {
                        $('#CommonModal').modal('hide');
                        BinningLocation.draw();
                    });
                }
                else
                {
                    swal("Sorry!", res['msg'], "warning");
                }
            },
            error:function(error){
                swal("Warning!", "Sorry! There is an error", "error");
            },
            complete:function(){
                $("#loader").css("display","none");
            }
        });
    }else {
        swal("Sorry!", "Please load a order ID!", "warning");
    }
});
// Confirm Binning
$(document).on('click', '#ConfirmBinning', function(e){
    var biningTable= $('table#biningTable tbody tr');
    if(biningTable.length > 0) {
        var form = $('#BinningLocationForm');
        e.preventDefault();
        var last_tr = $('body #entryProductTbody tr').find('select');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="") {
                x=1;
            }
        })
        if(x==1) {
            swal("Warning!", "Enter data first", "error");
        }else {
            swal({
                title: "Are you sure?",
                text: "You want to confim this binning location!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes',
                cancelButtonText: "No",
            }).then(function(isConfirm) {
                if (isConfirm && isConfirm.value) {
                    var formData = form.serializeArray();
                    formData.push({ name: "order_status", value: "CreateOrder" });
                    $.ajax({
                        url : base_url+"/confirm-binning-location", 
                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        dataType:'json',
                        beforeSend:function(){  
                            $("#loader").css("display","block");
                        },
                        success: function(res){
                            if(res['status']) {
                                swal({
                                    title: 'Success',
                                    text: res['msg'],
                                    icon: 'success',
                                    type:'success',
                                }).then(function() {
                                    $('#CommonModal').modal('hide');
                                    BinningLocation.draw();
                                });
                            }else {
                                swal("Sorry!", res['msg'], "warning");
                            }
                        },
                        error:function(error){
                            swal("Warning!", "Sorry! There is an error", "error");
                        },
                        complete:function(){
                            $("#loader").css("display","none");
                        }
                    });
                }
            });
        }
    }else {
        swal("Sorry!", "Please load a order ID!", "warning");
    }
});