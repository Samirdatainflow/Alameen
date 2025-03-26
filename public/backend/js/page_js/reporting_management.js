// Table
var ReportingListTable = $('#ReportingList').DataTable({
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
        "url": base_url+"/list-reporting-management",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'reporting_manager', name: 'reporting_manager', orderable: false, searchable: false},
        {data: 'reporting_name', name: 'reporting_name', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Reporting </button>');
// MOdal Show
function show_form() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/add-reporting-management",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res['status']) {
                $('.modal-title').text('').text("Add Reporting");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                //$(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res['message']);
                reporting_form();
            }
        },
        error:function(){
            hideLoader();
            swal({title: "Sorry!", text: "There is an error", type: "error"});
        },
        complete:function(){
            hideLoader();
        }
    })
}

function reporting_form() {
    $('.selectpicker').selectpicker().change(function(){
        $(this).valid()
    });
    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
    }).change(function(){
        $(this).valid()
    });
    $("#CommonModal").find("#userReportingForm").validate({
        rules: {
            reporting_manager_id: "required",
            reporting_id: "required",
        },
        messages: {
            'warehouse_id[]': "This field is required",
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "reporting_manager_id") {
                error.appendTo(element.parent());
            }else if (element.attr("name") == 'reporting_id') {
                error.appendTo(element.parent());
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            var formData = new FormData($('#userReportingForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-reporting-management",  
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
                        $('#userReportingForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            ReportingListTable.draw();
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

// $('body').on('change', '#reporting_manager_id', function() {
//     var variable = $(this).val();
//     $("#reporting_id").val('');
//     $("#reporting_id option").prop('disabled', false);
//     $("#reporting_id option[value='"+ variable + "']").attr('disabled', true);
// });

$('body').on('click',"a.edit-reporting-management",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-reporting-management",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Reporting");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                reporting_form();
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

$("body").on("click", "a.delete-reporting-management", function(e) {                   
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
                url:base_url+"/delete-reporting-management",  
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
                            ReportingListTable.draw();
                        });
                    }else {
                        
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    
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

