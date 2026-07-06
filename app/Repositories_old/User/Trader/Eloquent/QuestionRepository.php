<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Repositories\BaseRepository;

class QuestionRepository extends BaseRepository implements QuestionInterface
{
    /**
     * @var Question
     */
    protected $model;

    /**
     * Purpose: initializes the QuestionRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param Question $question
     */
    public function __construct(Question $question)
    {
        $this->model = $question;
    }
}