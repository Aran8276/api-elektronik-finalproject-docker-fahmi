<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class KategoriController extends Controller
{
    public function index()
    {
        try {
            $kategori = Cache::remember('kategori', 60 * 60 * 24, function () {
                return KategoriModel::getKategori();
            });
            $response = array(
                'success' => true,
                'message' => 'Successfuly get data kategori',
                'data' => $kategori->isEmpty() ? null : $kategori
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

    public function show(int $kategori_id)
    {
        try {
            $cacheKey = 'kategori_'.$kategori_id;
            $kategori = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($kategori_id) {
                return KategoriModel::getKategoriById($kategori_id);
            });

            if (!$kategori) {
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
                'data' => $kategori
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
                'kategori_nama' => 'required|string|max:100'
            ]);
            $kategori = KategoriModel::createKategori($validator->validate());
            Cache::put('kategori', KategoriModel::getKategori(), 60 * 5);

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
                'message' => 'Successfuly get data kategori',
                'data' => $kategori
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

    public function update(Request $request, int $kategori_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kategori_nama' => 'required|string|max:100'
            ]);
            $kategori = KategoriModel::updateKategori($validator->validate(), $kategori_id);
            Cache::put('kategori', KategoriModel::getKategori(), 60 * 5);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to update data alat, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            } else if (!$kategori) {
                $response = array(
                    'success' => false,
                    'message' => 'Pelanggan not found',
                    'data' => null
                );

                return response()->json($response, 404);
            }

            $response = array(
                'success' => true,
                'message' => 'Successfuly update alat kategori',
                'data' => $kategori
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

    public function destroy(int $kategori_id)
    {
        try {
            $kategori = KategoriModel::deleteKategori($kategori_id);
            Cache::put('kategori', KategoriModel::getKategori(), 60 * 5);

            $response = array(
                'success' => true,
                'message' => 'Successfuly delete data alat',
                'data' => $kategori
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
