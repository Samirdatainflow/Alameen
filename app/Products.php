<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Products extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'products';
	protected $dateFormat = 'Y-m-d h:i:s';
    //public $timestamps = false;
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'product_id  ', 'pno_created_date', 'gr', 'ct', 'brn', 'pmpno','application','manfg_no','altn_part','part_name','pmrprc','mark_up','lc_price','lc_date','prvious_lc_price','prvious_lc_date','moq','country_of_origin','supplier_currency','re_order_level','no_re_order','stop_sale','wh_location1','wh_location2','wh_location3','reserved_qty','allocation_qty','last_month_stock','current_stock','qty_in_transit','qty_on_order','alert'
    ];
}
