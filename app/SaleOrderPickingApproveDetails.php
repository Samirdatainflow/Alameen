<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class SaleOrderPickingApproveDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sale_order_picking_approve_details';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_id', 'product_id', 'quantity', 'location_id', 'zone_id', 'row_id', 'rack_id', 'plate_id', 'place_id'
    ];
}
