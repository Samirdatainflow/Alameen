    var saleOrderTable = $('#sale_order').DataTable({
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
            "url": base_url+"/get-sale-order",
            "type": "POST",
            'data': function(data){
              
            },
            
        },
        'columns': [
            {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
            {data: 'invoice_no', name: 'invoice_no', orderable: true, searchable: false},
            {data: 'client_name', name: 'client_name', orderable: true, searchable: true},
            {data: 'company_name', name: 'company_name', orderable: false, searchable: false},
            {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
       
    }).on('xhr.dt', function(e, settings, json, xhr) {

    });
    $('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_order1_management" aria-expanded="false" class="btn-shadow btn btn-info" onclick="show_form()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-plus fa-w-20"></i></span>Create New Order</button> <button type="button" style="margin: 2px;" aria-haspopup="true" id="add_catagory" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportSaleOrderTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

    function ExportSaleOrderTable() {
    window.location.href = base_url+"/sale-order-management-export";
    }

    function show_form(){
        $.ajax({
            url:base_url+"/add-new-order",
            type:'get',
            dataType:'html',
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                // console.log(res);
                hideLoader();
                // if(res['status']) {
                $('.modal-title').text('').text("New Order");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").removeClass('modal-lg');
                $(".modal-dialog").addClass('modal-xl');
                $("#formContent").html(res);
                $('.input-tags').tagsInput();
                $("#engine_tag").attr("placeholder", "Engine No*");
                $("#engine_tag").css("width", "100px");
                $("#chassis_model_tag").attr("placeholder", "Chassis / Model");
                $("#chassis_model_tag").css("width", "100px");
                $("#manfg_no_tag").attr("placeholder", "Manufacturer No*");
                $("#manfg_no_tag").css("width", "110px");
                $("#altn_part_tag").attr("placeholder", "Alternate Part No*");
                $("#altn_part_tag").css("width", "110px");
                // order_management_form();
                $('.selectpicker').selectpicker().change(function(){
                    $(this).valid()
                });
                $("#preview").hide();
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
    // Edit
    $(document).on('click', '.sales-order-edit', function() {
        var sale_order_id = $(this).data("sale-order-id");
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/sales-order/sales-order-edit",
            type:'get',
            data: {sale_order_id: sale_order_id},
            dataType:'html',
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                // console.log(res);
                hideLoader();
                // if(res['status']) {
                $('.modal-title').text('').text("Update Order");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $(".modal-dialog").removeClass('modal-lg');
                $(".modal-dialog").addClass('modal-xl');
                $("#formContent").html(res);
                $('.selectpicker').selectpicker().change(function(){
                    $(this).valid()
                });
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
    
    $(document).on('click', '.edit-order-details', function() {
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
            // console.log(res);
            hideLoader();
            // if(res['status']) {
            $('.modal-title').text('').text("Update Order");
            $("#CommonModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-dialog").removeClass('modal-lg');
            $(".modal-dialog").addClass('modal-xl');
            $("#formContent").html(res);
            //$('.input-tags').tagsInput();
            // $("#engine_tag").attr("placeholder", "Engine No*");
            // $("#engine_tag").css("width", "100px");
            // $("#chassis_model_tag").attr("placeholder", "Chassis / Model");
            // $("#chassis_model_tag").css("width", "100px");
            // $("#manfg_no_tag").attr("placeholder", "Manufacturer No*");
            // $("#manfg_no_tag").css("width", "110px");
            // $("#altn_part_tag").attr("placeholder", "Alternate Part No*");
            // $("#altn_part_tag").css("width", "110px");
            // $("#preview").hide();
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
    });
    $(document).on('click','.new_row',function(){
       $('#displayMessage').html('');
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
        var html='<tr><td><input type="hidden" name="product_id[]" class="form-control product_id"><input type="text" name="part_no[]" class="form-control part_no" autocomplete="off"></td><td><input type="text" readonly="readonly" name="name[]" class="form-control name"></td><td><input type="text" readonly="readonly" name="category_name[]" class="form-control category_name"><input type="hidden" name="category_id[]" class="form-control category_id"></td><td><input type="text" name="mrp[]" class="form-control mrp"><div class="pre-price-details"></div><input type="hidden" class="last-lp-price"><input type="hidden" class="hidden-lc-price"></td><td class="stock" style="display:none"></td><td><input type="number" min="1" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" name="qty[]" class="form-control qty"><div class="avl-qty-details"></div></td><td class="row_total"></td><td><button type="button" class="btn-shadow btn btn-info new_row" title="Add New Row"><i class="fa fa-plus"></i></button> <button type="button" class="btn-shadow btn btn-info trash" title="Trash"><i class="fa fa-trash"></i></button></td></tr>'
        $("#order_row").append(html);
      }
    });
    $(document).on('click','.trash',function(){
        // alert();
      $(this).parents('tr').remove();
      final_calculation();
    });
    function checkAlreadyExistsProduct(_this,product_id)
    {
      var parentTr = $(_this).parents("tr");
      var last_tr=$('table#new_order tr').not(parentTr).find('.product_id');
      var r=0;
      $(last_tr).not(_this).each(function(){
        if($(this).val()==product_id)
        {
          r=1;
          
        }
      });
      return r;
    }
    $(document).on('change','.mrp',function()
    {
        var mrp = $(this).parents('tr').find('.mrp').val();
        var last_lp_price = $(this).parents('tr').find('.hidden-lc-price').val();
        if(last_lp_price > 0)
        {
            mrp = parseFloat(mrp);
            last_lp_price = parseFloat(last_lp_price);
            if(mrp < last_lp_price)
            {
                $(this).parents('tr').find('.mrp').val('').focus();
            }
        }
      
    });
    
    $(document).on('keyup paste','.qty',function()
    {
        
      var mrp = $(this).parents('tr').find('.mrp').val();
      var qty = $(this).parents('tr').find('.qty').val();
      var part_no = $(this).parents('tr').find('.part_no').val();

      if(qty != "" && qty > 0 && mrp > 0) {
        var net_total = parseFloat(mrp) * parseFloat(qty);
        net_total = net_total.toFixed(3);
        $(this).parents('tr').find('.row_total').html(net_total);
        $(this).parents('tr').find('.qty').css('border-color','#ced4da');

        final_calculation();
      }
      
    });
    // $(document).on('keyup paste','.qty',function()
    // {

    //   var current_stock = parseFloat($(this).parents('tr').find('.stock').html());
    //   console.log("current_stock", current_stock);
    //   var mrp = $(this).parents('tr').find('.mrp').val();
    //   var qty = $(this).parents('tr').find('.qty').val();
    //   var part_no = $(this).parents('tr').find('.part_no').val();

    //   if(qty > current_stock || isNaN(current_stock)) {
    //     $('#displayMessage').html('').append('<span style="color:red">This part no "'+part_no+'" quantity greater than stock, it will be placed in non stock order.</span><br/>');
    //   }

    //   var gst = $(this).parents('tr').find('.gst').val();
    //   var tax_rate = $('#hidden_tax_rate').val();
    //   var cal_gst = 0;

    //   if(qty != "" && qty > 0 && mrp > 0) {
    //     var net_total = parseFloat(mrp) * parseFloat(qty);
    //     cal_gst = (net_total * tax_rate) / 100;
    //     cal_gst = cal_gst.toFixed(3);
    //     net_total = net_total.toFixed(3);
    //     $(this).parents('tr').find('.row_total').html(net_total);
    //     $(this).parents('tr').find('.gst').val(cal_gst);
    //     $(this).parents('tr').find('.qty').css('border-color','#ced4da');

    //     final_calculation();
    //   }
      
    // });
    
    function final_calculation(){
      var sub_total=0;
      var total_tax=0;
      var grand_total=0;
      $('#order_row tr').each(function(){
        var mrp = parseFloat($(this).find('.mrp').val());
        var qty = $(this).find('.qty').val()==""?0:parseFloat($(this).find('.qty').val());
        var csub_total =(mrp*qty);
        sub_total += parseFloat(csub_total);
      });
      grand_total = sub_total+total_tax;
      $("#sub-total").val(sub_total.toFixed(3));
      $("#expertSubTotalWithTax").val(grand_total.toFixed(3));

      $("#sub-total1").html(sub_total.toFixed(3));
      $("#expertSubTotalWithTax1").html(grand_total.toFixed(3));
    }
    
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
            totalTax = totalTax.toFixed(3);
            $('#tax1').text(totalTax);
            $('#tax').val(totalTax);
            
            var grandTotal = parseFloat(sub_total) + parseFloat(totalTax);
            grandTotal = grandTotal.toFixed(3);
            $('#expertSubTotalWithTax1').text(grandTotal);
            $('#expertSubTotalWithTax').val(grandTotal);
        }
    });

    // function final_calculation(){
    //   var sub_total=0;
    //   var total_tax=0;
    //   var grand_total=0;
    //   var cal_gst = 0;
    //   var tax_rate = $('#hidden_tax_rate').val();
    //   $('#order_row tr').each(function(){
    //     var mrp = parseFloat($(this).find('.mrp').val());
    //     var qty = $(this).find('.qty').val()==""?0:parseFloat($(this).find('.qty').val());
    //     var gst = parseFloat($(this).find('.gst').val());
    //     var csub_total =(mrp*qty);
    //     //total_tax +=((parseFloat(mrp)*parseFloat(qty))*parseFloat(gst))/100;
    //     cal_gst = (csub_total * tax_rate)/100;
    //     total_tax += cal_gst;
    //     cal_gst = cal_gst.toFixed(3);
    //     //sub_total += parseFloat(csub_total) + parseFloat(cal_gst);
    //     sub_total += parseFloat(csub_total);
    //     //console.log("sub_total", sub_total);
    //   });
    //   grand_total = sub_total+total_tax;
    //   $("#sub-total").val(sub_total.toFixed(3));
    //   $("#tax").val(total_tax.toFixed(3));
    //   $("#expertSubTotalWithTax").val(grand_total.toFixed(3));

    //   $("#sub-total1").html(sub_total.toFixed(3));
    //   $("#tax1").html(total_tax.toFixed(3));
    //   $("#expertSubTotalWithTax1").html(grand_total.toFixed(3));
    // }
    // List product by Part No
    $(document).on('keyup input', '.part_no', function(element){
        var client_id = $('#client').val();
        if(client_id == "" || client_id == null)
        {
            $(this).val('');
            swal("Sorry!", "Please select a customer", "warning");
        }
        else
        {
            var _this = $(this);
            var part_no = $(this).val();
            $.ajax({
              url : base_url+"/get-product-by-part-no-order", 
              type: 'POST',
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {part_no: part_no, client_id:client_id},
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
        }
    });
    // Get Product Details
    $(document).on("click",'.product-details',function(){
      var product_id = $(this).data('product-id');
      var pmpno = $(this).data('pmpno');
      var current_stock = $(this).data('current_stock');
      var lp_amount = $(this).data('lp_amount');
      var ls_amount = $(this).data('ls_amount');
      var lc_amount = $(this).data('lc_amount');
      var selling_price = $(this).data('selling_price');
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
              //_this.parents('tr').find('.mrp').val(res[0].pmrprc);
              _this.parents('tr').find('.last-lp-price').val(lp_amount);
              _this.parents('tr').find('.hidden-lc-price').val(lc_amount);
              //_this.parents('tr').find('.mrp').after().html('');
              _this.parents('tr').find('.pre-price-details').html('').html('<span> SP: '+selling_price+'</span><br><span> LP: '+lp_amount+'</span><br><span> LS: '+ls_amount+'</span><br><span> LC: '+lc_amount+'</span>');
              if(res[0].min_price > 0 && res[0].max_price > 0 && res[0].order_status != '')
              _this.parents('tr').find('.low_high').html("<span>Low Value: "+(res[0].min_price).toFixed(2)+"</span><br><span>High Value: "+(res[0].max_price).toFixed(2));
              _this.parents('tr').find('.qty').val("");
              _this.parents('tr').find('.qty').focus();
              _this.parents('tr').find('.qty').after('');
              _this.parents('tr').find('.avl-qty-details').html('').html('<span> Avl: '+current_stock+'</span>');
              _this.parents('tr').find('.row_total').html("");
              _this.parents('tr').find('.stock').html(res[0].current_stock==0?"":res[0].current_stock);
              //_this.parents('tr').find('.current-stock').val(res[0].current_stock==0?"":res[0].current_stock);
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
    $(document).on('click', '.preview-multiple-order', function(){
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
                $("#OrderPreviewModal").modal({
                    backdrop: 'static',
                    keyboard: false
                  });
              	$('#OrderPreviewModal .modal-title').text('').text("Order Preview");
                $("#OrderPreviewModal #formContent").html(res);
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
    $(document).on('click','.create_mutiple_order_csv', function(){
        
        var vat_type_value = $('#OrderPreviewModal #vat_type_value2').val();
        if($("#client").val() == "") {
            swal("Warning!", "Please select client!", "error");
        }else if(vat_type_value == "") {
            swal("Warning!", "Please select VAT!", "error");
        }else {
            
            var vat_percentage = $('#OrderPreviewModal #vat_type_value2').find(':selected').data('percentage');
            
            var file_data = $('#product_csv').prop('files')[0];   
            if(file_data) {
              var form_data = new FormData();                  
              form_data.append('file', file_data);  
              form_data.append('client', $("#client").val());
              form_data.append('vat_type_value', vat_type_value);
              form_data.append('vat_percentage', vat_percentage);
              
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
    });
    
    $(document).on('click','.file-upload-browse', function() {
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $(document).on('change', '.file-upload-default', function() {
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
    $(document).on('change','#client', function() {
    	$("#preview").show();
    });
 //    $(document).ready(function(){
	//   $("#client").click(function(){
	    
	//   });
	// });
    $(document).on('click', '#download_template', function(){
      window.open(base_url+"/public/backend/file/order_template.csv");
    })
    $(document).on('click', '#CreateOrder', function(e){
      var form = $('#CreateNewOrderForm');
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
        if($("#client").val() == "" || $("#client").val() == null) {
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
                  if(res['status'])
                  {
                     swal({
                        title: 'Success',
                        text: res['msg'],
                        icon: 'success',
                        type:'success',
                      }).then(function() {
                        window.location.href=base_url+"/sale-order-management";
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
    $(document).on('click', '#UpdateOrder', function(e){
      var form = $('#CreateNewOrderForm');
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
        if($("#client").val() == "" || $("#client").val() == null) {
          swal("Warning!", "Please select client", "error");
        }else if($("#vat_type_value").val() == "" || $("#vat_type_value").val() == null) {
          swal("Warning!", "Please select VAT", "error");
        }else {
          swal({
              title: "Are you sure?",
              text: "You want to update this order!",
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
                  if(res['status'])
                  {
                     swal({
                        title: 'Success',
                        text: res['msg'],
                        icon: 'success',
                        type:'success',
                      }).then(function() {
                        window.location.href=base_url+"/sale-order-management";
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
    $(document).on('click', '#SaveOrder', function(e){
      var form = $('#CreateNewOrderForm');
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
        if($("#client").val() == "" || $("#client").val() == null) {
          swal("Warning!", "Please select client", "error");
        }else if($("#vat_type_value").val() == "" || $("#vat_type_value").val() == null) {
          swal("Warning!", "Please select VAT", "error");
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
                        window.location.href=base_url+"/sale-order-management";
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
    })

    $("#sale_order").on('click','.view-order-details',function(){
        var sale_order_id= $(this).data('sale-order-id');
        var ordersatatus= $(this).data('ordersatatus');
        $.ajax({
            url:base_url+"/get-sale-order-details",
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
    })

    $("#sale_order").on('click',".approved-order",function(){
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
                $(".modal-dialog").addClass('modal-lg');
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
    })
    // Add product entry
    $('body').on('click', 'a.add-entry-product', function() {
        var product_entry_count = $('#product_entry_count').val();
        $('#ListProductEntry').append('<tr id="entryProductRow'+product_entry_count+'"><td><input type="text" class="form-control entry-part-no" name="entry_part_no[]" autocomplete="off"></td><td><input type="text" class="form-control entry-product-name" name="entry_product_name[]" readonly="readonly"><input type="hidden" class="form-control entry-product-id" name="entry_product[]"></td><td><input type="number" class="form-control price" name="price[]" readonly="readonly"></td><td><input type="text" class="form-control qty" name="qty[]" readonly="readonly"></td><td><input type="number" class="form-control entry-product-approve-quantity" name="entry_product_approve_quantity[]" placeholder="Enter qty"></td><td style="width: 12%;"><button type="button" class="btn btn-danger btn-sm" title="Remove" onclick="removeProductEntry('+product_entry_count+')"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>');
        $('#product_entry_count').val(parseInt(product_entry_count)+1);
    });
    function removeProductEntry(line_no) {
        $('#entryProductRow'+line_no).remove();
    }
    $("body").on('keyup input', '#ListProductEntry .entry-part-no', function(element){
        var _this = $(this);
        var part_no = $(this).val();
        if(part_no != "") {
            $.ajax({
                url : base_url+"/sale-order/get-product-by-part-no", 
                type: 'POST',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {part_no: part_no},
                dataType:'json',
                beforeSend:function(){  
                    //showLoader();
                },
                success: function(res){
                    console.log(res);
                    if(res['status']) {
                        hideLoader();
                        _this.parents('td').find('.list-group').remove();
                        _this.parents('td').find('.entry-part-no').after(res.data);
                    }else {
                        _this.parents('td').find('.list-group').remove();
                        //hideLoader();
                    }
                },
                error:function(error){
                    //hideLoader();
                    swal("Warning!", "Sorry! There is an error", "error");
                },
                complete:function(){
                    //hideLoader();
                }
            });
        }else {
            _this.parents('td').find('.list-group').remove();
            _this.parents('tr').find('.entry-part-no').val('');
            _this.parents('tr').find('.entry-product-name').val('');
            _this.parents('tr').find('.entry-product-id').val('');
            _this.parents('tr').find('.entry-product-id').val('');
            _this.parents('tr').find('.price').val('');
        }
    });
    $("body").on("click",'#ListProductEntry .product-details',function(){
        var product_entry_count = $('#product_entry_count').val();
        var pmpno = $(this).data('pmpno');
        _this = $(this);
        //if(!checkAlreadyExistsProduct(_this, pmpno)) {
            $.ajax({
                url : base_url+"/purchase_order/get-product-details", 
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {part_no:pmpno, product_entry_count:product_entry_count},
                dataType:'json',
                beforeSend:function(){  
                    //showLoader();
                },
                success: function(res){
                    console.log(res);
                    if(res.data.length > 0) {
                        hideLoader();
                        _this.parents('tr').find('.entry-part-no').val(res.data[0].pmpno);
                        _this.parents('tr').find('.entry-product-name').val(res.data[0].part_name);
                        _this.parents('tr').find('.entry-product-id').val(res.data[0].product_id);
                        _this.parents('tr').find('.entry-product-id').val(res.data[0].product_id);
                        _this.parents('tr').find('.price').val(res.data[0].pmrprc);
                        //$('#product_entry_count').val(res.product_entry_count);
                        _this.parents('td').find('.list-group').remove();
                    }else {
                        //hideLoader();
                        _this.parents('tr').find('.entry-part-no').val('');
                        _this.parents('tr').find('.entry-product-name').val("");
                        _this.parents('tr').find('.entry-product-id').val("");
                    }
                },
                error:function(error){
                    //hideLoader();
                    swal("Warning!", "Sorry! There is an error", "error");
                },
                complete:function(){
                    //hideLoader();
                }
            });
        // }else {
        //     swal("Warning!", "Sorry! You have already added this product", "error");
        // }
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
                if($(this).val()=="" && !$(this).hasClass('entry-product-name') && !$(this).hasClass('price') && !$(this).hasClass('qty')) {
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
                        saleOrderTable.draw();
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
    })
    $("body").on("click", "a.view-order-reject", function(e) {                  
        var obj = $(this);
        var id = obj.data("sale-order-id");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:base_url+"/sale_order/view-order-reject-form",  
            type: "POST",
            data:  {order_id: id},
            beforeSend:function(){
                showLoader();
            },  
            success:function(res){
                $('.modal-title').text('').text("Order Reject Details");
                $("#CommonModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#formContent").html(res);
                order_reject_form();
                hideLoader();
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
    function order_reject_form() {
        $("#CommonModal").find("#OrderRejectForm").validate({
            rules: {
                reason: "required",
            },
            submitHandler: function() {
                var formData = new FormData($('#OrderRejectForm')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:base_url+"/reject-sale-order",  
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
                            $('#OrderRejectForm')[0].reset();
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                $('#CommonModal').modal('hide');
                                saleOrderTable.draw();
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
    // Delete section
    $("body").on("click", "a.sales-order-delete", function(e) {                  
        var obj = $(this);
        var sale_order_id = obj.data("sale-order-id");
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
                    url:base_url+"/sale-order/delete-sale-order",  
                    type: "POST",
                    data:  {sale_order_id: sale_order_id},
                    beforeSend:function(){  
                    },  
                    success:function(res){
                        if(res["status"]) {
                            swal({
                                title: "Success!",
                                text: res["msg"],
                                type: "success"
                            }).then(function() {
                                saleOrderTable.draw();
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
    
    $("body").on("click", "a.delete-sale-order-details", function(e) {                  
        var obj = $(this);
        var id = obj.data("id");
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
                    url:base_url+"/sale-order/delete-sale-order-details",  
                    type: "POST",
                    data:  {id: id},
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
    $('body').on('click', '.download-order-template', function() {
        var template_name = $(this).data('template_name');
        window.open(base_url+"/public/backend/file/upload_order_csv/"+template_name);
    });
    // Download Invoice
    $('body').on('click', 'a.download-invoice', function() {
        var obj = $(this);
        var id = obj.data("sale-order-id");
        window.open(base_url+"/sale-order/download-invoice?id="+id, '_blank');
    });

    // Order Quantity update
    $('body').on('click',"a.order-quantity-update-form",function(){
      var id = $(this).data('sale_order_details_id');
      var current_stock = $(this).data('current_stock');
      var sl = $(this).data('sl');
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url:base_url+"/sale-order/order-quantity-update-form",  
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
                  url:base_url+"/sale-order/update-order-quantity",  
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
                              //currencyList.draw();
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
    $('body').on('click',"a.order-price-update-form",function(){
      var id = $(this).data('sale_order_details_id');
      var sl = $(this).data('sl');
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url:base_url+"/sale-order/order-price-update-form",  
          type: "POST",
          data:  {id:id},
          dataType:"json", 
          beforeSend:function(){  
              showLoader();
          },  
          success:function(res){
              hideLoader();
              if(res["status"]) {
                  $('#OrderPreviewModal .modal-title').text('').text("Update Price");
                  $("#OrderPreviewModal").modal({
                      backdrop: 'static',
                      keyboard: false
                  });
                  $("#OrderPreviewModal .modal-dialog").removeClass('modal-lg');
                  $("#OrderPreviewModal #formContent").html(res["message"]);
                  $('#OrderPreviewModal #formContent #sl').val(sl);
                  order_price_update_form();
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
    function order_price_update_form() {
      $("#OrderPreviewModal").find("#orderPriceUpdateForm").validate({
          rules: {
              product_price: "required"
          },
          submitHandler: function() {
            var formData = new FormData($('#orderPriceUpdateForm')[0]);
            var sl=$('#sl').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:base_url+"/sale-order/update-order-price",  
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
                        $('#orderPriceUpdateForm')[0].reset();
                        swal({title: "Success!", text: res["msg"], type: "success"}).then(function() {
                          $('#showPrice'+sl).html('').html(res["product_price"]);
                          $('#OrderPreviewModal').modal('hide');
                            //currencyList.draw();
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
    
$('body').on('click', 'a.print-order-details', function() {
    var obj = $(this);
    var sale_order_id = obj.data("sale_order_id");
    window.open(base_url+"/sale-order/print-order-details?sale_order_id="+sale_order_id, '_blank');
});