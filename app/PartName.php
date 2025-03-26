<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class PartName extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'part_name';
	protected $dateFormat = 'Y-m-d H:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'part_name', 'status'
    ];
}