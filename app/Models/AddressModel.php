<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    use HasFactory/*, SoftDeletes*/;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adresses';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'address_id';

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
        'address_id',
        'client_id',
        'city_id',
        'zip_code',
        'street',
        'district',
        'complement',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
