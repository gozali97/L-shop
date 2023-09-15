<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable=[
        'brand_name','short_des','description','photo','address','phone','email','brand_logo',
        'socmed_facebook', 'socmed_instagram', 'socmed_wa', 'theme',
    ];

    protected function socmedWa(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => substr($value, 2),
            set: fn ($value) => '62' . $value,
        );
    }

    protected function theme(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
}
