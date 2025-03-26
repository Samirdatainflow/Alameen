@php
if(!empty($ClientDocuments))
{
    foreach($ClientDocuments as $document)
    {
        $extension = pathinfo($document['document_name'], PATHINFO_EXTENSION);
        $url = url('public/backend/images/customer_file/');
        $customer_file_path = $url."/".$document['document_name'];
        $csvIcon = url('public/backend/images/csv-icon.jpg');
        $excelIcon = url('public/backend/images/excel-icon.png');
        $docIcon = url('public/backend/images/doc-icon.png');
        if($extension == "pdf")
        {
        @endphp
            <div class="row">
                <iframe src="{{$customer_file_path}}" style="width:100%; height:500px;" frameborder="0"></iframe>
            </div>
        @php
        }
        else if($extension == "png" || $extension == "jpg" || $extension == "jpeg"){
        @endphp
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="{{$customer_file_path}}" style="width:200px; height:200px">
                </div>
            </div>
        @php
        }
        else if($extension == "csv")
        {
        @endphp
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href={{$customer_file_path}} target="_blank">
                        <img src="{{$csvIcon}}" alt="{{$customer_file_path}}" style="width:200px; height:200px">
                    </a>
                </div>
            </div>
        @php
        }
        else if($extension == "xlsx" || $extension == "xls")
        {
        @endphp
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href={{$customer_file_path}} target="_blank">
                        <img src="{{$excelIcon}}" alt="{{$customer_file_path}}" style="width:200px; height:200px">
                    </a>
                </div>
            </div>
        @php
        }
        else if($extension == "docx" || $extension == "doc")
        {
        @endphp
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href={{$customer_file_path}} target="_blank">
                        <img src="{{$docIcon}}" alt="{{$customer_file_path}}" style="width:200px; height:200px">
                    </a>
                </div>
            </div>
        @php
        }
    }
}
@endphp
<div class="row">
    <p>&nbsp;</p>
    <div class="col-md-12">
        <p class="text-right">
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
    </div>
</div>