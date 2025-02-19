<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener el parámetro "id" de la ruta
        $id = $request->route('id');

        // Validar que sea un número entero positivo
        if (!ctype_digit($id)) {
            return response()->json(['error' => 'El ID debe ser un número entero válido'], 400);
        }

        return $next($request);
    }
}
