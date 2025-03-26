<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductRates extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'product_rates';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'rate_id', 'default_rate', 'level_1', 'level_2', 'level_3', 'level_4','level_5','store_id','product_id'
    ];
}
