<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ManufacturingNo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'manufacturing_no';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'manufacturing_no_id', 'manufacturing_no', 'product_id','created_at','updated_at','status'
    ];
}