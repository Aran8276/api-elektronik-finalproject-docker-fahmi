<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PenyewaanController extends Controller
{
    public function index(){
        try {
            $penyewaan = Cache::remember('penyewaan', 60*60*24, function(){
                return PenyewaanModel::getPenyewaan();
            });

            $response = array(
                'success' => true,
                'message' => 'Successfuly, get data pelanaggan',
                'data' => $penyewaan->isEmpty() ? null : $penyewaan
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, these error in internal server',
                'data' => null,
                'error' => $error->getMessage()
            );
            return response()->json($response, 500);
        }
    }

    public function show(int $penyewaan_id){
        try {
            $cacheKey = 'penyewaan_'.$penyewaan_id;
            $penyewaan = Cache::remember($cacheKey, 60*60*24, function() use($penyewaan_id){
                return PenyewaanModel::getPenyewaanById($penyewaan_id);
            });

            if(!$penyewaan){
                $response = [
                    'succes' => false,
                    'message' => 'Data pelanggan not found',
                    'data' => null
                ];

                return response()->json($response. 404);
            }

            $response = array(
                'success' => true,
                'message' => 'Successfuly, get id data pelanaggan',
                'data' => $penyewaan
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, these error in internal server',
                'data' => null,
                'error' => $error->getMessage()
            );
            return response()->json($response, 500);
        }
    }

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id'   => 'required|integer|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => "required|date",
                'penyewaan_tglkembali'     => 'required|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'required|in:Lunas,Belum Dibayar,DP',
                'penyewaan_sttskembali'    => 'required|in:Sudah Kembali,Belum Kembali',
                'penyewaan_totalharga'     => 'required|integer|min:0'
            ]);
            if($validator->fails()){
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data products, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            $penyewaan = PenyewaanModel::createPenyewaan($validator->validate());
            Cache::put('penyewaan', PenyewaanModel::getPenyewaan(), 60*60*24);
            $response = array(
                'success' => true,
                'message' => 'Successfuly, create data pelanaggan',
                'data' => $penyewaan
            );

            return response()->json($response, 201);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, these error in internal server',
                'data' => null,
                'error' => $error->getMessage()
            );
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, int $penyewaan_id){
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id'   => 'required|integer|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => "required|date",
                'penyewaan_tglkembali'     => 'required|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'required|in:Lunas,Belum Dibayar,DP',
                'penyewaan_sttskembali'    => 'required|in:Sudah Kembali,Belum Kembali',
                'penyewaan_totalharga'     => 'required|integer|min:0'
            ]);

            if($validator->fails()){
                $response = array(
                    'success' => false,
                    'message' => 'Failed to update data products, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            $penyewaan = PenyewaanModel::updatePenyewaan($penyewaan_id ,$validator->validate());
            Cache::put('penyewaan', PenyewaanModel::getPenyewaan(), 60*60*24);
            $response = array(
                'success' => true,
                'message' => 'Successfuly, update data pelanaggan',
                'data' => $penyewaan
            );

            return response()->json($response, 201);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, these error in internal server',
                'data' => null,
                'error' => $error->getMessage()
            );
            return response()->json($response, 500);
        }
    }

    public function destroy(int $penyewaan_id){
        try {
            $penyewaan = PenyewaanModel::deletePenyewaan($penyewaan_id);
            Cache::put('penyewaan', PenyewaanModel::getPenyewaan(), 60*5);

            $response = array(
                'success' => true,
                'message' => 'Successfuly delete data penyewaan',
                'data' => $penyewaan
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, these error in internal server',
                'data' => null,
                'error' => $error->getMessage()
            );
            return response()->json($response, 500);
        }
    }
}
