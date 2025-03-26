<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

class MailApiKey extends Model 
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mail_api_key';
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
        'api_key', 'smtp_user', 'smtp_pass', 'smtp_port', 'from_mail', 'from_name', 'status'
    ];

    
}
