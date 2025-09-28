<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cotizacion;
use Carbon\Carbon;

class CotizacionesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Generando historial de cotizaciones desde enero hasta hoy...');
        
        // Configurar fechas
        $fechaInicio = Carbon::create(2024, 1, 1);
        $fechaFin = Carbon::now();
        
        // Valores base iniciales para enero 2024 (aproximados)
        $valoresBase = [
            'oficial' => ['compra' => 800, 'venta' => 808],
            'blue' => ['compra' => 980, 'venta' => 1000],
            'mep' => ['compra' => 960, 'venta' => 975]
        ];
        
        $fecha = $fechaInicio->copy();
        $contador = 0;
        
        while ($fecha->lte($fechaFin)) {
            foreach ($valoresBase as $tipo => $valores) {
                
                // Generar fluctuación realista
                $fluctuacion = $this->generarFluctuacion($fecha, $tipo);
                
                $compra = round($valores['compra'] + $fluctuacion['compra'], 2);
                $venta = round($valores['venta'] + $fluctuacion['venta'], 2);
                
                // Asegurar que venta > compra
                if ($venta <= $compra) {
                    $venta = $compra + rand(5, 20);
                }
                
                Cotizacion::updateOrCreate(
                    [
                        'tipo' => $tipo,
                        'fecha' => $fecha->format('Y-m-d')
                    ],
                    [
                        'compra' => $compra,
                        'venta' => $venta
                    ]
                );
                
                $contador++;
            }
            
            // Mostrar progreso cada 30 días
            if ($fecha->day == 1) {
                $this->command->info("📅 Procesando: " . $fecha->format('F Y'));
            }
            
            $fecha->addDay();
        }
        
        $this->command->info("✅ Se crearon {$contador} registros de cotizaciones");
        $this->command->info("📊 Período: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}");
    }
    
    /**
     * Generar fluctuación realista según el mes y tipo de cambio
     */
    private function generarFluctuacion($fecha, $tipo)
    {
        $mes = $fecha->month;
        $dia = $fecha->dayOfYear;
        
        // Tendencia general alcista durante el año
        $tendenciaAnual = $dia * 0.5; // Incremento gradual
        
        // Volatilidad por tipo
        $volatilidad = [
            'oficial' => 2,   // Más estable
            'blue' => 15,     // Muy volátil  
            'mep' => 8        // Moderadamente volátil
        ];
        
        // Eventos importantes del año (simulados)
        $impactoEventos = 0;
        
        // Elecciones (octubre-noviembre)
        if (in_array($mes, [10, 11])) {
            $impactoEventos += rand(-30, 50);
        }
        
        // Fin de año (diciembre)
        if ($mes == 12) {
            $impactoEventos += rand(-10, 25);
        }
        
        // Temporada alta turística (enero-febrero)
        if (in_array($mes, [1, 2])) {
            $impactoEventos += rand(-15, 30);
        }
        
        // Fluctuación diaria aleatoria
        $fluctuacionDiaria = rand(-$volatilidad[$tipo], $volatilidad[$tipo]);
        
        $compraFluctuacion = $tendenciaAnual + $impactoEventos + $fluctuacionDiaria;
        $ventaFluctuacion = $compraFluctuacion + rand(2, 8); // Venta siempre mayor
        
        return [
            'compra' => $compraFluctuacion,
            'venta' => $ventaFluctuacion
        ];
    }
}