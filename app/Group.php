<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'group';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'group_id', 'group_name', 'status'
    ];
}