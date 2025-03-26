<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Price extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'price';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'price_id', 'cost', 'selling_price', 'tax', 'warehouse_id', 'product_id','type'
    ];
}
