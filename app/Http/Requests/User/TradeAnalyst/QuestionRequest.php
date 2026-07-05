<?php

namespace App\Http\Requests\User\TradeAnalyst;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class QuestionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'required',
        ];

    }
}
