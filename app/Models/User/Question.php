<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = ['user_id', 'title', 'content'];

    protected $fakeFields = ['title', 'content'];

    /**
     * Purpose: defines a model relation or computed value through user.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
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
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
