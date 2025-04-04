<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class AutoFillBinningLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'auto_fill_binning_location';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'auto_fill_binning_location_id', 'count_no', 'created_at','updated_at'
    ];
}