<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class SaleOrderTemplate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sale_order_template';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'sale_order_id', 'template_name', 'created_at','updated_at'
    ];
}