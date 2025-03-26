<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ApplicationNo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'application_no';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'application_no_id', 'application_no', 'product_id','created_at','updated_at','status'
    ];
}