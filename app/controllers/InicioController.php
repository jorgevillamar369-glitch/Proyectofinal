<?php
/**
 * Portada pública del acuario.
 */

declare(strict_types=1);

final class InicioController extends Controller
{
    public function index(): void
    {
        $totales = Reporte::totales();
        $litros = Reporte::litrosTotales();

        $destacados = array_values(array_filter(
            Tanque::todos(),
            static fn (array $tanque): bool => in_array($tanque['codigo'], ['T-01', 'T-04', 'T-07'], true)
        ));

        $imagenes = [
            'T-01' => ['archivo' => 'assets/img/hero-tanque.jpg', 'alt' => 'Visitantes frente al ventanal del Gran Arrecife'],
            'T-04' => ['archivo' => 'assets/img/acceso-acuario.jpg', 'alt' => 'Cardumen de peces amazónicos dentro del tanque'],
            'T-07' => ['archivo' => 'assets/img/coral.jpg', 'alt' => 'Anémonas y coral del tanque táctil'],
        ];

        $numeros = ['T-01' => '01', 'T-04' => '02', 'T-07' => '03'];

        $horarios = [
            'Martes a viernes'   => '09:00 – 17:30',
            'Sábados y domingos' => '09:00 – 19:00',
            'Lunes'              => 'Cerrado por mantenimiento',
            'Última entrada'     => '45 minutos antes del cierre',
        ];

        $this->vista('inicio', [
            'tituloPagina'   => 'Aqua Viva · Acuario de Quito',
            'preloadImagen'  => 'assets/img/hero-tanque.jpg',
            'tanquesActivos' => $totales['tanquesOperativos'],
            'tanquesTotal'   => $totales['tanquesTotal'],
            'totalEspecies'  => $totales['especies'],
            'litros'        => $litros,
            'destacados'    => $destacados,
            'imagenes'      => $imagenes,
            'numeros'       => $numeros,
            'horarios'      => $horarios,
        ], 'publica');
    }
}
