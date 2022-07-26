<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Starmoozie\CRUD\app\Models\Traits\CrudTrait;
    use \Starmoozie\LaravelMenuPermission\app\Traits\GenerateId;
    use \App\Traits\UploadPhoto;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role_id',
        'details'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'details'           => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function role()
    {
        return $this->belongsTo(
            \Starmoozie\LaravelMenuPermission\app\Models\Role::class,
            'role_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getAddressAttribute()
    {
        return is_array($this->details) && isset($this->details['address']) ? $this->details['address'] : NULL;
    }

    public function getPhotoAttribute()
    {
        return is_array($this->details) && isset($this->details['photo']) ? $this->details['photo'] : NULL;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setDetailsAttribute($value)
    {
        $value['photo'] = $this->uploadPhoto($value['photo'], 'photo', 'public/uploads/profile');

        $this->attributes['details'] = JSON_ENCODE($value);
    }
}
