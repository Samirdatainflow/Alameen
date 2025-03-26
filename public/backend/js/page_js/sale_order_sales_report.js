var SalesReportTable = $('#SalesReportTable').DataTable({
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
        "url": base_url+"/list-sale-order-sales-report",
        "type": "POST",
        'data': function(data){
          data.filter_customer=$("#filter_customer").val();
        },
        
    },
    'columns': [
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'outstanding_amount', name: 'outstanding_amount', orderable: false, searchable: false},
        {data: 'partial_amount', name: 'partial_amount', orderable: false, searchable: false},
        {data: 'receipt_amount', name: 'receipt_amount', orderable: false, searchable: false},
    ]
   
}).on('xhr.dt', function(e, settings, json, xhr) {

});

$("#filter_customer").on('change',function(){
    SalesReportTable.draw();
});

$('body').on('click', 'a.rest-filter', function() {
    $('#filter_customer').val('');
    SalesReportTable.draw();
});