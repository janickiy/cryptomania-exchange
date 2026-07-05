<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = ['user_id', 'commentable_id', 'content', 'commentable_type'];

    protected $fakeFields = ['commentable_id', 'content', 'commentable_type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
