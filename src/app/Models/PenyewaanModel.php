<?php

namespace App\Models;

use App\Http\Controllers\PenyewaanController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyewaanModel extends Model
{
    use HasFactory;

    protected $table = 'penyewaan';
    protected $primaryKey = "penyewaan_id";
    public $timestamps = true;
    protected $fillable = [
        'penyewaan_pelanggan_id',
        'penyewaan_tglsewa',
        'penyewaan_tglkembali',
        'penyewaan_sttspembayaran',
        'penyewaan_stsskembali',
        'penyewaan_totalharga'
    ];

    public static function getPenyewaan(){
        $penyewaan = self::all();

        return $penyewaan;
    }

    public static function getPenyewaanById(int $penyewaan_id){
        $penyewaan = self::find($penyewaan_id);

        return $penyewaan;
    }

    public static function createPenyewaan($data){
        $penyewaan = self::create($data);

        return $penyewaan;
    }

    public static function updatePenyewaan(int $penyewaan_id, $data){
        $penyewaan = self::find($penyewaan_id);
        $penyewaan->update($data);

        return $penyewaan;
    }

    public static function deletePenyewaan(int $penyewaan_id){
        $penyewaan = self::find($penyewaan_id);
        $penyewaan->destroy($penyewaan_id);

        return $penyewaan;
    }

    public function pelanggan()
    {
        return $this->belongsTo(PelangganModel::class, 'penyewaan_pelanggan_id');
    }

    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetailModel::class, 'penyewaan_detail_penyewaan_id');
    }
}
