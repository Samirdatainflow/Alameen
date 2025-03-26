<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class PurchaseOrderReturnFiles extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'purchase_order_return_files';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_id', 'file_name', 'status'
    ];
}