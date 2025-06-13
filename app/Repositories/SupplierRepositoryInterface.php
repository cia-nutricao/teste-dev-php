<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function find(int $id): ?Supplier;
    
    public function findByDocument(string $document): ?Supplier;
    
    public function create(array $data): Supplier;
    
    public function update(int $id, array $data): ?Supplier;
    
    public function delete(int $id): bool;
    
    public function search(string $term): Collection;
    
    public function getStats(): array;
}