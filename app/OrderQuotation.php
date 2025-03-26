<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderQuotation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_quotation';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_quotation_id', 'order_request_id', 'quotation','is_confirm','status','created_at','updated_at'
    ];
}