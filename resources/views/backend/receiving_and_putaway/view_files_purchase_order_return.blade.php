<div class="row">
	@php
	if(!empty($PurchaseOrderReturnFiles)) {
		foreach($PurchaseOrderReturnFiles as $data) {
	@endphp
	<div class="col-md-12" id="fileId{{$data['purchase_order_return_files_id']}}">
		@php
        if(!empty($data['file_name'])) {
            $file_extention = substr($data['file_name'], strrpos($data['file_name'], '.' )+1);
            $url = url('public/backend/images/purchase_order_return/')."/".$data['file_name'];
    		if($file_extention == "pdf") {
    		@endphp
    		<iframe src="{{$url}}" style="width:100%; height:500px;" frameborder="0"></iframe>
			<p>
				<a href="javascript:void(0)" onclick="DeleteFile({{$data['purchase_order_return_files_id']}}, '{{$data['file_name']}}')"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>
			</p>
    		@php
    		}else {
    		@endphp
    		<img src="{{$url}}" style="width:100%">
			<br><br>
			<p>
				<a href="javascript:void(0)" onclick="DeleteFile({{$data['purchase_order_return_files_id']}}, '{{$data['file_name']}}')"><button type="button" class="btn btn-danger btn-sm" title="Remove"><i class="fa fa-trash"></i></button></a>
			</p>
    		@php
    		}
        }
        @endphp
	</div>
	<p>&nbsp;</p>
	@php
		}
	}
	@endphp
</div>
<p>&nbsp;</p>
<div class="row">
	<div class="col-md-12">
		<p class="text-right">
            <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
        </p>
	</div>
</div>