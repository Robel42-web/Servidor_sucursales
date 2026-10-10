<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\JsonResponse;

class WorkShiftController extends Controller
{
    public function index(): JsonResponse
    {
        $shifts = WorkShift::query()
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shifts,
        ]);
    }
}
