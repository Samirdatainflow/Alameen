var saleOrderTable = $('#save_order').DataTable({
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
        "url": base_url+"/save-order-list",
        "type": "POST",
        'data': function(data){
          
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'client_name', name: 'client_name', orderable: true, searchable: true},
        {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_brand" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
function ExportTable() {
    window.location.href = base_url+"/save-order-export";
}
$("body").on('click','.view-order-details',function(){
    var sale_order_id= $(this).data('sale-order-id');
    $.ajax({
        url:base_url+"/get-sale-order-details",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        data:{sale_order_id:sale_order_id},
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
$(document).on('click', '.edit-save-order', function() {
    var sale_order_id = $(this).data("sale-order-id");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:base_url+"/edit-new-order",
        type:'get',
        data: {sale_order_id: sale_order_id},
        dataType:'html',
        beforeSend:function(){
            showLoader();
        },
        success:function(res){
            hideLoader();
            $('.modal-title').text('').text("Update Order");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
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
$(document).on('click', '#SaveOrder', function(e){
  var form = $('#CreateNewOrderForm');
  e.preventDefault();
  var count_tr=$('table#new_order tbody tr');
  var last_tr=$('table#new_order tr').find('input');
  var x=0;
  if(count_tr.length == 0) {
      x=1;
  }
  $(last_tr).each(function(){
    if($(this).val()=="") {
      x=1;
    }
  })
  console.log(x);
  if(x==1) {
    swal("Warning!", "Enter data first", "error");
  }else {
    if($("#client").val() == "") {
      swal("Warning!", "Please select client", "error");
    }else {
      swal({
          title: "Are you sure?",
          text: "You want to save this order!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',
          confirmButtonText: 'Yes',
          cancelButtonText: "No",
      }).then(function(isConfirm) {
        if (isConfirm && isConfirm.value) {
          var formData = form.serializeArray();
          formData.push({ name: "order_status", value: "SaveOrder" });
          $.ajax({
            url : base_url+"/create-order", 
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
                    window.location.href=base_url+"/save-order";
                  });
              }
              else
              {
                swal("Warning!", res['msg'], "error");
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
  }
});
$(document).on('click', '#CreateOrder', function(e){
  var form = $('#CreateNewOrderForm');
  e.preventDefault();
  var count_tr=$('table#new_order tbody tr');
  var last_tr=$('table#new_order tr').find('input');
  var x=0;
  if(count_tr.length == 0) {
      x=1;
  }
  $(last_tr).each(function(){
    if($(this).val()=="") {
      x=1;
    }
  })
  if(x==1) {
    swal("Warning!", "Enter data first", "error");
  }else {
    if($("#client").val() == "") {
      swal("Warning!", "Please select client", "error");
    }else {
        swal({
            title: "Are you sure?",
            text: "You want to create this order!",
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
                url : base_url+"/create-order", 
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
                        window.location.href=base_url+"/save-order";
                    });
                }
                else {
                    swal("Warning!", res['msg'], "error");
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
  }
});
// New Item Add
$(document).on('click','.new_row',function(){
    // alert();
  var last_tr=$('table#new_order tr:last').find('input');
  var x=0;
  $(last_tr).each(function(){
    if($(this).val()=="")
    {
      x=1;
    }
  })
  if(x==1)
  {
    swal("Warning!", "Enter data first", "error");
  }else {
    var html='<tr><td><input type="hidden" name="product_id[]" class="form-control product_id"><input type="text" name="part_no[]" class="form-control part_no" autocomplete="off"></td><td><input type="text" readonly="readonly" name="name[]" class="form-control name"></td><td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name"><input type="hidden" name="category_id[]" class="form-control category_id"></td><td><input type="text" readonly="readonly" name="gst[]" class="form-control gst"></td><td><input type="text" name="mrp[]" class="form-control mrp"></td><td class="stock" style="display:none"></td><td><input type="number" min="1" name="qty[]" class="form-control qty"></td><td class="row_total"></td><td><button type="button" class="btn-shadow btn btn-info new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn-shadow btn btn-info trash" title="Trash"><i class="fa fa-trash"></i></button></td></tr>'
    $("#order_row").append(html);
  }
});
// Remove Item
$(document).on('click','.trash',function(){
    var count_tr=$('table#new_order tbody tr');
    if(count_tr.length > 1) {
        $(this).parents('tr').remove();
        final_calculation();
    }else {
        swal("Sorry!", "Can't remove the last record.", "warning");
    }
});
// Get product by part no
$(document).on('keyup input', '.part_no', function(element){
    var _this = $(this);
    var part_no = $(this).val();
    $.ajax({
      url : base_url+"/get-product-by-part-no-order", 
      type: 'POST',
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {part_no: part_no},
      dataType:'json',
      beforeSend:function(){  
        //$("#loader").css("display","block");
      },
      success: function(res){
        // console.log(res.data);
        if(res['status']) {
          console.log(res);
          _this.parents('td').find('.list-group').remove();
          _this.parents('td').find('.part_no').after(res.data);
        }else {
          //swal("Warning!", res['msg'], "error");
        }
      },
      error:function(error){
        swal("Warning!", "Sorry! There is an error", "error");
      },
      complete:function(){
        //$("#loader").css("display","none");
      }
    });
});
// Get Product Details
$(document).on("click",'.product-details',function(){
  var product_id = $(this).data('product-id');
  var pmpno = $(this).data('pmpno');
  _this = $(this);
  if(!checkAlreadyExistsProduct(_this, product_id)) {
    $.ajax({
      url : base_url+"/product-details", 
      type: 'POST',
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {part_no:pmpno},
      dataType:'json',
      beforeSend:function(){  
        //$("#loader").css("display","block");
      },
      success: function(res){
        console.log(res);
        if(res.length > 0) {
          _this.parents('tr').find('.part_no').val(res[0].pmpno);
          _this.parents('tr').find('.product_id').val(res[0].product_id);
          _this.parents('tr').find('.product_id').val(res[0].product_id);
          _this.parents('tr').find('.name').val(res[0].part_name);
          _this.parents('tr').find('.category_name').val(res[0].c_name);
          _this.parents('tr').find('.category_id').val(res[0].ct);
          _this.parents('tr').find('.gst').val(0);
          _this.parents('tr').find('.mrp').val(res[0].pmrprc);
          if(res[0].min_price > 0 && res[0].max_price > 0 && res[0].order_status != '')
          _this.parents('tr').find('.low_high').html("<span>Low Value: "+(res[0].min_price).toFixed(2)+"</span><br><span>High Value: "+(res[0].max_price).toFixed(2));
          _this.parents('tr').find('.qty').val("");
          _this.parents('tr').find('.qty').focus();
          _this.parents('tr').find('.row_total').html("");
          _this.parents('tr').find('.stock').html(res[0].current_stock==0?"":res[0].current_stock);
          _this.parents('td').find('.list-group').remove();
        }else {
          _this.parents('tr').find('.part_no').val('');
          _this.parents('tr').find('.product_id').val("");
          _this.parents('tr').find('.name').val("");
          _this.parents('tr').find('.category_name').val("");
          _this.parents('tr').find('.category_id').val("");
          _this.parents('tr').find('.gst').val("");
          _this.parents('tr').find('.mrp').val("");
          _this.parents('tr').find('.stock').html("");
        }
        
      },
      error:function(error){
        swal("Warning!", "Sorry! There is an error", "error");
      },
      complete:function(){
        //$("#loader").css("display","none");
      }
    });
  }else {
    swal("Warning!", "Sorry! You have already added this product", "error");
  }
});
function checkAlreadyExistsProduct(_this,product_id)
{
  var last_tr=$('table#new_order tr').find('.product_id');
  var r=0;
  $(last_tr).not(_this).each(function(){
    if($(this).val()==product_id)
    {
      r=1;
      
    }
  });
  return r;
}