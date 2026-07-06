<?php

namespace App\Repositories\User\Interfaces;

interface CommentInterface
{
    /**
     * Purpose: describes the save contract for CommentInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function save(array $attribute, \Illuminate\Database\Eloquent\Model $commentable);
}
