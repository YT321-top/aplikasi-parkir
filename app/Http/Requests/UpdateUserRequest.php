<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => trim((string) $this->input('username')),
            ]);
        }
        if ($this->has('nama_lengkap')) {
            $this->merge([
                'nama_lengkap' => trim((string) $this->input('nama_lengkap')),
            ]);
        }
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id_user : $user;

        return [
            'nama_lengkap' => ['required', 'string', 'max:50'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tb_user', 'username')->ignore($userId, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'max:100'],
            'role' => ['required', 'in:admin,petugas,owner'],
            'status_aktif' => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 50 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'status_aktif.required' => 'Status aktif wajib dipilih.',
            'status_aktif.in' => 'Status aktif tidak valid.',
        ];
    }
}
