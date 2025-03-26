// dataTable
var inventoryList = $('#InventoryList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6 text-right'B>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [
            {
                extend:    'print',
                text:      '<i class="fa fa-print fa-w-20"></i> Print Inventory List',
                className: 'btn-shadow btn btn-datatable'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-excel-o fa-w-20"></i> Export to CSV',
                className: 'btn-shadow btn btn-datatable'
            },
        ],
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "pageLength" : 50,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-inventory-management",
        "type": "POST",
        'data': function(data){
          data.stock_status=$("#stock_status").val();
          data.filter_part_no=$("#filter_part_no").val();
          data.filter_part_name=$("#filter_part_name").val();
          data.filter_units=$("#filter_units").val();
          data.filter_category=$("#filter_category").val();
        },
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: true, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'alternate_part_no', name: 'alternate_part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'unit', name: 'unit', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'stock_alert', name: 'stock_alert', orderable: false, searchable: false},
        {data: 'current_stock', name: 'current_stock', orderable: false, searchable: false},
        {data: 'cost', name: 'cost', orderable: false, searchable: false},
        {data: 'pmrprc', name: 'pmrprc', orderable: false, searchable: false},
        {data: 'transit_quantity', name: 'transit_quantity', orderable: false, searchable: false},
        {data: 'damage_quantity', name: 'damage_quantity', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'location', name: 'location', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#stock_status").on('change',function(){
    inventoryList.draw();
});
$("#filter_part_no").on('keyup input',function(){
    inventoryList.draw();
});
$("#filter_part_name").on('change',function(){
    inventoryList.draw();
});
$("#filter_units").on('keyup input',function(){
    inventoryList.draw();
});
$("#filter_category").on('keyup input',function(){
    inventoryList.draw();
});
function printDiv() 
{

  var divToPrint=document.getElementById('InventoryList');
  console.log(divToPrint.innerHTML);

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}
$('body').on('click',"a.customer_wise",function(){
    var product_id = $(this).data('product_id');
     $.ajax({
        url:base_url+"/inventory-customer-form",
        data:  {product_id:product_id},
        type:'get',
        dataType:"json",
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Price Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                // save_countries_form();
                hideLoader();
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
// Product Quantity updated
$('body').on('click',"a.quantity-on-hand-form-open",function(){
    var product_id = $(this).data('product_id');
    var pmpno = $(this).data('pmpno');
    var part_name = $(this).data('part_name');
     $.ajax({
        url:base_url+"/inventory/quantity-on-hand-form-open",
        data:  {product_id:product_id},
        type:'get',
        dataType:"json",
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Product Quantity");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                $('#view_part_no').val(pmpno);
                $('#view_part_nname').val(part_name);
                quantity_on_hand_form();
                hideLoader();
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
//
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
$('body').on('change', '.place-id', function() {
    _this = $(this);
    var place_id = _this.val();
    var location_id = _this.closest("tr").find('.location-id').val();
    var zone_id = _this.closest("tr").find('.zone-id').val();
    var row_id = _this.closest("tr").find('.row-id').val();
    var rack_id = _this.closest("tr").find('.rack-id').val();
    var plate_id = _this.closest("tr").find('.plate-id').val();
    var product_id = $("#product_id").val();
    console.log("location_id", location_id);
    console.log("zone_id", zone_id);
    console.log("row_id", row_id);
    console.log("rack_id", rack_id);
    console.log("plate_id", plate_id);
    console.log("place_id", place_id);
    $.ajax({
        url:base_url+"/inventory/check-location",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data:{product_id:product_id, location_id:location_id, zone_id:zone_id, row_id:row_id, rack_id:rack_id, plate_id:plate_id, place_id:place_id},
        dataType:'json',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res.status) {
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
});
function quantity_on_hand_form() {
    $("#CommonModal").find("#quantityOnHandForm").validate({
        rules: {
            //current_stock: "required"
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
                    var formData = new FormData($('#quantityOnHandForm')[0]);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url:base_url+"/inventory/save-quantity-on-hand-form-open",  
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
                                $('#quantityOnHandForm')[0].reset();
                                swal({
                                    title: "Success!",
                                    text: res["msg"],
                                    type: "success"
                                }).then(function() {
                                    $('#CommonModal').modal('hide');
                                    inventoryList.draw();
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
        }
    });
}
// Binning location view
$('body').on('click',"a.view-binning-location",function(){
    var product_id = $(this).data('product_id');
     $.ajax({
        url:base_url+"/inventory/view-binning-location",
        data:  {product_id:product_id},
        type:'get',
        dataType:"json",
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Product Location Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                hideLoader();
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
$('body').on('click', '#ConfirmAutoFillBinning', function(e){
    var biningTable= $('table#biningTable tbody tr');
    if(biningTable.length > 0) {
        var form = $('#BinningLocationForm');
        e.preventDefault();
        var last_tr = $('body #entryProductTbody tr').find('input,select');
        var x=0;
        var m=0;
        $(last_tr).each(function(){
            var quantity = $('.quantity').val();
            var max_capacity = $('.auto-fill-max-capacity').val();
            if(parseInt(max_capacity) < parseInt(quantity)) {
                m =1;
            }
            if($(this).val()=="" && !$(this).hasClass('quantity')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Warning!", "Enter data first", "error");
        }else if(m==1) {
            swal("Warning!", "Max capacity can't be less than quantity", "error");
        }else {
            //return false;
            swal({
                title: "Are you sure?",
                text: "You want to save this binning location!",
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
                        url : base_url+"/inventory/product-auto-fill-binning-location", 
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