<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class DefectiveBin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'defective_bin';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        'order_id', 'bin_status'
    ];
}