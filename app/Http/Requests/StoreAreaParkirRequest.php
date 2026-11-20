<?php

namespace App\Http\Requests;

use App\Models\AreaParkir;
use Illuminate\Foundation\Http\FormRequest;

class StoreAreaParkirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nama_area')) {
            $this->merge([
                'nama_area' => trim((string) $this->input('nama_area')),
            ]);
        }
    }

    public function rules(): array
    {
        $area = $this->route('area_parkir');
        $minKapasitas = 1;

        if ($area instanceof AreaParkir) {
            $minKapasitas = max(1, (int) $area->terisi);
        }

        return [
            'nama_area' => ['required', 'string', 'max:50'],
            'kapasitas' => ['required', 'integer', 'min:' . $minKapasitas, 'max:100000'],
        ];
    }

    public function messages(): array
    {
        $area = $this->route('area_parkir');
        $terisi = $area instanceof AreaParkir ? (int) $area->terisi : 0;

        return [
            'nama_area.required' => 'Nama area wajib diisi.',
            'nama_area.max' => 'Nama area maksimal 50 karakter.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka bulat.',
            'kapasitas.min' => $terisi > 0
                ? "Kapasitas tidak boleh kurang dari jumlah kendaraan terisi ({$terisi})."
                : 'Kapasitas minimal 1 slot.',
        ];
    }
}
