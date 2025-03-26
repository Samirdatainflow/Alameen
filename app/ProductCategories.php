<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductCategories extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'product_categories';
	protected $dateFormat = 'Y-m-d h:i:s';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
       'category_id', 'category_name', 'category_description', 'warehouse_id', 'status','created_at', 'updated_at'
    ];
}
