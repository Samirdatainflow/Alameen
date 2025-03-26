
// Table
var ExpenseTable = $('#ExpenseTable').DataTable({
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
        "url": base_url+"/config/list-expenses",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'expenses_description', name: 'expenses_description', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Add Expenses</button> <button type="button" aria-haspopup="true" id="" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/config/expenses-export";
}
function show_form(){
     $.ajax({
        url:base_url+"/config/add-expenses",
        type:'get',
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Add Expenses");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#formContent").html(res);
            expenses_form();
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
// Mail Api Key save
function expenses_form() {
    $("#CommonModal").find("#ExpensesForm").validate({
        rules: {
            expenses_description: "required"
        },
        submitHandler: function() {
            var formData = new FormData($('#ExpensesForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/config/save-expenses",  
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
                        $('#ExpensesForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            ExpenseTable.draw();
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
$('body').on('click',"a.edit-expenses",function(){
    var id = $(this).data('id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/config/edit-expenses",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            hideLoader();
            if(res["status"]) {
                $('.modal-title').text('').text("Update Expense");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $(".modal-dialog").addClass('modal-lg');
                $("#formContent").html(res["message"]);
                expenses_form();
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
// Delete Mail Api key 
// $("body").on("click", "a.delete-additional-charges", function(e) {                   
//     var obj = $(this);
//     var id = obj.data("id");
//     swal({
//         title: "Are you sure?",
//         text: "You want to delete it.",
//         type: "warning",
//         showCancelButton: !0,
//         confirmButtonText: "Yes.",
//         cancelButtonText: "No!",
//         confirmButtonClass: "btn btn-success mr-5",
//         cancelButtonClass: "btn btn-danger",
//         buttonsStyling: !1
//     }).then((result) => {
//         if (result.value) {
//             $.ajax({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                 },
//                 url:base_url+"/config/delete-additional-charges",  
//                 type: "POST",
//                 data:  {id: id},
//                 beforeSend:function(){  
//                     //$('#pageOverlay').css('display', 'block');
//                 },  
//                 success:function(res){
//                     if(res["status"]) {
//                         swal({
//                             title: "Success!",
//                             text: res["msg"],
//                             type: "success"
//                         }).then(function() {
//                             AdditionalChargesTable.draw();
//                         });
//                     }else {
//                         //$('#pageOverlay').css('display', 'none');
//                         swal("Opps!", res["msg"], "error");
//                     }
//                 },
//                 error: function(e) {
//                     //$('#pageOverlay').css('display', 'none');
//                     swal("Opps!", "There is an error", "error");
//                 },
//                 complete: function(c) {
//                     //$('#pageOverlay').css('display', 'none');
//                 }
//             });
//         } else if (
//             result.dismiss === Swal.DismissReason.cancel
//         ) {
//             swal("Cancelled", "Data is safe :)", "error")
//         }
//     })
// });