<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class SaleOrderDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sale_order_details';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	// const CREATED_AT = 'created_at';
 //    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'sale_order_details_id', 'sale_order_id', 'order_line_no', 'product_id', 'product_tax', 'product_tax','qty'
    ];
}
