<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'filename', 
        'original_name', 
        'path', 
        'mime_type',
        'extracted_text', 
        'summary', 
        'keywords',
        'sentiment', 
        'sentiment_score', 
        'linkedin_post', 
        'status'
    ];

    protected $casts = [
        'keywords' => 'array',
    ];
}
