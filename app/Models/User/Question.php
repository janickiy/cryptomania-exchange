<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = ['user_id', 'title', 'content'];

    protected $fakeFields = ['title', 'content'];

    public function user(): mixed
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): mixed
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
