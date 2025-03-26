<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class WmsProductGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wms_product_group';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'product_group_id', 'product_group', 'product_group_description','created_date','updated_date'
    ];
}