<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $reportStats = [
            'total' => Report::count(),
            'menunggu' => Report::where('status', 'menunggu')->count(),
            'diproses' => Report::where('status', 'diproses')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
        ];

        $serviceStats = [
            'total' => Service::count(),
            'menunggu' => Service::where('status', 'menunggu')->count(),
            'diproses' => Service::where('status', 'diproses')->count(),
            'selesai' => Service::where('status', 'selesai')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reports' => $reportStats,
                'services' => $serviceStats
            ]
        ]);
    }
}
