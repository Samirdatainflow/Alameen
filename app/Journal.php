<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Journal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'journal';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'journal_id', 'user_id', 'function_code','existing_data','modified_date','journal_date'
    ];
}