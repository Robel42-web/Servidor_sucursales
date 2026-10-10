<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use Illuminate\Http\JsonResponse;

class JobPositionController extends Controller
{
    public function index(): JsonResponse
    {
        $positions = JobPosition::query()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $positions,
        ]);
    }
}
