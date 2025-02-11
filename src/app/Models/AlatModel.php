<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatModel extends Model
{
    use HasFactory;

    protected $table = 'alat';
    protected $primaryKey = 'alat_id';
    public $timestamps = true;
    protected $fillable = [
        'alat_kategori_id',
        'alat_nama',
        'alat_deskripsi',
        'alat_hargaperhari',
        'alat_stok'
    ];

    public static function getAlat()
    {
        $alat = self::all();

        return $alat;
    }

    public static function getAlatById(int $alat_id){
        $alat = self::find($alat_id);

        return $alat;
    }

    public static function createAlat($data){
        $alat = self::create($data);

        return $alat;
    }

    public static function updateAlat($data, int $alat_id){
        $alat = self::find($alat_id);
        $alat->update($data);

        return $alat;
    }

    public static function deleteAlat(int $alat_id){
        $alat = self::find($alat_id);
        $alat->destroy($alat_id);

        return $alat;
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'alat_kategori_id', 'kategori_id');
    }

    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetailModel::class, 'penyewaan_detail_alat_id', 'alat_id');
    }
}
