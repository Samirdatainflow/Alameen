<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class MailApiKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'mail_api_key';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'smtp_user','smtp_pass','smtp_port','api_key','from_mail','from_name', 'status'
    ];
}