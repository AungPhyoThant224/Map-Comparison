<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessType extends Model
{
    protected $fillable = ['name', 'marker_icon'];

    public function customers(): HasMany {
        return $this->hasMany(Customer::class);
    }
}
