<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsSaleOrderAproved extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_sale_order_approved';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_approved_id', 'order_id', 'date_approve','warehouse_id','product_id','qty','qty_appr','agent_id'
    ];
}