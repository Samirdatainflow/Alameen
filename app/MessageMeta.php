<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class MessageMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'message_meta';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'msg_meta_id', 'message_id', 'status', 'from_id', 'to_id', 'subject_id'
    ];
}
