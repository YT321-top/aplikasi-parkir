<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaParkir extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tb_area_parkir';

    protected $primaryKey = 'id_area';

    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'terisi' => 'integer',
    ];

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    public function getSisaSlotAttribute(): int
    {
        return max(0, $this->kapasitas - $this->terisi);
    }
}
