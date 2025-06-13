<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Repositories\SupplierRepositoryInterface;
use App\Services\BrasilApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    protected $supplierRepository;
    protected $brasilApiService;

    public function __construct(
        SupplierRepositoryInterface $supplierRepository,
        BrasilApiService $brasilApiService
    ) {
        $this->supplierRepository = $supplierRepository;
        $this->brasilApiService = $brasilApiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'name', 'document', 'document_type', 'city', 
            'state', 'active', 'sort_by', 'sort_order', 'search'
        ]);

        // Se houver termo de busca, usar método search
        if ($request->has('search')) {
            $suppliers = $this->supplierRepository->search($request->search);
            
            return response()->json([
                'data' => SupplierResource::collection($suppliers),
                'message' => 'Suppliers retrieved successfully'
            ]);
        }

        $perPage = $request->get('per_page', 15);
        $suppliers = $this->supplierRepository->paginate($perPage, $filters);

        return response()->json([
            'data' => SupplierResource::collection($suppliers->items()),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem(),
            ],
            'message' => 'Suppliers retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierRepository->create($request->validated());

        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $supplier = $this->supplierRepository->find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, string $id): JsonResponse
    {
        $supplier = $this->supplierRepository->update($id, $request->validated());

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->supplierRepository->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ]);
    }

    /**
     * Find supplier by document.
     */
    public function findByDocument(Request $request): JsonResponse
    {
        $request->validate([
            'document' => 'required|string'
        ]);

        $supplier = $this->supplierRepository->findByDocument($request->document);

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier found'
        ]);
    }

    /**
     * Get CNPJ data from BrasilAPI.
     */
    public function getCnpjData(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|string'
        ]);

        $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json([
                'message' => 'Invalid CNPJ format'
            ], 422);
        }

        $data = $this->brasilApiService->getCnpjData($cnpj);

        if (!$data) {
            return response()->json([
                'message' => 'CNPJ not found or API unavailable'
            ], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'CNPJ data retrieved successfully'
        ]);
    }

    /**
     * Get suppliers statistics with cache.
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->supplierRepository->getStats();

        return response()->json([
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Clear all supplier caches.
     */
    public function clearCache(): JsonResponse
    {
        \App\Services\CacheService::clearSupplierCaches();

        return response()->json([
            'message' => 'Cache cleared successfully'
        ]);
    }

    /**
     * Get cache information.
     */
    public function getCacheInfo(): JsonResponse
    {
        $info = \App\Services\CacheService::getInfo();

        return response()->json([
            'data' => $info,
            'message' => 'Cache information retrieved successfully'
        ]);
    }

    /**
     * Warm up cache with essential data.
     */
    public function warmUpCache(): JsonResponse
    {
        \App\Services\CacheService::warmUp();

        return response()->json([
            'message' => 'Cache warmed up successfully'
        ]);
    }
}
