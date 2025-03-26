<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ShippingAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'shipping_address';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'client_id','address','status'
    ];
}