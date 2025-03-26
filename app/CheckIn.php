<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class CheckIn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'check_in';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_id', 'product_id', 'quantity', 'good_quantity', 'bad_quantity', 'status'
    ];
}