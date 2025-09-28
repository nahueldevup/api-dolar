<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';
    
    protected $fillable = [
        'tipo',
        'compra', 
        'venta',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'date',
        'compra' => 'decimal:2',
        'venta' => 'decimal:2'
    ];

    /**
     * Obtener promedio mensual por tipo y valor
     */
    public static function promedioMensual($tipo, $valor, $mes)
    {
        // Parsear el mes (formato: 2024-01)
        $fecha = Carbon::createFromFormat('Y-m', $mes);
        
        return self::where('tipo', $tipo)
            ->whereYear('fecha', $fecha->year)
            ->whereMonth('fecha', $fecha->month)
            ->avg($valor);
    }

    /**
     * Verificar si ya existe cotización para un tipo y fecha
     */
    public static function existeParaFecha($tipo, $fecha)
    {
        return self::where('tipo', $tipo)
            ->where('fecha', $fecha)
            ->exists();
    }
}