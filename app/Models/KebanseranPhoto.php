<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KebanseranPhoto extends Model
{
    protected $fillable = ['akreditasi_id','path'];

    public function akreditasi(): BelongsTo {
        return $this->belongsTo(Akreditasi::class);
    }
}
