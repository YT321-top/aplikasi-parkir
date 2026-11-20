<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Str;

class LogAktivitasService
{
    public static function record(?int $idUser, string $aktivitas): void
    {
        if (!$idUser) {
            return;
        }

        LogAktivitas::create([
            'id_user' => $idUser,
            'aktivitas' => Str::limit(trim($aktivitas), 100, ''),
            'waktu_aktivitas' => now(),
        ]);
    }
}
