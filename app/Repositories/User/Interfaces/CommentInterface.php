<?php

namespace App\Repositories\User\Interfaces;

use App\Models\User\Comment;
use Illuminate\Database\Eloquent\Model;

interface CommentInterface
{
    /**
     * Purpose: describes the save contract for CommentInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function save(array $attribute, Model $commentable): Comment;
}
