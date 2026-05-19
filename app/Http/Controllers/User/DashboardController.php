<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $userId = auth('api')->id();

        $reportStats = [
            'total' => Report::where('user_id', $userId)->count(),
            'aktif' => Report::where('user_id', $userId)->whereIn('status', ['menunggu', 'diproses'])->count(),
        ];

        $serviceStats = [
            'total' => Service::where('user_id', $userId)->count(),
            'aktif' => Service::where('user_id', $userId)->whereIn('status', ['menunggu', 'diproses'])->count(),
        ];

        $latestReports = Report::with('category')->where('user_id', $userId)->latest()->take(5)->get()->map(function ($item) {
            $item->type = 'Pengaduan';
            return $item;
        });
        
        $latestServices = Service::with('category')->where('user_id', $userId)->latest()->take(5)->get()->map(function ($item) {
            $item->type = 'Permohonan';
            return $item;
        });

        $recentActivities = $latestReports->concat($latestServices)->sortByDesc('created_at')->take(5)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'reports' => $reportStats,
                'services' => $serviceStats,
                'recent_activities' => $recentActivities
            ]
        ]);
    }
}
