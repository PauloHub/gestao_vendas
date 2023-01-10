<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    use HasFactory/*, SoftDeletes*/;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'city_id';

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
        'city_id',
        'state_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
