<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class DeliveryDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'delivery_detail';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'delivery_detail_id', 'delivery_id', 'product_id', 'qty', 'volume', 'poids','warehouse_id','inventory_id'
    ];
}
