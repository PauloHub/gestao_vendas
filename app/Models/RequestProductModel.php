<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RequestProductModel extends Model
{
    use HasFactory/*, SoftDeletes*/;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests_products';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'request_product_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_product_id',
        'request_id',
        'product_id',
        'amount',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getPriceAttribute($value)
    {
        if(empty($value)){
            return 0;
        }
        return number_format($value, 2, '.', '');
    }
}
