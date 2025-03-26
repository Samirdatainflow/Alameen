$('.datepicker').datepicker({
    format  :  'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true,
}).change(function(){
    //$(this).valid()
});
var approveSaleOrderTable = $('#approveSaleOrder').DataTable({
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
        "url": base_url+"/get-approve-sale-order",
        "type": "POST",
        'data': function(data){
            data.filter_part_no=$("#filter_part_no").val();
            data.filter_part_brand=$("#filter_part_brand").val();
            data.filter_part_name=$("#filter_part_name").val();
            data.filter_customer=$("#filter_customer").val();
            data.filter_from_date=$("#filter_from_date").val();
            data.filter_to_date=$("#filter_to_date").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'client_name', name: 'client_name', orderable: true, searchable: true},
        {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$("#filter_part_no").on('keyup input',function(){
    approveSaleOrderTable.draw();
    chnageDateFilter();
});
$("#filter_part_brand").on('change',function(){
    approveSaleOrderTable.draw();
    chnageDateFilter();
});
$("#filter_part_name").on('change',function(){
    approveSaleOrderTable.draw();
    chnageDateFilter();
});
$("#filter_customer").on('change',function(){
    approveSaleOrderTable.draw();
    chnageDateFilter();
});
$("#filter_from_date").on('change',function(){
    approveSaleOrderTable.draw();
});
$("#filter_to_date").on('change',function(){
    approveSaleOrderTable.draw();
});
$('body').on('click', 'a.rest-filter', function() {
    $('#filter_part_no').val('');
    $('#filter_part_brand').val('');
    $('#filter_part_name').val('');
    $('#filter_customer').val('');
    $('#filter_from_date').val('');
    $('#filter_to_date').val('');
    approveSaleOrderTable.draw();
});
function chnageDateFilter() {
    
    $('#filter_to_date').datepicker('setDate', 'now');
    var startDate = $('#filter_to_date').datepicker('getDate');
    
    startDate.setTime(startDate.getTime() - (1000*60*60*24*5));
    $('#filter_from_date').datepicker("setDate", startDate);
}
$("body").on('click','.view-order-details',function(){
    var sale_order_id= $(this).data('sale-order-id');
    var ordersatatus= $(this).data('ordersatatus');
    $.ajax({
        url:base_url+"/get-approve-sale-order-details",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        data:{sale_order_id:sale_order_id, ordersatatus:ordersatatus},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            $('.modal-title').text('').text("Order Details (#"+sale_order_id+")");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").addClass('modal-lg');
            $("#formContent").html(res);
        },
        error:function(){
            hideLoader();
        },
        complete:function(){
            hideLoader();
        }
    })
});

$("body").on('click',".approved-order",function(){
    var sale_order_id= $(this).data('sale-order-id');
    $.ajax({
        url:base_url+"/get-sale-order-details-for-approve",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        data:{sale_order_id:sale_order_id},
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            $('.modal-title').text('').html('Order Details (#'+sale_order_id+') <a href="javascript:void(0)" class="add-entry-product"><button type="button" class="btn btn-success btn-sm" title="Add Entry"><i class="fa fa-plus" aria-hidden="true"></i></button></a>');
            $("#CommonModal").modal('show');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            $("#sale_order_id").val(sale_order_id);
        },
        error:function(){
            hideLoader();
        },
        complete:function(){
            hideLoader();
        }
    }) 
});

$(document).on('keyup paste','.appr_qty',function() {
    
    var appr_qty = $(this).val();
    var current_stock = $(this).parents('tr').find('.current-stock').val();
    if(parseInt(appr_qty) > parseInt(current_stock)) {
        $(this).val(current_stock);
    }
  
});

$("#CommonModal").on("submit",'#sale_order_approve',function(e){
    var form_data=$(this).serialize();
    e.preventDefault();
    var r=0;
    $("#approve_table tbody").find("tr").each(function(){
        var prev_qty=$(this).find(".prev_qty").val();
        var appr_qty=$(this).find(".appr_qty").val();
        console.log(prev_qty+" "+appr_qty);
        if(appr_qty == "")
        {
            r=1;
            $(this).find(".appr_qty").focus();
        }
        else if(parseInt(prev_qty) < parseInt(appr_qty))
        {
            r=1;
            $(this).find(".appr_qty").focus();
            swal({title: "Sorry!", text: "Approve quantity can not be more than order quantity", type: "error"});
        }
    });
    if(r==0) {
        var last_tr = $('body #ListProductEntry tr').find('input');
        var x=0;
        $(last_tr).each(function(){
            if($(this).val()=="" && !$(this).hasClass('prev-product-tax') && !$(this).hasClass('price') && !$(this).hasClass('qty')) {
                x=1;
            }
        })
        if(x==1) {
            swal("Warning!", "Enter data first", "error");
        }else {
            $.ajax({
                url:base_url+"/approve-order",
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                data:form_data,
                dataType:'json',
                beforeSend:function(){
                    showLoader();
                },
                success:function(res){
                    if(res.status)
                    {
                        swal({title: "Success", text: res.msg, type: "success"});
                    }
                    else
                    {
                        swal({title: "Sorry!", text: res.msg, type: "error"});
                    }
                    approveSaleOrderTable.draw();
                    $("#CommonModal").modal('hide');
                },
                error:function(){
                    hideLoader();
                },
                complete:function(){
                    hideLoader();
                }
            })
        }
    }
});

$('body').on('click',"a.approved-quantity-update-form",function(){
  var id = $(this).data('sale_order_details_id');
  var current_stock = $(this).data('current_stock');
  var sl = $(this).data('sl');
  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url:base_url+"/sale-order/approve-quantity-update-form",  
      type: "POST",
      data:  {id:id},
      dataType:"json", 
      beforeSend:function(){  
          showLoader();
      },  
      success:function(res){
          hideLoader();
          if(res["status"]) {
              $('#OrderPreviewModal .modal-title').text('').text("Update Quantity");
              $("#OrderPreviewModal").modal({
                  backdrop: 'static',
                  keyboard: false
              });
              $("#OrderPreviewModal .modal-dialog").removeClass('modal-lg');
              $("#OrderPreviewModal #formContent").html(res["message"]);
              $('#OrderPreviewModal #formContent #current_stock').val(current_stock);
              $('#OrderPreviewModal #formContent #sl').val(sl);
              order_quantity_update_form();
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
function order_quantity_update_form() {
  $("#OrderPreviewModal").find("#orderQuantityUpdateForm").validate({
      rules: {
          qty: "required"
      },
      submitHandler: function() {
        var formData = new FormData($('#orderQuantityUpdateForm')[0]);
        var qty=$('#qty').val();
        var sl=$('#sl').val();
        var current_stock=$('#current_stock').val();

        if(parseInt(qty) > parseInt(current_stock)) {
          swal("Sorry!", "Enter quantity not available in stock!", "warning");
          return false;
        }else {
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url:base_url+"/sale-order/update-approved-order-quantity",  
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
                      $('#orderQuantityUpdateForm')[0].reset();
                      swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                        $('#showQty'+sl).html('').html(res["qty"]);
                        $('#OrderPreviewModal').modal('hide');
                          
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

$("body").on("click", "a.delete-sale-order-details", function(e) {                  
    var obj = $(this);
    var id = obj.data("id");
    var sale_order_id = obj.data("sale_order_id");
    var line_no = obj.data("line-no");
    swal({
        title: "Are you sure?",
        text: "You want to remove it.",
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
                url:base_url+"/sale-order/delete-approve-sale-order-details",  
                type: "POST",
                data:  {id: id, sale_order_id:sale_order_id},
                beforeSend:function(){  
                },  
                success:function(res){
                    if(res["status"]) {
                        swal({
                            title: "Success!",
                            text: res["msg"],
                            type: "success"
                        }).then(function() {
                            $('#orderDetailsTr'+line_no).remove();
                        });
                    }else {
                        swal("Opps!", res["msg"], "error");
                    }
                },
                error: function(e) {
                    swal("Opps!", "There is an error", "error");
                },
                complete: function(c) {
                }
            });
        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swal("Cancelled", "Data is Not Reject :)", "error")
        }
    })
});
