<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class SmsApiKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sms_api_key';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'api_key', 'status'
    ];
}