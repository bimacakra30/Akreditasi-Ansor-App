<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KtaPhoto extends Model
{
    protected $fillable = ['akreditasi_id','path'];

    public function akreditasi() {
        return $this->belongsTo(Akreditasi::class);
    }
}
