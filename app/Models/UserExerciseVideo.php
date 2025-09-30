<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Trainer\Exercise;

class UserExerciseVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'video_url',
        'title',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    // Скоупы
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForExercise($query, $exerciseId)
    {
        return $query->where('exercise_id', $exerciseId);
    }

    // Методы для работы с YouTube
    public function isYouTubeUrl()
    {
        if (!$this->video_url) return false;
        return str_contains($this->video_url, 'youtube.com') || str_contains($this->video_url, 'youtu.be');
    }

    public function getYouTubeEmbedUrl()
    {
        if (!$this->video_url) return '';
        
        $videoId = '';
        
        // youtube.com/watch?v=VIDEO_ID
        if (str_contains($this->video_url, 'youtube.com/watch?v=')) {
            $videoId = explode('v=', $this->video_url)[1];
            $videoId = explode('&', $videoId)[0];
        }
        // youtu.be/VIDEO_ID
        elseif (str_contains($this->video_url, 'youtu.be/')) {
            $videoId = explode('youtu.be/', $this->video_url)[1];
            $videoId = explode('?', $videoId)[0];
        }
        // youtube.com/embed/VIDEO_ID
        elseif (str_contains($this->video_url, 'youtube.com/embed/')) {
            $videoId = explode('embed/', $this->video_url)[1];
            $videoId = explode('?', $videoId)[0];
        }
        
        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : '';
    }
}
