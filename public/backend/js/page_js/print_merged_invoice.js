var saleOrderTable = $('#PrintInvoice').DataTable({
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
        "url": base_url+"/print-merged-invoice-list",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'invoice_no', name: 'invoice_no', orderable: true, searchable: false},
        {data: 'client_name', name: 'client_name', orderable: true, searchable: true},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});

$("#filter_customer").on('change',function(){
    saleOrderTable.draw();
});

$('body').on('click', 'a.rest-filter', function() {
    $('#filter_customer').val('');
    saleOrderTable.draw();
});

$('body').on('click', 'a.download-merged-invoice', function() {
    var obj = $(this);
    var invoice_no = obj.data("invoice_no");
    var client_id = obj.data("client_id");
    window.open(base_url+"/sale-order/download-merged-invoice?invoice_no="+invoice_no+'&client_id='+client_id, '_blank');
});
$(window).bind("load", function() {
   $('#filter_customer').val('');
});