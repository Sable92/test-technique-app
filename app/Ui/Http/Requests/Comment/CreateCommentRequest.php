<?php

declare(strict_types=1);

namespace App\Ui\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


/**
 * FormRequest pour la validation des données de commentaire
 */
class CreateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; //Authorisation gérée par le middleware auth:sanctum
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|string>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|min:10|max:1000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu du commentaire est obligatoire.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'content.min' => 'Le commentaire doit contenir au moins 10 caractères.',
            'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }
}
