var roleAccessTable = $('#roleAccessList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-role-access",
        "type": "POST",
        'data': function(data){
          
        },
        
    },
    'columns': [
        {data: 'role_name', name: 'role_name', orderable: true, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_user" aria-expanded="false" class="btn-shadow btn btn-info" onclick="add_role_access()"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Role Access</button> <button type="button" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportRoleAccessTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function ExportRoleAccessTable() {
    window.location.href = base_url+"/role-access-export";
}


function add_role_access() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/add-role-access",
        type:'post',
        dataType:'JSON',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            console.log(res);
            hideLoader();
            if(res['status']) {
                $('.modal-title').text('').text("Add Access");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res['message']);
                user_role_access_form();
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
function user_role_access_form() {
    $("#CommonModal").find("#userRoleAccessForm").validate({
        rules: {
            warehouse_id: "required",
            fk_role_id: "required",
        },
        submitHandler: function() {
            var formData = new FormData($('#userRoleAccessForm')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/save-user-role-access",  
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
                        $('#userRoleAccessForm')[0].reset();
                        swal({title: "Success", text: res["msg"], type: "success"}).then(function() {
                            $('#CommonModal').modal('hide');
                            roleAccessTable.draw();
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
$("body").on("change", ".submenu", function(e) {
    if ($(this).is(':checked')) {
        var obj = $(this);
        var parent = obj.data("parent");
        $('#menu'+parent).prop("checked", true);
    }
})
$("body").on("change",".parent_menu",function(){
    if ($(this).is(':checked')) {
        var obj = $(this);
        obj.parents("li").find(".submenu").prop("checked", true);
    }
    else if (!$(this).is(':checked')) {
        var obj = $(this);
        obj.parents("li").find(".submenu").prop("checked", false);
    }
})
$('body').on('click',"a.view-user-role-access",function(){
    var id = $(this).data('id');
    var name = $(this).data('name');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/view-user-role-access",  
        type: "POST",
        data:  {id:id},
        dataType:"json", 
        beforeSend:function(){  
            showLoader();
        },  
        success:function(res){
            if(res["status"]) {

                $('.modal-title').text('').text("User Role Access ("+name+")");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res["message"]);
                user_role_access_form();
                hideLoader();
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
})