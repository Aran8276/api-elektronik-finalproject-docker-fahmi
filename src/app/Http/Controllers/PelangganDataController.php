<?php

namespace App\Http\Controllers;

use App\Models\PelangganDataModel;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PelangganDataController extends Controller
{
    public function index()
    {
        try {
            $pelangganData = Cache::remember('pelanggan_data', 60 * 60 * 24, function () {
                return PelangganDataModel::getPelangganData();
            });

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched pelanggan data',
                'data' => $pelangganData->isEmpty() ? null : $pelangganData
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function show(int $pelanggan_data_id)
    {
        try {
            $cacheKey = 'pelanggan_data_' . $pelanggan_data_id;
            $pelangganData = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($pelanggan_data_id) {
                return PelangganDataModel::getPelangganDataById($pelanggan_data_id);
            });

            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched pelanggan data',
                'data' => $pelangganData
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_data_pelanggan_id' => 'required|integer|exists:pelanggan,pelanggan_id',
                'pelanggan_data_jenis' => 'required|in:KTP,SIM',
                'pelanggan_data_file' => 'required|mimes:jpg,png,jpeg|max:2048' // Validasi file
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            if ($request->hasFile('pelanggan_data_file')) {
                $file = $request->file('pelanggan_data_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads', $filename, 'public');

                $validatedData = $validator->validated();
                $validatedData['pelanggan_data_file'] = 'storage/' . $filepath;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File pelanggan wajib diunggah!',
                    'data' => null
                ], 400);
            }

            $pelangganData = PelangganDataModel::createPelangganData($validatedData);
            Cache::put('pelanggan_data', PelangganDataModel::getPelangganData(), 60 * 60 * 24);

            return response()->json([
                'success' => true,
                'message' => 'Successfully created pelanggan data',
                'data' => $pelangganData
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $pelanggan_data_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_data_pelanggan_id' => 'required|integer|exists:pelanggan,pelanggan_id',
                'pelanggan_data_jenis' => 'required|in:KTP,SIM',
                'pelanggan_data_file' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $pelangganData = PelangganDataModel::updatePelangganData($pelanggan_data_id, $validator->validated());

            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan data not found'
                ], 404);
            }

            Cache::put('pelanggan_data', PelangganDataModel::getPelangganData(), 60 * 60 * 24);

            return response()->json([
                'success' => true,
                'message' => 'Successfully updated pelanggan data',
                'data' => $pelangganData
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function destroy(int $pelanggan_data_id)
    {
        try {
            $pelangganData = PelangganDataModel::deletePelangganData($pelanggan_data_id);

            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan data not found'
                ], 404);
            }

            Cache::put('pelanggan_data', PelangganDataModel::getPelangganData(), 60 * 5);

            return response()->json([
                'success' => true,
                'message' => 'Successfully deleted pelanggan data',
                'data' => $pelangganData
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
