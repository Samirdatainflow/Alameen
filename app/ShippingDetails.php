<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ShippingDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'shipping_details';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'sale_order_id', 'product_id', 'price', 'quantity'
    ];
}