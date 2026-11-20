<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_kendaraan' => ['required', 'in:motor,mobil,lainnya'],
            'tarif_per_jam' => ['required', 'integer', 'min:0', 'max:9999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid.',
            'tarif_per_jam.required' => 'Tarif per jam wajib diisi.',
            'tarif_per_jam.integer' => 'Tarif per jam harus berupa angka bulat.',
            'tarif_per_jam.min' => 'Tarif per jam minimal 0.',
        ];
    }
}
