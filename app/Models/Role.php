<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Starmoozie\CRUD\app\Models\Traits\CrudTrait;

use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Starmoozie\LaravelMenuPermission\app\Models\MenuPermission;

class Role extends Model
{
    use CrudTrait, HasJsonRelationships;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table    = 'role';
    protected $guarded  = ['id'];
    protected $fillable = [
        'id',
        'name',
        'options',
        'details'
    ];
    protected $casts    = [
        'details' => 'array',
        'options' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();
        Self::creating(function ($model) {
            $unique         = filter_var(microtime(true), FILTER_SANITIZE_NUMBER_INT);
            $model->id      = substr($unique, 0, 14);
            $model->options = JSON_DECODE(request()->menuPermission ?: JSON_ENCODE([]));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function menuPermission()
    {
        return $this->belongsToJson(MenuPermission::class, 'options');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getCityAttribute()
    {
        return is_array($this->details) && isset($this->details['city']) ? $this->details['city'] : NULL;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
