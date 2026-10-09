<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'doctor') {
            return response()->json([
                'message' => 'Only doctors may author consultations and prescriptions.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
