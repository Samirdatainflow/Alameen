<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class InvoiceDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'invoice_details';
	protected $dateFormat = 'Y-m-d h:i:s';
    public $timestamps = false;
	// const CREATED_AT = 'created_at';
 //    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'invoice_id', 'sale_order_id', 'product_id', 'product_tax', 'discount', 'product_price','qty'
    ];
}
