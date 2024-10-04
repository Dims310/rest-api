<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'total_amount',
        'status',
        'user_id',
        'service_id',
        'created_at',
        'updated_at'
    ];

    public function service() {
        return $this->belongsTo(Service::class);
    }
}
