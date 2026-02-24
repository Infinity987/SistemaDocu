<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Redirige al login cuando la sesión expira (419 Page Expired).
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            // Cerrar sesión si aún aparece autenticado
            if (Auth::check()) {
                Auth::logout();
            }

            // Invalidar sesión y regenerar token
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Si la petición es AJAX / Livewire => responde con JSON
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'message'  => 'Tu sesión ha expirado. Inicia sesión nuevamente.',
                    'redirect' => route('login'),
                ], 419);
            }

            // Para peticiones normales => redirige al login
            return redirect()
                ->route('login')
                ->withErrors(['message' => 'Tu sesión ha expirado por inactividad.']);
        }

        return parent::render($request, $e);
    }
}
