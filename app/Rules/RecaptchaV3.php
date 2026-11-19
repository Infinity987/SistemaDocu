<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class RecaptchaV3 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('La verificación de seguridad es obligatoria.');
            return;
        }

        // Enviamos el token a Google para que lo evalúe
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $value,
            // 'remoteip' => request()->ip(),
        ]);

        $body = $response->json();

        // Verificamos si Google aceptó la solicitud y la puntuación recibida
        // 0.5 suele ser el estándar balanceado. Menor a 0.5 es muy probable que sea un bot.
        if (!$body['success'] || !isset($body['score']) || $body['score'] < 0.5) {
            $fail('El sistema detectó actividad sospechosa (Bot). Inténtalo de nuevo.');
        }
    }
}
