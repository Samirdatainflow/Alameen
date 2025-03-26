    $('.datepicker').datepicker({
        format  :  'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
    }).change(function(){
        //$(this).valid()
    });
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
            "url": base_url+"/get-no-stock-order",
            "type": "POST",
            'data': function(data){
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
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
       
    }).on('xhr.dt', function(e, settings, json, xhr) {
       
    });
    
    $("#filter_customer").on('change',function()
    {
        saleOrderTable.draw();
        //chnageDateFilter();
    });
    $("#filter_from_date").on('change',function()
    {
        saleOrderTable.draw();
    });
    $("#filter_to_date").on('change',function()
    {
        saleOrderTable.draw();
    });
    $('body').on('click', 'a.rest-filter', function()
    {
        $('#filter_customer').val('');
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        saleOrderTable.draw();
    });
    function chnageDateFilter() {
    
        $('#filter_to_date').datepicker('setDate', 'now');
        var startDate = $('#filter_to_date').datepicker('getDate');
        
        startDate.setTime(startDate.getTime() - (1000*60*60*24*5));
        $('#filter_from_date').datepicker("setDate", startDate);
    }
    $('div.toolbar').html('<button type="button" aria-haspopup="true" id="add_brand" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportLossOfSaleReport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>LOSS OF SALE-Report</button> <button type="button" aria-haspopup="true" id="add_brand" aria-expanded="false" class="btn-shadow btn btn-info" onclick="ExportTable()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');
    function ExportTable() {
        var filter_customer = $('#filter_customer').val();
        var filter_from_date = $('#filter_from_date').val();
        var filter_to_date = $('#filter_to_date').val();
        window.location.href = base_url+"/no-stock-order-export?filter_customer="+filter_customer+"&filter_from_date="+filter_from_date+"&filter_to_date="+filter_to_date;
    }
    function ExportLossOfSaleReport() {
        window.location.href = base_url+"/loss-of-sales-report-export";
    }
    $("#sale_order").on('click','.view-order-details',function(){
        var sale_order_id= $(this).data('sale-order-id');
        var ordersatatus= $(this).data('ordersatatus');
        var reference_text = "";
        // var reference_id= $(this).data('reference_id');
        // if(reference_id !== "") {
        //     reference_text = "( Reference order ID is #"+reference_id+" )";
        // }
        $.ajax({
            url:base_url+"/get-no-stock-sale-order-details",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            data:{sale_order_id:sale_order_id, ordersatatus:ordersatatus},
            beforeSend:function(){
                showLoader();
            },
            success:function(res){
                $('.modal-title').text('').text("Order Details (#"+sale_order_id+") ");
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

    // No stock create order
    $("body").on("click",'#CreateOrder',function(){
        var sale_order_id = $('#sale_order_id').val();
        var table_tr=$('table#orderDetails tbody tr');

        var x=0;
        var pmpno = "";

        $(table_tr).each(function(e){

            pmpno = table_tr.find('.pmpno'+e).val();
            var qty = table_tr.find('.qty'+e).val();
            var current_stock = table_tr.find('.current_stock'+e).val();

            if(qty > current_stock || current_stock < 1)
            {
                x=1;
                swal("Warning!", "This part no " + pmpno + " stock not available.", "error");
                return false;
            }
        });
        if(x==0) {
            swal({
                title: "Are you sure?",
                text: "You want to create this order.",
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
                        url:base_url+"/sale-order/create-no-stock-order",  
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
                                    $("#CommonModal").modal('hide');
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
                            //
                        }
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swal("Cancelled", "Data is Not Reject :)", "error")
                }
            })
        }
    });

    
    
