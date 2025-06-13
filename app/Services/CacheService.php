<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    // Tempos de cache em segundos
    const CACHE_TIMES = [
        'supplier_detail' => 300,      // 5 minutos
        'supplier_list' => 60,         // 1 minuto
        'supplier_search' => 120,      // 2 minutos
        'supplier_stats' => 900,       // 15 minutos
        'cnpj_data' => 86400,         // 24 horas
        'validation_cache' => 3600,    // 1 hora
    ];

    // Prefixos para organizar as chaves
    const CACHE_PREFIXES = [
        'supplier' => 'suppliers',
        'cnpj' => 'cnpj_data',
        'stats' => 'stats',
        'validation' => 'validation',
    ];

    /**
     * Gerar chave de cache padronizada
     */
    public static function key(string $prefix, string $identifier): string
    {
        return self::CACHE_PREFIXES[$prefix] . '.' . $identifier;
    }

    /**
     * Obter tempo de cache padrão
     */
    public static function time(string $type): int
    {
        return self::CACHE_TIMES[$type] ?? 300;
    }

    /**
     * Limpar todos os caches relacionados a fornecedores
     */
    public static function clearSupplierCaches(): void
    {
        $patterns = [
            'suppliers.*',
            'stats.*',
        ];

        foreach ($patterns as $pattern) {
            self::clearByPattern($pattern);
        }
    }

    /**
     * Limpar cache por padrão (requer Redis)
     */
    public static function clearByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $keys = $redis->keys(config('cache.prefix') . ':' . $pattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // Fallback para outros drivers de cache
                self::clearCommonKeys();
            }
        } catch (\Exception $e) {
            \Log::warning('Cache clearing failed: ' . $e->getMessage());
        }
    }

    /**
     * Limpar chaves comuns quando não há Redis
     */
    private static function clearCommonKeys(): void
    {
        $commonKeys = [
            'suppliers.all',
            'suppliers.stats',
        ];

        foreach ($commonKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Cache com invalidação automática
     */
    public static function rememberWithTags(string $key, array $tags, int $seconds, \Closure $callback)
    {
        if (config('cache.default') === 'redis') {
            return Cache::tags($tags)->remember($key, $seconds, $callback);
        }
        
        return Cache::remember($key, $seconds, $callback);
    }

    /**
     * Invalidar cache por tags
     */
    public static function invalidateTags(array $tags): void
    {
        if (config('cache.default') === 'redis') {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * Obter informações sobre o cache
     */
    public static function getInfo(): array
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                return [
                    'driver' => 'redis',
                    'memory_usage' => $redis->info('memory'),
                    'keyspace' => $redis->info('keyspace'),
                ];
            }
            
            return [
                'driver' => config('cache.default'),
                'memory_usage' => 'N/A',
                'keyspace' => 'N/A',
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Warm up cache com dados essenciais
     */
    public static function warmUp(): void
    {
        try {
            // Pré-carregar estatísticas
            app(\App\Repositories\SupplierRepositoryInterface::class)->getStats();
            
            // Pré-carregar lista de fornecedores ativos
            app(\App\Repositories\SupplierRepositoryInterface::class)->all();
            
            \Log::info('Cache warmed up successfully');
        } catch (\Exception $e) {
            \Log::error('Cache warm up failed: ' . $e->getMessage());
        }
    }
}