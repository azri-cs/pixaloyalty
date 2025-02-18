<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BrandUser extends Pivot
{
    protected $table = 'brand_user';

    protected $fillable = [
        'brand_id',
        'user_id',
    ];
}
