<?php

namespace App\Models\Backend;

use App\Models\User\Comment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'title', 'content', 'featured_image', 'is_published'];

    protected $fakeFields = ['title', 'content', 'featured_image', 'is_published'];

    /**
     * Purpose: defines a model relation or computed value through user.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Purpose: defines a model relation or computed value through comments.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
