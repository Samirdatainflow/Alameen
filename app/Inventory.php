<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Inventory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'inventory';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'inventory_id', 'dateinventory', 'inn', 'out_inv', 'product_id', 'warehouse_id','transfer_id','delivery_id','order_id','lot','return_id'
    ];
}
