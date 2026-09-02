<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic User Account Rules
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'first_name'        => ['required', 'string', 'max:255'],
            'middle_name'       => ['nullable', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'suffix'            => ['nullable', 'string', 'max:50'],
            'phone_number'      => ['required', 'string', 'max:20'], 
            'birth_date'        => ['required', 'date'],
            'birth_place'       => ['nullable', 'string', 'max:255'],
            'gender'            => ['required', 'string', 'in:Male,Female,Other'],
            'civil_status'      => ['required', 'string'],
            'citizenship'       => ['nullable', 'string', 'max:100'],
            'occupation'        => ['nullable', 'string', 'max:100'],
            'house_number'      => ['nullable', 'string', 'max:50'],
            'street'            => ['nullable', 'string', 'max:100'],
            'purok_sitio'       => ['nullable', 'string', 'max:100'], 
            'voter_precinct_no' => ['nullable', 'string', 'max:100'],
            'id_type'           => ['nullable', 'string', 'max:255'],
            'id_number'         => ['nullable', 'string', 'max:255'],

            // Emergency Contact Rules (Bagong dagdag)
            'emergency_contact_name'     => ['nullable', 'string', 'max:255'],
            'emergency_contact_number'   => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Verification Document Rules 
            'id_front_path'           => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'id_back_path'            => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'proof_of_residency_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * Custom validation messages 
     */
    public function messages(): array
    {
        return [
            'id_front_path.image'           => 'The ID Front must be a valid image file (JPG or PNG).',
            'id_back_path.image'            => 'The ID Back must be a valid image file (JPG or PNG).',
            'proof_of_residency_path.mimes' => 'Proof of residency must be an image (JPG, PNG) or PDF document.',
            '*.max'                         => 'File size must not exceed 5MB.',
        ];
    }
}