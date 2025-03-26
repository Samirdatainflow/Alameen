// List
var CarName = $('#CarNameList').DataTable({
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
        "url": base_url+"/list-car-name",
        "type": "POST",
        'data': function(data){},
    },
    'columns': [
        {data: 'car_name', name: 'car_name', orderable: true, searchable: false},
        {data: 'brand_name', name: 'brand_name', orderable: true, searchable: false},
        {data: 'car_manufacture', name: 'car_manufacture', orderable: true, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()" title="Add New Car Name"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add New</button> <button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()" title="Add New Car Name"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/car-name-export";
}
// Add Form
function show_form(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/add-car-name",
        type: 'post',
        dataType: 'html',
        beforeSend: function(){
            showLoader();
        },
        success: function(res){
            hideLoader();
            $('.modal-title').text('').text("Add Car Name");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#formContent").html(res);
            car_name_form();
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
// Save Data
function car_name_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#CarNameForm").validate({
        rules: {
            car_manufacture_id: "required",
            brand_id: "required",
            car_name: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#CarNameForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-car-name",  
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
                    // console.log(res);
                    if(res["status"]) {
                        hideLoader();
                        $('#CarNameForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            CarName.draw();
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
// Delete 
$("body").on("click", "a.delete-car-name", function(e) {                   
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
                url:base_url+"/delete-car-name",  
                type: "POST",
                data:  {id: id},
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
                            CarName.draw();
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
// Edit -->
$('body').on('click',"a.edit-car-name",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-car-name",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {
                hideLoader();
                $('.modal-title').text('').text("Update Car Name");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                car_name_form();
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
// Get Car Model by Menufacture
function getCarModel(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/get-car-model-by-car-manufacture",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            //$('#brand_id').find('option:not(:first)').remove();
            if(res["status"]) {
                var data = "";
                for(i=0; i<res.data.length; i++){
                    data += '<option value="'+res.data[i]['brand_id']+'">'+res.data[i]['brand_name']+'</option>';
                }
                $("#brand_id").html(data);
                $("#brand_id").selectpicker('refresh');
            }else {
                var data = "";
                $("#brand_id").html(data);
                $("#brand_id").selectpicker('refresh');
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