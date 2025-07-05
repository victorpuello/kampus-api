<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\Models\Anio;
use App\Http\Requests\StorePeriodoRequest;
use App\Http\Requests\UpdatePeriodoRequest;
use App\Http\Resources\PeriodoResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Periodo::with(['anio']);

        // Filtrar por año académico si se proporciona
        if ($request->has('anio_id')) {
            $query->where('anio_id', $request->anio_id);
        }

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('fecha_inicio', 'like', "%{$search}%")
                  ->orWhere('fecha_fin', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 10);
        $periodos = $query->paginate($perPage);

        return response()->json([
            'data' => PeriodoResource::collection($periodos->items()),
            'current_page' => $periodos->currentPage(),
            'last_page' => $periodos->lastPage(),
            'per_page' => $periodos->perPage(),
            'total' => $periodos->total(),
            'from' => $periodos->firstItem(),
            'to' => $periodos->lastItem(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeriodoRequest $request): JsonResponse
    {
        try {
            $periodo = Periodo::create($request->validated());
            
            return response()->json([
                'message' => 'Periodo creado exitosamente',
                'data' => new PeriodoResource($periodo->load('anio'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Periodo $periodo): JsonResponse
    {
        return response()->json([
            'data' => new PeriodoResource($periodo->load('anio'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeriodoRequest $request, Periodo $periodo): JsonResponse
    {
        try {
            $periodo->update($request->validated());
            
            return response()->json([
                'message' => 'Periodo actualizado exitosamente',
                'data' => new PeriodoResource($periodo->load('anio'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periodo $periodo): JsonResponse
    {
        try {
            $periodo->delete();
            
            return response()->json([
                'message' => 'Periodo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener periodos de un año académico específico
     */
    public function getByAnio(Anio $anio, Request $request): JsonResponse
    {
        $query = $anio->periodos();

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('fecha_inicio', 'like', "%{$search}%")
                  ->orWhere('fecha_fin', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'fecha_inicio');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 10);
        $periodos = $query->paginate($perPage);

        return response()->json([
            'data' => PeriodoResource::collection($periodos->items()),
            'current_page' => $periodos->currentPage(),
            'last_page' => $periodos->lastPage(),
            'per_page' => $periodos->perPage(),
            'total' => $periodos->total(),
            'from' => $periodos->firstItem(),
            'to' => $periodos->lastItem(),
        ]);
    }

    /**
     * Crear periodo para un año académico específico
     */
    public function storeForAnio(Anio $anio, StorePeriodoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['anio_id'] = $anio->id;
            
            $periodo = Periodo::create($data);
            
            return response()->json([
                'message' => 'Periodo creado exitosamente',
                'data' => new PeriodoResource($periodo->load('anio'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un periodo específico de un año académico
     */
    public function showForAnio(Anio $anio, Periodo $periodo): JsonResponse
    {
        // Verificar que el periodo pertenece al año académico
        if ($periodo->anio_id !== $anio->id) {
            return response()->json([
                'message' => 'El periodo no pertenece al año académico especificado'
            ], 404);
        }

        return response()->json([
            'data' => new PeriodoResource($periodo->load('anio'))
        ]);
    }

    /**
     * Actualizar un periodo específico de un año académico
     */
    public function updateForAnio(Anio $anio, Periodo $periodo, UpdatePeriodoRequest $request): JsonResponse
    {
        // Verificar que el periodo pertenece al año académico
        if ($periodo->anio_id !== $anio->id) {
            return response()->json([
                'message' => 'El periodo no pertenece al año académico especificado'
            ], 404);
        }

        try {
            $periodo->update($request->validated());
            
            return response()->json([
                'message' => 'Periodo actualizado exitosamente',
                'data' => new PeriodoResource($periodo->load('anio'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un periodo específico de un año académico
     */
    public function destroyForAnio(Anio $anio, Periodo $periodo): JsonResponse
    {
        // Verificar que el periodo pertenece al año académico
        if ($periodo->anio_id !== $anio->id) {
            return response()->json([
                'message' => 'El periodo no pertenece al año académico especificado'
            ], 404);
        }

        try {
            $periodo->delete();
            
            return response()->json([
                'message' => 'Periodo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el periodo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 