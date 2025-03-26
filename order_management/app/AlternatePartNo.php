<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class AlternatePartNo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'alternate_part_no';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'alternate_part_no_id', 'alternate_no', 'product_id','created_at','updated_at','status'
    ];
}