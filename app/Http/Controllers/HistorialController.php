<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Cotizacion;
use Carbon\Carbon;

class HistorialController extends Controller
{
    /**
     * Ejecutar consulta manual de cotizaciones
     */
    public function consultarManual()
    {
        try {
            // Ejecutar el comando con la opción manual
            Artisan::call('cotizaciones:consultar', ['--manual' => true]);
            
            $output = Artisan::output();
            
            return response()->json([
                'mensaje' => 'Consulta manual ejecutada correctamente',
                'detalles' => $output
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al ejecutar consulta manual',
                'detalles' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener promedio mensual
     * GET /api/promedio?tipo=blue&valor=venta&mes=2024-01
     */
    public function promedio(Request $request)
    {
        // Validar parámetros
        $request->validate([
            'tipo' => 'required|in:oficial,blue,mep',
            'valor' => 'required|in:compra,venta', 
            'mes' => 'required|regex:/^\d{4}-\d{2}$/' // Formato: YYYY-MM
        ]);

        $tipo = $request->get('tipo');
        $valor = $request->get('valor');
        $mes = $request->get('mes');

        try {
            // Verificar que el mes sea válido
            Carbon::createFromFormat('Y-m', $mes);
            
            $promedio = Cotizacion::promedioMensual($tipo, $valor, $mes);
            
            if ($promedio === null) {
                return response()->json([
                    'mensaje' => 'No hay datos disponibles para el período solicitado',
                    'tipo' => $tipo,
                    'valor' => $valor,
                    'mes' => $mes,
                    'promedio' => null
                ], 404);
            }

            return response()->json([
                'tipo' => $tipo,
                'valor' => $valor,
                'mes' => $mes,
                'promedio' => round($promedio, 2),
                'cantidad_registros' => $this->contarRegistros($tipo, $mes)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al calcular el promedio',
                'detalles' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener historial de cotizaciones con filtros
     */
    public function historial(Request $request)
    {
        $query = Cotizacion::query();

        // Filtro por tipo
        if ($request->has('tipo')) {
            $query->where('tipo', $request->get('tipo'));
        }

        // Filtro por fecha desde
        if ($request->has('desde')) {
            $query->where('fecha', '>=', $request->get('desde'));
        }

        // Filtro por fecha hasta
        if ($request->has('hasta')) {
            $query->where('fecha', '<=', $request->get('hasta'));
        }

        $cotizaciones = $query->orderBy('fecha', 'desc')
                            ->paginate(50);

        return response()->json($cotizaciones);
    }

    /**
     * Contar registros para un tipo y mes específico
     */
    private function contarRegistros($tipo, $mes)
    {
        $fecha = Carbon::createFromFormat('Y-m', $mes);
        
        return Cotizacion::where('tipo', $tipo)
            ->whereYear('fecha', $fecha->year)
            ->whereMonth('fecha', $fecha->month)
            ->count();
    }
}