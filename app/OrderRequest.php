<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderRequest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'order_request';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_request_id', 'supplier_id', 'status','created_at','updated_at'
    ];
}