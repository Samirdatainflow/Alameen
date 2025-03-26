<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsProductTaxes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_product_taxes';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = '';
    const UPDATED_AT = '';
    protected $fillable = [
       'tax_id', 'tax_name', 'tax_rate','tax_type','tax_description','store_id'
    ];
}