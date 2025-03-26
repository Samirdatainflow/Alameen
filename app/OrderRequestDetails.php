<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderRequestDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_request_details';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_request_details_id', 'order_request_id', 'product_id','status','created_at','updated_at'
    ];
}