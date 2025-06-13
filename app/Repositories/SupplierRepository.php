<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SupplierRepository implements SupplierRepositoryInterface
{
    protected $model;
    protected $cacheTime = 300; // 5 minutos

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return Cache::remember('suppliers.all', $this->cacheTime, function () {
            return $this->model->active()->orderBy('name')->get();
        });
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Cache baseado nos filtros aplicados
        $cacheKey = 'suppliers.paginate.' . md5(serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, 60, function () use ($perPage, $filters) {
            $query = $this->model->query();

            // Aplicar filtros
            if (!empty($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            if (!empty($filters['document'])) {
                $query->byDocument($filters['document']);
            }

            if (!empty($filters['document_type'])) {
                $query->where('document_type', $filters['document_type']);
            }

            if (!empty($filters['city'])) {
                $query->where('city', 'like', '%' . $filters['city'] . '%');
            }

            if (!empty($filters['state'])) {
                $query->where('state', $filters['state']);
            }

            if (isset($filters['active'])) {
                $query->where('active', $filters['active']);
            } else {
                $query->active();
            }

            // Ordenação
            $sortBy = $filters['sort_by'] ?? 'name';
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($sortBy, $sortOrder);

            return $query->paginate($perPage);
        });
    }

    public function find(int $id): ?Supplier
    {
        return Cache::remember("supplier.{$id}", $this->cacheTime, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByDocument(string $document): ?Supplier
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $document);
        
        return Cache::remember("supplier.document.{$cleanDocument}", $this->cacheTime, function () use ($document) {
            return $this->model->byDocument($document)->first();
        });
    }

    public function create(array $data): Supplier
    {
        $supplier = $this->model->create($data);
        
        // Limpar caches relacionados
        $this->clearSupplierCaches();
        
        return $supplier;
    }

    public function update(int $id, array $data): ?Supplier
    {
        $supplier = $this->find($id);
        
        if ($supplier) {
            $supplier->update($data);
            
            // Limpar caches relacionados
            $this->clearSupplierCaches();
            Cache::forget("supplier.{$id}");
            
            return $supplier->fresh();
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $supplier = $this->find($id);
        
        if ($supplier) {
            $result = $supplier->delete();
            
            // Limpar caches relacionados
            $this->clearSupplierCaches();
            Cache::forget("supplier.{$id}");
            
            return $result;
        }

        return false;
    }

    public function search(string $term): Collection
    {
        $cacheKey = 'suppliers.search.' . md5($term);
        
        return Cache::remember($cacheKey, 120, function () use ($term) {
            return $this->model->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                      ->orWhere('trade_name', 'like', '%' . $term . '%')
                      ->orWhere('email', 'like', '%' . $term . '%')
                      ->orWhere('city', 'like', '%' . $term . '%');
            })->active()->orderBy('name')->get();
        });
    }

    /**
     * Limpar caches relacionados aos fornecedores
     */
    private function clearSupplierCaches(): void
    {
        Cache::forget('suppliers.all');
        
        // Limpar cache de paginação (usando padrão de tags se disponível)
        $this->clearPaginationCache();
    }

    /**
     * Limpar cache de paginação
     */
    private function clearPaginationCache(): void
    {
        try {
            // Apenas se Redis estiver configurado e disponível
            if (config('cache.default') === 'redis' && extension_loaded('redis')) {
                $keys = Cache::getRedis()->keys('*suppliers.paginate*');
                foreach ($keys as $key) {
                    Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
                }
            }
            // Para outros drivers, não fazemos nada pois já limpamos os principais
        } catch (\Exception $e) {
            // Se der erro, apenas log e continua
            \Log::warning('Could not clear pagination cache: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de fornecedores com cache
     */
    public function getStats(): array
    {
        return Cache::remember('suppliers.stats', 900, function () { // 15 minutos
            return [
                'total' => $this->model->count(),
                'active' => $this->model->where('active', true)->count(),
                'inactive' => $this->model->where('active', false)->count(),
                'cnpj_count' => $this->model->where('document_type', 'cnpj')->count(),
                'cpf_count' => $this->model->where('document_type', 'cpf')->count(),
                'by_state' => $this->model->selectRaw('state, COUNT(*) as count')
                    ->whereNotNull('state')
                    ->groupBy('state')
                    ->pluck('count', 'state')
                    ->toArray()
            ];
        });
    }
}