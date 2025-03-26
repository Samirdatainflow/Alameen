<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class UserMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'user_meta';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'user_meta_id', 'user_id', 'message_email', 'last_login_time', 'last_login_ip', 'login_attempt','login_lock'
    ];
}
