<?php
/**
 * Panel de resumen. El contenido cambia según el rol del usuario.
 */

declare(strict_types=1);

final class PanelController extends Controller
{
    public function index(): void
    {
        Auth::requerirLogin();

        $totales = Reporte::totales();
        $mes = Reporte::mes();
        $tanques = Reporte::tanquesConDiagnostico();

        $alertas = [];
        foreach ($tanques as $tanque) {
            if ($tanque['diagnostico']['clase'] === 'alerta') {
                foreach ($tanque['diagnostico']['problemas'] as $problema) {
                    $alertas[] = [
                        'tipo'   => 'agua',
                        'titulo' => $tanque['codigo'] . ' · ' . $tanque['nombre'],
                        'texto'  => $problema,
                        'url'    => url('tanques/editar', ['id' => $tanque['id']]),
                    ];
                }
            }
            if ((float) $tanque['ocupacion_pct'] > 80 && $tanque['estado'] === 'operativo') {
                $alertas[] = [
                    'tipo'   => 'carga',
                    'titulo' => $tanque['codigo'] . ' · ' . $tanque['nombre'],
                    'texto'  => 'Carga biológica en ' . number_format((float) $tanque['ocupacion_pct'], 1, ',', '.') . '% de su capacidad recomendada.',
                    'url'    => url('tanques'),
                ];
            }
        }

        $seguimiento = Pez::seguimiento();
        foreach ($seguimiento as $pez) {
            $alertas[] = [
                'tipo'   => 'pez',
                'titulo' => $pez['nombre'] . ' · ' . $pez['nombre_comun'],
                'texto'  => $pez['estado'] === 'tratamiento'
                    ? 'Está en tratamiento veterinario en ' . ($pez['tanque_nombre'] ?? 'fuera de exhibición') . '.'
                    : 'Permanece en observación en ' . ($pez['tanque_nombre'] ?? 'cuarentena') . '.',
                'url'    => url('peces/editar', ['id' => $pez['id']]),
            ];
        }

        $acciones = '';
        if (Auth::puedeVisitantes()) {
            $acciones .= '<a class="boton" href="' . url('entradas/crear') . '">' . icono('entrada', 17) . ' Vender entrada</a>';
        }
        if (Auth::puedePeces()) {
            $acciones .= '<a class="boton-secundario" href="' . url('peces/crear') . '">' . icono('pez', 17) . ' Registrar pez</a>';
        }

        $this->vista('panel', [
            'titulo'             => 'Resumen del acuario',
            'subtitulo'          => 'Panel de ' . Auth::nombreRol(Auth::rol()),
            'activo'             => 'dashboard',
            'acciones'           => $acciones,
            'totales'            => $totales,
            'mes'                => $mes,
            'tanques'            => $tanques,
            'alertas'            => $alertas,
            'seguimiento'        => $seguimiento,
            'ultimasEntradas'    => Entrada::ultimas(6),
            'ultimosMovimientos' => Finanza::ultimos(5),
        ]);
    }
}
