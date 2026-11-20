<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiMasukRequest extends FormRequest
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
        $mode = $this->input('mode_kendaraan', 'terdaftar');

        $rules = [
            'mode_kendaraan' => ['required', 'in:terdaftar,baru'],
            'id_area' => ['required', 'integer', 'exists:tb_area_parkir,id_area'],
            'id_tarif' => ['required', 'integer', 'exists:tb_tarif,id_tarif'],
        ];

        if ($mode === 'baru') {
            $rules['plat_nomor'] = ['required', 'string', 'max:15'];
            $rules['jenis_kendaraan'] = ['required', 'in:motor,mobil,lainnya'];
            $rules['warna'] = ['required', 'string', 'max:20'];
            $rules['pemilik'] = ['required', 'string', 'max:100'];
        } else {
            $rules['id_kendaraan'] = ['required', 'integer', 'exists:tb_kendaraan,id_kendaraan'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'id_kendaraan.required' => 'Pilih kendaraan terlebih dahulu.',
            'id_kendaraan.exists' => 'Kendaraan tidak ditemukan.',
            'plat_nomor.required' => 'Plat nomor wajib diisi.',
            'plat_nomor.max' => 'Plat nomor maksimal 15 karakter.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid.',
            'warna.required' => 'Warna kendaraan wajib diisi.',
            'pemilik.required' => 'Nama pemilik wajib diisi.',
            'id_area.required' => 'Area parkir wajib dipilih.',
            'id_area.exists' => 'Area parkir tidak ditemukan.',
            'id_tarif.required' => 'Tarif parkir wajib dipilih.',
            'id_tarif.exists' => 'Tarif parkir tidak ditemukan.',
        ];
    }
}
