<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsProductBrand extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_product_brand';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'product_brand_id', 'product_brand', 'product_brand_description','created_date','updated_date'
    ];
}