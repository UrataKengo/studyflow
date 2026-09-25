<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'question',
        'answer',
        'question_image',
        'answer_image',
        'next_review_date',
        'level',
        'review_count',
        'study_count',
        'difficulty',
        'stability',
        'lapses',
        'last_reviewed_at',
        'status',
        'review_at',
    ];

    protected $casts = [
        'next_review_date' => 'date',
        'review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',

        'level' => 'integer',
        'review_count' => 'integer',
        'study_count' => 'integer',
        'lapses' => 'integer',

        'difficulty' => 'float',
        'stability' => 'float',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function studyLogs()
    {
        return $this->hasMany(StudyLog::class);
    }
}