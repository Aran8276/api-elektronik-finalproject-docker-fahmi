<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanDetailModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PenyewaanDetailController extends Controller
{
    public function index(){
        try {
            $penyewaan_detail = Cache::remember('penyewaan_detail', 60*60*24, function(){
                return PenyewaanDetailModel::getPenyewaanDetail();
            });

            $response = array(
                'success' => true,
                'message' => 'Successfuly, get data penyewaan detail',
                'data' => $penyewaan_detail->isEmpty() ? null : $penyewaan_detail
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

    public function show(int $penyewaan_detail_id){
        try {
            $cacheKey = 'penyewaan_detail)'.$penyewaan_detail_id;
            $penyewaan_detail = Cache::remember($cacheKey, 60*60*24, function() use($penyewaan_detail_id){
                return PenyewaanDetailModel::getPenyewaanDetailById($penyewaan_detail_id);
            });

            if(!$penyewaan_detail){
                $response = [
                    'succes' => false,
                    'message' => 'Data penyewaan detail not found',
                    'data' => null
                ];

                return response()->json($response. 404);
            }

            $response = array(
                'success' => true,
                'message' => 'Successfuly, get id data penyewaan detail',
                'data' => $penyewaan_detail
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
                'penyewaan_detail_penyewaan_id' => 'required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'required|integer',
                'penyewaan_detail_subharga' => 'required|numeric'
            ]);
            if($validator->fails()){
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data penyewaan detail, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            $penyewaan_detail = PenyewaanDetailModel::createPenyewaanDetail($validator->validate());
            Cache::put('penyewaan_detail', PenyewaanDetailModel::getPenyewaanDetail(), 60*60*24);
            $response = array(
                'success' => true,
                'message' => 'Successfuly, create data penyewaan detail',
                'data' => $penyewaan_detail
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

    public function update(Request $request, int $penyewaan_detail_id){
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_detail_penyewaan_id' => 'required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'required|integer',
                'penyewaan_detail_subharga' => 'required|numeric'
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

            $penyewaan_detail = PenyewaanDetailModel::updatePenyewaanDetail($penyewaan_detail_id ,$validator->validate());
            Cache::put('penyewaan_detail', PenyewaanDetailModel::getPenyewaanDetail(), 60*60*24);
            $response = array(
                'success' => true,
                'message' => 'Successfuly, update data penyewaan detail',
                'data' => $penyewaan_detail
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

    public function destroy(int $penyewaan_detail_id){
        try {
            $penyewaan_detail = PenyewaanDetailModel::deletePenyewaanDetail($penyewaan_detail_id);
            Cache::put('penyewaan_detail', PenyewaanDetailModel::getPenyewaanDetail(), 60*5);

            $response = array(
                'success' => true,
                'message' => 'Successfuly delete data penyewaan detail',
                'data' => $penyewaan_detail
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
