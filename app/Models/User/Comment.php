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
     * Purpose: defines a model relation or computed value through commentable.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return MorphTo
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
