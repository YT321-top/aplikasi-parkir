<?php

namespace App\Http\Requests;

use App\Models\Kendaraan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKendaraanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merged = [];

        if ($this->has('plat_nomor')) {
            $plat = strtoupper(trim((string) $this->input('plat_nomor')));
            $merged['plat_nomor'] = preg_replace('/\s+/', ' ', $plat);
        }
        if ($this->has('warna')) {
            $merged['warna'] = trim((string) $this->input('warna'));
        }
        if ($this->has('pemilik')) {
            $merged['pemilik'] = trim((string) $this->input('pemilik'));
        }

        if (!empty($merged)) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        $kendaraan = $this->route('kendaraan');
        $kendaraanId = $kendaraan instanceof Kendaraan ? $kendaraan->id_kendaraan : $kendaraan;

        return [
            'plat_nomor' => [
                'required',
                'string',
                'max:15',
                Rule::unique('tb_kendaraan', 'plat_nomor')->ignore($kendaraanId, 'id_kendaraan'),
            ],
            'jenis_kendaraan' => ['required', 'in:motor,mobil,lainnya'],
            'warna' => ['required', 'string', 'max:20'],
            'pemilik' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'plat_nomor.required' => 'Plat nomor wajib diisi.',
            'plat_nomor.max' => 'Plat nomor maksimal 15 karakter.',
            'plat_nomor.unique' => 'Plat nomor sudah terdaftar.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid.',
            'warna.required' => 'Warna kendaraan wajib diisi.',
            'warna.max' => 'Warna maksimal 20 karakter.',
            'pemilik.required' => 'Nama pemilik wajib diisi.',
            'pemilik.max' => 'Nama pemilik maksimal 100 karakter.',
        ];
    }
}
