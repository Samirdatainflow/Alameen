<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Countries extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'countries';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $fillable = [
       'country_id', 'country_code', 'country_name'
    ];
}