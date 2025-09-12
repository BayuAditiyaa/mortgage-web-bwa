<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class House extends Model
{
    //

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     */

    protected $fillable = ['name', 'slug', 'thumbnail', 'certificate', 'about', 'price', 'bedroom', 'bathroom', 'electric', 'land_area', 'building_area', 'category_id', 'city_id'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function photos()
    {
        return $this->hasMany(HousePhoto::class);
    }

    public function interests()
    {
        return $this->hasMany(Interest::class);
    }


    public function facilities()
    {
        return $this->hasMany(HouseFacility::class, 'house_id');
    }

    public function MortgageRequests()
    {
        return $this->hasMany(MortgageRequest::class);
    }
}
