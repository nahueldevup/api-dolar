<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Cotizacion;
use Carbon\Carbon;
use Exception;

class ConsultarCotizaciones extends Command
{
    protected $signature = 'cotizaciones:consultar {--manual : Ejecutar consulta manual}';
    protected $description = 'Consulta las cotizaciones del dólar desde la API externa';

    private $tipos = ['oficial', 'blue', 'mep'];
    private $maxReintentos = 3;

    public function handle()
    {
        $esManual = $this->option('manual');
        $fecha = Carbon::now()->format('Y-m-d');
        
        $this->info('Iniciando consulta de cotizaciones...');
        
        if ($esManual) {
            $this->warn('🔧 Consulta MANUAL iniciada');
        } else {
            $this->info('⏰ Consulta AUTOMÁTICA iniciada');
        }

        $exitos = 0;
        $errores = 0;

        foreach ($this->tipos as $tipo) {
            if ($this->consultarTipo($tipo, $fecha, $esManual)) {
                $exitos++;
            } else {
                $errores++;
            }
        }

        $this->info("✅ Cotizaciones consultadas: {$exitos} éxitos, {$errores} errores");
        
        return Command::SUCCESS;
    }

    private function consultarTipo($tipo, $fecha, $esManual = false)
    {
        // Verificar si ya existe (solo en automático)
        if (!$esManual && Cotizacion::existeParaFecha($tipo, $fecha)) {
            $this->warn("⚠️ Ya existe cotización {$tipo} para {$fecha}");
            return true;
        }

        $baseUrl = config('services.dolarapi.url');
        
        for ($intento = 1; $intento <= $this->maxReintentos; $intento++) {
            try {
                $this->info("Consultando {$tipo} (intento {$intento})...");
                
                $response = Http::timeout(10)->get("{$baseUrl}/{$tipo}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['compra']) && isset($data['venta'])) {
                        // Guardar o actualizar
                        Cotizacion::updateOrCreate(
                            [
                                'tipo' => $tipo,
                                'fecha' => $fecha
                            ],
                            [
                                'compra' => $data['compra'],
                                'venta' => $data['venta']
                            ]
                        );
                        
                        $this->info("✅ {$tipo}: Compra={$data['compra']}, Venta={$data['venta']}");
                        return true;
                    } else {
                        $this->error("❌ Datos incompletos para {$tipo}");
                    }
                } else {
                    $this->error("❌ Error HTTP {$response->status()} para {$tipo}");
                }
                
            } catch (Exception $e) {
                $this->error("❌ Excepción para {$tipo}: " . $e->getMessage());
            }
            
            // Esperar antes del siguiente intento
            if ($intento < $this->maxReintentos) {
                $this->warn("⏳ Esperando 5 segundos antes del siguiente intento...");
                sleep(5);
            }
        }
        
        $this->error("❌ Falló después de {$this->maxReintentos} intentos: {$tipo}");
        return false;
    }
}