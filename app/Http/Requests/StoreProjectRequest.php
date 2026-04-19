<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'status_id' => 'required|exists:project_statuses,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'budget' => 'nullable|numeric|min:0',
            'contract_value' => 'nullable|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'payment_status' => 'nullable|string|in:pending,partial,paid',
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
            'client_name.required' => 'Nama klien wajib diisi.',
            'status_id.required' => 'Status proyek wajib dipilih.',
            'status_id.exists' => 'Status proyek yang dipilih tidak valid.',
            'deadline.after_or_equal' => 'Tanggal deadline harus setelah atau sama dengan tanggal mulai.',
            'institution_id.exists' => 'Instansi yang dipilih tidak valid.',
        ];
    }
}
