// dataTable
var stockTable = $('#StockTable').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    'orderable': true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-stock",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: true, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'product_name', name: 'product_name', orderable: false, searchable: false},
        {data: 'warehouse_id', name: 'warehouse_id', orderable: false, searchable: false},
        {data: 'lot_name', name: 'lot_name', orderable: false, searchable: false},
        {data: 'total_qty', name: 'total_qty', orderable: false, searchable: false},
        //{data: 'created_date', name: 'created_date', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_stock" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Stock</button> <button type="button" aria-haspopup="true" id="add_stock" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/stock-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/add-stock",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Stock");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
            stock_form();
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
$("body").on('change', '#pmpno', function(){
        var pmpno = $('#pmpno').val();
        var location = $('#location_id').val();
        var warehouse = $('#warehouse_id').val();
        if(location == "")
        {
            swal("Opps!", "Please select location", "error");
        }
        else if(warehouse == "")
        {
            swal("Opps!", "Please select warehouse", "error");
        }
        else
        {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/get-product-details",
                data: {pmpno:pmpno,location:location,warehouse:warehouse},
                type: "POST",
                dataType: "json",
                beforeSend:function(){  
                    // console.log("Before");
                },  
                success:function(res){
                    console.log(res);
                    if(res["status"]) {
                        $("#product_name").val(res.data[0]['part_name_id']);
                        $("#product_id").val(res.data[0]['product_id']);
                        //$("#product_name").html(""+res.data[0]['product_name']);
                    }else {
                        $("#product_name").val('');
                        $("#product_id").val('');
                        swal("Opps!", res["msg"], "error");
                    }
                },
            });
        }
        
    });
    $("body").on('change', '#location_id', function(){
        $("#warehouse_id").html("<option value=''>Select Warehouse *</option>");
        $("#pmpno").val("");
        $("#product_id").val("");
        $("#product_name").val("");
        var location_id = $(this).val();
        // console.log(location_id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/get-warehouse",
            data: {location_id:location_id},
            type: "POST",
            dataType: "json",
            beforeSend:function(){  
                // console.log("Before");
            },  
            success:function(res){
                $(res).each(function(i){
                $("#warehouse_id").append("<option value='"+res[i]['warehouse_id']+"'>"+res[i]['name']+"</option>");
            })
            hideLoader();
            },
            error:function(){
                swal({
                  title: "Sorry!",
                  text: "There is an error",
                  type: "error" 
                });
            },
            complete:function(){
                hideLoader();
            }
        });
    });
    $("body").on('change', '#warehouse_id', function(){
        $("#pmpno").val("");
        $("#product_id").val("");
        $("#product_name").val("");
    });
// Cities save
function stock_form() {
    $("#CommonModal").find("#StockForm").validate({
        rules: {
            pmpno: "required",
            product_id: "required",
            product_name: "required",
            warehouse_id: "required",
            stock_units: "required",
            qty: "required",
            lot_name: "required",
            unit_load: "required",
            location_id: "required",
            reserved_qty: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#StockForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-stock",  
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
                    console.log(res);
                    if(res["status"]) {
                        hideLoader();
                        $('#StockForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            stockTable.draw();
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
// Delete Js
$("body").on("click", "a.delete-stock", function(e) {                   
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
                url:base_url+"/delete-stock",  
                type: "POST",
                data:  {id: id},
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
                            stockTable.draw();
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

    