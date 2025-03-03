<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reversal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['transaction_id', 'reason'];
    protected $dates = ['deleted_at'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
