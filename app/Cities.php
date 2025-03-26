<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Cities extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'cities';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
        'city_code', 'city_name','country_name'
    ];
}