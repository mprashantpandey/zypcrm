<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class HealthCheckController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $schedulerLastRunAt = Setting::getValue('scheduler_last_run_at');

        return response()->json([
            'ok' => true,
            'app' => config('app.name'),
            'scheduler_last_run_at' => $schedulerLastRunAt,
        ]);
    }
}

