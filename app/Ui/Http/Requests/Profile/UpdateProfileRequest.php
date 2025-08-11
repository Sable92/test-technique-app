<?php

declare(strict_types=1);

namespace App\Ui\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la validation des données de mise à jour de profil
 */
class UpdateProfileRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['sometimes', 'required', Rule::in(['inactive', 'pending', 'active'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
            'first_name.max' => 'Le prénom ne peut pas dépasser 255 caractères.',

            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.string' => 'Le nom doit être une chaîne de caractères.',
            'last_name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou GIF.',
            'image.max' => 'L\'image ne peut pas dépasser 2MB.',

            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être inactive, pending ou active.',
        ];
    }

}
