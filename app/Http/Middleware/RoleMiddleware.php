<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware kontrol akses berbasis peran
 *
 * Memverifikasi bahwa pengguna yang terautentikasi memiliki peran yang diperlukan untuk mengakses rute yang dilindungi.
 * Middleware ini memberlakukan otorisasi berdasarkan peran pengguna, memungkinkan beberapa peran per rute.
 *
 * Penggunaan dalam routes:
 *   Route::post('/admin/endpoint', [Controller::class, 'method'])->middleware('role:admin');
 *   Route::post('/teach', [Controller::class, 'method'])->middleware('role:teacher,admin');
 *
 * @see App\Models\User untuk peran yang tersedia: admin, teacher, student
 */
class RoleMiddleware
{
    /**
     * Menangani permintaan masuk
     *
     * @param Request $request Permintaan HTTP yang masuk
     * @param Closure $next Middleware/handler berikutnya dalam pipeline
     * @param string ...$roles Peran yang diperlukan (variadic - menerima multiple role names)
     * @return Response Respons yang akan dikirim
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            $user = $request->user();

            // Periksa apakah pengguna terautentikasi
            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'error' => 'Authentication required to access this resource',
                ], 401);
            }

            // Periksa apakah pengguna memiliki peran yang diperlukan
            if (!in_array($user->role, $roles)) {
                return response()->json([
                    'message' => 'Forbidden',
                    'error' => "Your role '{$user->role}' is not authorized to access this resource. Required roles: " . implode(', ', $roles),
                ], 403);
            }

            return $next($request);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Authorization check failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
