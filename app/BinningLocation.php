<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class BinningLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'binning_location';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'order_id', 'status'
    ];
}