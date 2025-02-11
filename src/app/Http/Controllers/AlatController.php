<?php

namespace App\Http\Controllers;

use App\Models\AlatModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class AlatController extends Controller
{
    public function index()
    {
        try {
            $alat = Cache::remember('alat', 60 * 60 * 24, function () {
                return AlatModel::getAlat();
            });
            $response = array(
                'success' => true,
                'message' => 'Successfuly get data alat',
                'data' => $alat->isEmpty() ? null : $alat
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

    public function show(int $alat_id)
    {
        try {
            $cacheKey = 'alat_'.$alat_id;
            $alat = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($alat_id) {
                return AlatModel::getAlatById($alat_id);
            });

            if (!$alat) {
                $response = array(
                    'success' => false,
                    'message' => 'Pelanggan not found',
                    'data' => null
                );

                return response()->json($response, 404);
            }
            $response = array(
                'success' => true,
                'message' => 'Successfuly get data kategori',
                'data' => $alat
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'alat_nama' => 'required|string|max:150',
                'alat_deskripsi' => 'required|string|max:255',
                'alat_hargaperhari' => 'required|integer',
                'alat_stok' => 'required|integer',
                'alat_kategori_id' => 'required|integer|exists:kategori,kategori_id'
            ]);
            $alat = AlatModel::createAlat($validator->validate());
            Cache::put('alat', AlatModel::getAlat(), 60 * 5);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data kategori, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            $response = array(
                'success' => true,
                'message' => 'Successfuly get data alat',
                'data' => $alat
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

    public function update(Request $request, int $alat_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'alat_nama' => 'required|string|max:150',
                'alat_deskripsi' => 'required|string|max:255',
                'alat_hargaperhari' => 'required|integer',
                'alat_stok' => 'required|integer',
                'alat_kategori_id' => 'required|integer|exists:kategori,kategori_id'
            ]);
            $alat = AlatModel::updateAlat($validator->validate(), $alat_id);
            Cache::put('alat', AlatModel::getAlat(), 60 * 5);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data alat, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            } else if (!$alat) {
                $response = array(
                    'success' => false,
                    'message' => 'Pelanggan not found',
                    'data' => null
                );

                return response()->json($response, 404);
            }

            $response = array(
                'success' => true,
                'message' => 'Successfuly update data alat',
                'data' => $alat
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

    public function destroy(int $alat_id)
    {
        try {
            $alat = AlatModel::deleteAlat($alat_id);
            Cache::put('alat', AlatModel::getAlat(), 60 * 5);

            $response = array(
                'success' => true,
                'message' => 'Successfuly delete data alat',
                'data' => $alat
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
