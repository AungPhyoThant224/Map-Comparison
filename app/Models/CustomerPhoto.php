<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPhoto extends Model
{
    protected $fillable = ['customer_id', 'photo_path', 'is_primary'];

    public function customer():BelongsTo{
        return $this->belongsTo(Customer::class);
    }
}
