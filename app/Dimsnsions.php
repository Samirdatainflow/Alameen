<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class Dimsnsions extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'dimsnsions';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'id', 'product_id', 'long_pr', 'larg', 'haut', 'poids'
    ];
}
