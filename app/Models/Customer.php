<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',  // Other fillable attributes
        'email',
        'points',  // Add 'points' here
    ];


    public function loyaltyPoint(): HasMany
{
    return $this->hasMany(LoyaltyPoint::class);
}


    
}



