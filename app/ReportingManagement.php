<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ReportingManagement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'reporting_management';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'reporting_manager_id', 'reporting_id'
    ];
}
