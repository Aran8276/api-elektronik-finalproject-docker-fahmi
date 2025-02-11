<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganDataModel extends Model
{
    use HasFactory;

    protected $table = 'pelanggan_data';
    protected $primaryKey = 'pelanggan_data_id';
    public $timestamps = true;

    protected $fillable = [
        'pelanggan_data_pelanggan_id',
        'pelanggan_data_jenis',
        'pelanggan_data_file'
    ];

    public static function getPelangganData()
    {
        return self::all();
    }

    public static function getPelangganDataById(int $pelanggan_data_id)
    {
        return self::find($pelanggan_data_id);
    }

    public static function createPelangganData($data)
    {
        return self::create($data);
    }

    public static function updatePelangganData(int $pelanggan_data_id, $data)
    {
        $pelangganData = self::find($pelanggan_data_id);
        if ($pelangganData) {
            $pelangganData->update($data);
            return $pelangganData;
        }
        return null;
    }

    public static function deletePelangganData(int $pelanggan_data_id)
    {
        $pelangganData = self::find($pelanggan_data_id);
        if ($pelangganData) {
            $pelangganData->delete();
            return $pelangganData;
        }
        return null;
    }

    public function pelanggan()
    {
        return $this->belongsTo(PelangganModel::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

}
