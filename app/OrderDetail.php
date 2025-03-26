<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_detail';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'order_detail_id', 'order_id', 'warehouse_id', 'price_id', 'product_id', 'qty'
    ];
}
