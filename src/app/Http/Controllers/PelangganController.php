<?php

namespace App\Http\Controllers;

use App\Models\PelangganModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PelangganController extends Controller
{
    public function index()
    {
        try {
            $pelanggan = Cache::remember('pelanggan', 60 * 60 * 24, function () {
                return PelangganModel::getPelanggan();
            });
            $response = array(
                'success' => true,
                'message' => 'Successfully, get data pelanggan',
                'data' => $pelanggan->isEmpty() ? null : $pelanggan
            );
            return response()->json($response, 200)->header('Cache-Control', 'public,max-age=300');
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

    public function show(int $pelanggan_id)
    {
        try {
            $cacheKey = 'pelanggan_'.$pelanggan_id;
            $pelanggan = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($pelanggan_id) {
                return PelangganModel::getPelangganById($pelanggan_id);
            });

            if (!$pelanggan) {
                $response = array(
                    'success' => false,
                    'message' => 'Pelanggan not found',
                    'data' => null
                );

                return response()->json($response, 404);
            }

            $response = array(
                'success' => true,
                'message' => "Successfuly, get data pelanggan",
                'data' => $pelanggan
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
                'pelanggan_nama' => 'required|string|max:100',
                'pelanggan_alamat' => 'required|string|max:200',
                'pelanggan_notelp' => 'required|string|max:13',
                'pelanggan_email' => 'required|email|max:100'
            ]);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data products, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            $pelanggan = PelangganModel::createPelanggan($validator->validate());
            Cache::put('pelanggan', PelangganModel::getPelanggan(), 60 * 60 * 24);
            $response = array(
                'success' => true,
                'message' => "Successfuly, create data pelanggan",
                'data' => $pelanggan
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

    public function update(Request $request, int $pelanggan_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_nama' => 'required|string|max:100',
                'pelanggan_alamat' => 'required|string|max:200',
                'pelanggan_notelp' => 'required|string|max:13',
                'pelanggan_email' => 'required|email|max:100'
            ]);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data products, data not completed, please check your data',
                    'data' => null,
                    'error' => $validator->errors()
                );
                return response()->json($response, 400);
            }

            Cache::put('pelanggan', PelangganModel::getPelanggan(), 60 * 60 * 24);
            $pelanggan = PelangganModel::updatePelanggan($pelanggan_id, $validator->validate());
            $response = array(
                'success' => true,
                'message' => "Successfuly, update data pelanggan",
                'data' => $pelanggan
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

    public function destroy(int $pelanggan_id)
    {
        try {
            $pelanggan = PelangganModel::deletePelanggan($pelanggan_id);
            Cache::put('pelanggan', PelangganModel::getPelanggan(), 60 * 5);
            $response = array(
                'success' => true,
                'message' => "Succesfully, delete categories data",
                'data' => $pelanggan
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => "Sorry, these error in internal server",
                'data' => null,
                'error' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
    }
}
