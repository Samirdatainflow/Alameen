<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class DeliveryMethod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'delivery_method';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
       'delivery_method_id', 'delivery_method', 'delivery_description','warehouseid'
    ];
}