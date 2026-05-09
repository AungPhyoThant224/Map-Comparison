<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['business_type_id', 'name', 'phone', 'email', 'website', 'address', 'latitude', 'longitude', 'rating', 'review_count'];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'rating' => 'float',
        'review_count' => 'integer'
    ];

    public function businessType(): BelongsTo{
        return $this->belongsTo(BusinessType::class);
    }

    public function customerPhoto(): HasMany{
        return $this->hasMany(CustomerPhoto::class);
    }
}
