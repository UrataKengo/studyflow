<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLog extends Model
{
    protected $fillable = [
        'card_id',
        'result',
        'studied_at',
    ];

    protected $casts = [
        'studied_at' => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}