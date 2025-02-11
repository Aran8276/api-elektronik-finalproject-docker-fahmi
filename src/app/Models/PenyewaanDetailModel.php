<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyewaanDetailModel extends Model
{
    use HasFactory;

    protected $table = 'penyewaan_detail';
    protected $primaryKey = 'penyewaan_detail_id';
    public $timestamps = true;
    protected $fillable = [
        'penyewaan_detail_penyewaan_id',
        'penyewaan_detail_alat_id',
        'penyewaan_detail_jumlah',
        'penyewaan_detail_subharga'
    ];

    public static function getPenyewaanDetail(){
        $penyewaan_detail = self::all();

        return $penyewaan_detail;
    }

    public static function getPenyewaanDetailById(int $penyewaan_detail_id){
        $penyewaan_detail = self::find($penyewaan_detail_id);

        return $penyewaan_detail;
    }

    public static function createPenyewaanDetail($data){
        $penyewaan_detail = self::create($data);

        return $penyewaan_detail;
    }

    public static function updatePenyewaanDetail(int $penyewaan_detail_id, $data){
        $penyewaan_detail = self::find($penyewaan_detail_id);
        $penyewaan_detail->update($data);

        return $penyewaan_detail;
    }

    public static function deletePenyewaanDetail(int $penyewaan_detail_id){
        $penyewaan_detail = self::find($penyewaan_detail_id);
        $penyewaan_detail->destroy($penyewaan_detail_id);

        return $penyewaan_detail;
    }

    public function penyewaan()
    {
        return $this->belongsTo(PenyewaanModel::class, 'penyewaan_detail_penyewaan_id');
    }

    public function alat()
    {
        return $this->belongsTo(AlatModel::class, 'penyewaan_detail_alat_id');
    }

}
