<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('services.recaptcha.secret')) {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response');

        if (empty($token)) {
            return response()->json(['message' => 'reCAPTCHA token is required'], 422);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        if (!$result['success'] || $result['score'] < 0.5) {
            return response()->json(['message' => 'reCAPTCHA verification failed'], 422);
        }

        return $next($request);
    }
}
