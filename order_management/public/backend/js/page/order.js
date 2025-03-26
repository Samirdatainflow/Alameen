var item = $('#order').DataTable({
        filter: false,
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "ordering":true,
        "order": [0, ''],
        "ajax": {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            "url": base_url+"/get-order",
            "type": "POST",
            'data': function(data){
               
            },
            
        },
        'columns': [
            {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
            // {data: 'client_name', name: 'client_name', orderable: false, searchable: false},
            // {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
            // {data: 'discount', name: 'discount', orderable: false, searchable: false},
            {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
            {data: 'option', name: 'option', orderable: false, searchable: false},
            {data: 'remarks', name: 'remarks', orderable: false, searchable: false}
        ],
        createdRow: function( row, data, dataIndex ) {
        // Set the data-status attribute, and add a class
            $( row ).find('.rejected').parents('tr')
                .addClass('rejected_row');
        }
       
    }).on('xhr.dt', function(e, settings, json, xhr) {
       
  
    });
    
    $('#reset').on('click',function(){
        $('#product_name').val("");
        $('#category').val("");
        item.draw();
    });

    $('#product_list_download').on('click',function(){
      var product_name=$("#product_name").val();
      var category=$("#category").val();
      $.ajax({
        url : base_url+"/export-item-csv", 
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { category:category,product_name:product_name },
        beforeSend:function(){  
          $("#loader").css("display","block");
        },
        success: function(data){

          var downloadLink = document.createElement("a");
          var fileData = ['\ufeff'+data];

          var blobObject = new Blob(fileData,{
             type: "text/csv;charset=utf-8;"
           });

          var url = URL.createObjectURL(blobObject);
          downloadLink.href = url;
          downloadLink.download = "items.csv";

          /*
           * Actually download CSV
           */
          document.body.appendChild(downloadLink);
          downloadLink.click();
          document.body.removeChild(downloadLink);

        },
        complete:function(){
          $("#loader").css("display","none");
        }
      });
    });

    $('#order_row').on('click','.new_row',function(){
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
        var html='<tr><td><input type="hidden" name="product_id[]" class="form-control product_id"><input type="text" name="part_no[]" class="form-control part_no" autocomplete="off"></td><td><input type="text" readonly="readonly" name="name[]" class="form-control name"></td><td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name"><input type="hidden" name="category_id[]" class="form-control category_id"></td><td><input type="text" name="mrp[]" class="form-control mrp"><p class="low_high"></p></td><td class="stock" style="display:none"></td><td><input type="number" min="1" name="qty[]" class="form-control qty"></td><td class="row_total"></td><td><button type="button" class="btn bg-blue text-white new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn bg-blue text-white trash" title="Trash"><i class="fa fa-trash"></i></button></td></tr>'
        $("#order_row").append(html);
      }
      
    });
    $('#order_row').on('click','.trash',function(){
      $(this).parents('tr').remove();
      final_calculation();
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
    // $('#order_row').on('change','.part_no',function(){
    //   var _this=$(this);
    //   var part_no=$(this).val();
    //   if(!checkAlreadyExistsProduct(_this,part_no)) {
    //     $.ajax({
    //       url : base_url+"/product-details", 
    //       type: 'POST',
    //       headers: {
    //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //       },
    //       data: {part_no:part_no},
    //       dataType:'json',
    //       beforeSend:function(){  
    //         $("#loader").css("display","block");
    //       },
    //       success: function(res){
    //         if(res.length > 0)
    //         {
    //           _this.parents('tr').find('.product_id').val(res[0].product_id);
    //           _this.parents('tr').find('.name').val(res[0].part_name);
    //           _this.parents('tr').find('.category_name').val(res[0].c_name);
    //           _this.parents('tr').find('.category_id').val(res[0].ct);
    //           _this.parents('tr').find('.gst').val(0);
    //           _this.parents('tr').find('.mrp').val(res[0].pmrprc);
    //           _this.parents('tr').find('.stock').html(res[0].current_stock==0?"":res[0].current_stock);
    //         }
    //         else
    //         {
    //           _this.parents('tr').find('.product_id').val("");
    //           _this.parents('tr').find('.name').val("");
    //           _this.parents('tr').find('.category_name').val("");
    //           _this.parents('tr').find('.category_id').val("");
    //           _this.parents('tr').find('.gst').val("");
    //           _this.parents('tr').find('.mrp').val("");
    //           _this.parents('tr').find('.stock').html("");
    //         }
            
    //       },
    //       error:function(error){
    //         swal("Warning!", "Sorry! There is an error", "error");
    //       },
    //       complete:function(){
    //         $("#loader").css("display","none");
    //       }
    //     });
    //   }else {
    //     swal("Warning!", "Sorry! You have already added this product", "error");
    //   }
    // });
    $('#order_row').on('keyup paste','.qty',function(){
      var current_stock = parseFloat($(this).parents('tr').find('.stock').html());
      var mrp = $(this).parents('tr').find('.mrp').val();
      var qty = $(this).parents('tr').find('.qty').val();
      //var gst = $(this).parents('tr').find('.gst').val();
      // if(current_stock >= qty)
      // {
        var net_total = ((parseFloat(mrp)*parseFloat(qty)));
        $(this).parents('tr').find('.row_total').html(net_total);
        $(this).parents('tr').find('.qty').css('border-color','#ced4da');
        final_calculation();
      //}
      // else
      // {
      //   $(this).parents('tr').find('.qty').val("");
      //   $(this).parents('tr').find('.row_total').html("");
      //   $(this).parents('tr').find('.qty').css('border-color','red');
      // }
      
    });

    

    $("#download_template").on('click',function(){
      window.open(base_url+"/public/backend/file/order_template.csv");
    })

    function final_calculation(){
      var sub_total=0;
      var total_tax=0;
      var grand_total=0;
      $('#order_row tr').each(function(){
        var mrp = parseFloat($(this).find('.mrp').val());
        var qty = $(this).find('.qty').val()==""?0:parseFloat($(this).find('.qty').val());
        //var gst = parseFloat($(this).find('.gst').val());
        sub_total +=(mrp*qty);
        //total_tax +=((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100;
      });
      grand_total = sub_total+total_tax;
      $("#sub-total").val(sub_total.toFixed(2));
      $("#tax").val(total_tax.toFixed(2));
      $("#expertSubTotalWithTax").val(grand_total.toFixed(2));

      $("#sub-total1").html(sub_total.toFixed(2));
      $("#tax1").html(total_tax.toFixed(2));
      $("#expertSubTotalWithTax1").html(grand_total.toFixed(2));
    }

    $("#create_order").on('submit',function(e){
      var form = $(this);
      e.preventDefault();
      var last_tr=$('table#new_order tr').find('input');
      var x=0;
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
        }else if($("#vat_type_value").val() == "" || $("#vat_type_value").val() == null) {
          swal("Warning!", "Please select VAT", "error");
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
              var formData = form.serialize();
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
                        window.location.href=base_url+"/new-order";
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
    
    $('body').on('change', '#vat_type_value', function() {
        
        var vat = $(this).find(':selected').data('percentage');
        var sub_total = $('#sub-total').val();
        
        if(isNaN(vat)){
            $('#tax1').text(vat);
            $('#tax').val(vat);
            
            $('#expertSubTotalWithTax1').text(sub_total);
            $('#expertSubTotalWithTax').val(sub_total);
        }else {
            
            var totalTax = (sub_total * vat)/100;
            $('#tax1').text(totalTax);
            $('#tax').val(totalTax);
            
            var grandTotal = parseFloat(sub_total) + parseFloat(totalTax);
            $('#expertSubTotalWithTax1').text(grandTotal);
            $('#expertSubTotalWithTax').val(grandTotal);
        }
    });

    $("#order").on('click','.view-order-details',function(){
        var sale_order_id= $(this).data('sale-order-id');
        $.ajax({
            url:base_url+"/get-sale-order-details",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            data:{sale_order_id:sale_order_id},
            beforeSend:function(){
                $("#loader").css("display","block");
            },
            success:function(res){
                $('.modal-title').text('').text("Order Details (#"+sale_order_id+")");
                $("#orderDetailsModal").modal({
                  backdrop: 'static',
                  keyboard: false
                });
                $(".modal-body").html(res);
            },
            error:function(){
                $("#loader").css("display","none");
            },
            complete:function(){
                $("#loader").css("display","none");
            }
        })
    });
    $("#order").on('click','.view-reason',function(){
        var sale_order_id= $(this).data('sale-order-id');
        $.ajax({
            url:base_url+"/order/view-reason",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            data:{sale_order_id:sale_order_id},
            dataType: "json",
            beforeSend:function(){
                $("#loader").css("display","block");
            },
            success:function(res){
              if(res["status"]) {
                $('.modal-title').text('').text("Reject Reason");
                $("#orderDetailsModal").modal({
                  backdrop: 'static',
                  keyboard: false
                });
                $(".modal-body").html(res.reject_reason);
              }else {
                $(".modal-body").html("");
                swal("Sorry!", "No reason found", "warning");
              }
            },
            error:function(){
                $("#loader").css("display","none");
            },
            complete:function(){
                $("#loader").css("display","none");
            }
        })
    });
    $('.create_mutiple_order_csv').on('click',function(){
      if($("#client").val() == "") {
        swal("Warning!", "Please select client", "error");
      }else {
        var file_data = $('#product_csv').prop('files')[0];   
        if(file_data) {
          var form_data = new FormData();                  
          form_data.append('file', file_data);  
          form_data.append('client', $("#client").val());                        
          $.ajax({
            url: base_url+"/create-multiple-order", 
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',  
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            beforeSend:function(){
                  $("#loader").css("display","block");
            },
            success:function(res){
              if(res['status']) {
                 swal({
                    title: 'Success',
                    text: res['msg'],
                    icon: 'success',
                    type:'success',
                  }).then(function() {
                    window.location.reload();
                  });
              }else {
                swal({
                    title: 'Warning',
                    text: res['msg'],
                    icon: 'error',
                    type:'error',
                  }).then(function() {
                    window.location.reload();
                  });
              }
            },
            error:function(){
                $("#loader").css("display","none");
            },
            complete:function(){
                $("#loader").css("display","none");
            }
          });
        }
        else
        {
          swal("Warning!", "Please select file", "error");
        }
        
      }
    })
    $('.preview-multiple-order').on('click',function(){
      
        var file_data = $('#product_csv').prop('files')[0]; 
        if(file_data)
        {  
          var form_data = new FormData();                  
          form_data.append('file', file_data);                         
          $.ajax({
              url: base_url+"/order-preview", 
              headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType: 'html',  
              cache: false,
              contentType: false,
              processData: false,
              data: form_data,                         
              type: 'post',
              beforeSend:function(){
                    $("#loader").css("display","block");
              },
              success:function(res){
                $("#orderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                  });
                $(".order_details").html(res);
              },
              error:function(){
                  $("#loader").css("display","none");
              },
              complete:function(){
                  $("#loader").css("display","none");
              }
           });
        }
        else
        {
          swal("Warning!", "Please select file", "error");
        }
      
    })

    $('.file-upload-browse').on('click', function() {
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $('.file-upload-default').on('change', function() {
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });

    $('#orderDetailsModal').on('click','.delete-item',function(){
      var _this=$(this);
      var sale_order_details_id = $(this).data('sale-order-details-id');
      var total = $(this).data('total');
      var sale_order_id = $(this).data('sale-order-id');
      swal({
            title: "Are you sure?",
            text: "You want to remove this item",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
         }).then(function(isConfirm) {
           console.log(isConfirm);
          if (isConfirm && isConfirm.value) {
              $.ajax({
                    url:base_url+"/remove-order-item",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type:'POST',
                    data:{sale_order_details_id:sale_order_details_id,total:total,sale_order_id:sale_order_id},
                    beforeSend:function(){
                        $("#loader").css("display","block");
                    },
                    success:function(res){
                      if(res['status'])
                      {
                         swal({
                            title: 'Success',
                            text: res['msg'],
                            type:'success',
                          }).then(function() {
                            _this.parents('tr').remove();
                            item.draw();
                          });
                      }
                      else
                      {
                        swal({
                            title: 'Warning',
                            text: res['msg'],
                            type:'error',
                          }).then(function() {
                          });
                      }
                    },
                    error:function(){
                        $("#loader").css("display","none");
                    },
                    complete:function(){
                        $("#loader").css("display","none");
                    }
              })
      }
    });
  })

// List product by Part No
$("#new_order").on('keyup input', '.part_no', function(element){
    var _this = $(this);
    var part_no = $(this).val();
    $.ajax({
      url : base_url+"/get-product-by-part-no", 
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
        if(res['status']) {
          console.log(res);
          _this.parents('td').find('.list-group').remove();
          _this.parents('td').find('.part_no').after(res.data);
        }else {
          _this.parents('td').find('.list-group').remove();
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
$("#new_order").on("click",'.product-details',function(){
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
        if(res.length > 0) {
          _this.parents('tr').find('.part_no').val(res[0].pmpno);
          _this.parents('tr').find('.product_id').val(res[0].product_id);
          _this.parents('tr').find('.product_id').val(res[0].product_id);
          _this.parents('tr').find('.name').val(res[0].part_name);
          _this.parents('tr').find('.category_name').val(res[0].c_name);
          _this.parents('tr').find('.category_id').val(res[0].ct);
        //   _this.parents('tr').find('.gst').val(0);
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
          //_this.parents('tr').find('.gst').val("");
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
$('body').on('click', '.download-order-template', function() {
  var template_name = $(this).data('template_name');
  window.open(base_url+"/public/backend/file/upload_order_csv/"+template_name);
});