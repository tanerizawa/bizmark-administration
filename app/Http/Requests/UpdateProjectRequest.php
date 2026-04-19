<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institution_id' => 'required|exists:institutions,id',
            'status_id' => 'required|exists:project_statuses,id',
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'completed_at' => 'nullable|date',
            'completion_notes' => 'nullable|string',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'budget' => 'nullable|numeric|min:0',
            'contract_value' => 'nullable|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama proyek wajib diisi.',
            'client_id.required' => 'Klien wajib dipilih.',
            'client_id.exists' => 'Klien yang dipilih tidak valid.',
            'permit_type.required' => 'Jenis izin wajib dipilih.',
            'permit_type.in' => 'Jenis izin yang dipilih tidak valid.',
            'target_completion_date.after_or_equal' => 'Tanggal target penyelesaian harus setelah atau sama dengan tanggal kontrak.',
            'current_status_id.required' => 'Status proyek wajib dipilih.',
            'current_status_id.exists' => 'Status proyek yang dipilih tidak valid.',
        ];
    }
}
