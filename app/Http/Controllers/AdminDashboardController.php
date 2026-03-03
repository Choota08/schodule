<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $data = Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'total_users' => User::count(),
                'total_students' => Student::count(),
                'total_teachers' => Teacher::count(),
                'total_schedules' => Schedule::count(),
            ];
        });

        return response()->json($data);
    }
}
