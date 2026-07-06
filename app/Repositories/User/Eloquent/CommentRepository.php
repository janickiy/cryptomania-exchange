<?php

namespace App\Repositories\User\Eloquent;

use App\Models\User\Comment;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\CommentInterface;
use Illuminate\Database\Eloquent\Model;

class CommentRepository extends BaseRepository implements CommentInterface
{
    /**
    * @var Comment
    */
     protected $model;

     /**
      * Purpose: initializes the CommentRepository instance.
      *
      * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
      *
      */
     public function __construct(Comment $comment)
     {
        $this->model = $comment;
     }

    /**
     * Purpose: performs the save operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $attribute
     * @param $commentable
     * @return mixed
     */
    public function save(array $attribute, Model $commentable): Comment
     {
         return $commentable->comments()->create($attribute);
     }
}
