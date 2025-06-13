<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Configurar headers de cache baseado na rota
        if ($request->is('api/suppliers*')) {
            return $this->addSupplierCacheHeaders($request, $response);
        }

        return $response;
    }

    /**
     * Adicionar headers de cache específicos para fornecedores
     */
    private function addSupplierCacheHeaders(Request $request, Response $response): Response
    {
        // Para requests GET apenas
        if ($request->isMethod('GET')) {
            if ($request->is('api/suppliers/stats')) {
                // Estatísticas: cache por 15 minutos
                $response->headers->set('Cache-Control', 'public, max-age=900, s-maxage=900');
            } elseif ($request->is('api/suppliers') && !$request->has('search')) {
                // Lista sem busca: cache por 1 minuto
                $response->headers->set('Cache-Control', 'public, max-age=60, s-maxage=60');
            } elseif ($request->is('api/suppliers/*') && is_numeric($request->route('supplier'))) {
                // Fornecedor específico: cache por 5 minutos
                $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=300');
            } else {
                // Outras rotas de consulta: cache por 2 minutos
                $response->headers->set('Cache-Control', 'public, max-age=120, s-maxage=120');
            }

            // Headers adicionais para otimização
            $response->headers->set('Vary', 'Accept, Accept-Encoding');
            $response->headers->set('ETag', md5($response->getContent()));
        } else {
            // Para métodos que modificam dados (POST, PUT, DELETE)
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
