<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductTaxes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_product_taxes';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
       'tax_name', 'tax_rate', 'tax_type', 'tax_description', 'warehouse_id','status'
    ];
}
