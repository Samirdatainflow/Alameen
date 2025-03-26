<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderRequestQuotationPrices extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_request_quotation_prices';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_request_unique_id', 'supplier_id', 'product_id', 'price', 'status'
    ];
}