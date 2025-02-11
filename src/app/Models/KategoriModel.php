<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';
    public $timestamps = true;
    protected $fillable = [
        'kategori_nama',
    ];


    public static function getKategori(){
        $kategori = self::all();

        return $kategori;
    }

    public static function getKategoriById(int $kategori_id){
        $kategori = self::find($kategori_id);

        return $kategori;
    }

    public static function createKategori($data){
        $kategori = self::create($data);

        return $kategori;
    }

    public static function updateKategori($data, int $kategori_id){
        $kategori = self::find($kategori_id);
        $kategori->update($data);

        return $kategori;
    }

    public static function deleteKategori(int $kategori_id){
        $kategori = self::find($kategori_id);
        $kategori->destroy($kategori_id);

        return $kategori;
    }

    public function alat()
    {
        return $this->hasMany(AlatModel::class, 'alat_kategori_id', 'alat_id');
    }

}
