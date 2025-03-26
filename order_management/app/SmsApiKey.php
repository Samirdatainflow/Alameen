<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class SmsApiKey extends Model 
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sms_api_key';
    protected $primaryKey = 'id';
	protected $dateFormat = 'Y-m-d H:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_key', 'status'
    ];

    
}
