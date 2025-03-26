<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class UserLevel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'user_level';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'level_id', 'level_name', 'level_description', 'level_page'
    ];
}
