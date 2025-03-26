<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductSubCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'product_sub_category';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'sub_category_id','sub_category_name', 'category_id','created_at','updated_at','status'
    ];
}