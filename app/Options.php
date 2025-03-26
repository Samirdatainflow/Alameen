<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Options extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'options';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'option_id', 'option_name', 'option_value
    ];
}
