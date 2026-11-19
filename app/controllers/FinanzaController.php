<?php
/**
 * Controlador de finanzas: libro de ingresos y egresos por mes.
 */

declare(strict_types=1);

final class FinanzaController extends Controller
{
    private const CATEGORIAS_INGRESO = ['Tienda', 'Cafetería', 'Donaciones', 'Visitas guiadas', 'Otros'];
    private const CATEGORIAS_EGRESO = ['Alimento', 'Mantenimiento', 'Planilla', 'Veterinaria', 'Servicios', 'Equipos', 'Otros'];

    public function index(): void
    {
        Auth::requerirRol([1, 3]);

        $mes = (string) ($_GET['mes'] ?? date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = date('Y-m');
        }

        $desde = $mes . '-01';
        $hasta = date('Y-m-t', strtotime($desde));

        $tipo = (string) ($_GET['tipo'] ?? '');
        if (!in_array($tipo, ['ingreso', 'egreso'], true)) {
            $tipo = '';
        }

        $resumen = Finanza::resumen($desde, $hasta);
        $taquilla = Finanza::taquilla($desde, $hasta);

        $this->vista('finanzas/index', [
            'titulo'       => 'Finanzas',
            'subtitulo'    => 'Tesorería del acuario',
            'activo'       => 'finanzas',
            'acciones'     => '<a class="boton" href="' . url('finanzas/crear') . '">' . icono('mas', 17) . ' Registrar movimiento</a>',
            'movimientos'  => Finanza::porMes($desde, $hasta, $tipo),
            'resumen'      => $resumen,
            'taquilla'     => $taquilla,
            'balance'      => (float) $resumen['ingresos'] + $taquilla - (float) $resumen['egresos'],
            'mes'          => $mes,
            'tipo'         => $tipo,
            'nombreMes'    => nombre_mes($mes),
        ]);
    }

    public function crear(): void
    {
        $this->formulario();
    }

    public function editar(): void
    {
        $this->formulario((int) ($_GET['id'] ?? 0));
    }

    private function formulario(int $id = 0): void
    {
        Auth::requerirRol([1, 3]);

        $editando = $id > 0;
        $errores = [];
        $movimiento = [
            'fecha'       => date('Y-m-d'),
            'tipo'        => 'egreso',
            'categoria'   => 'Mantenimiento',
            'descripcion' => '',
            'monto'       => '',
        ];

        if ($editando) {
            $encontrado = Finanza::buscar($id);
            if (!$encontrado) {
                flash('error', 'El movimiento solicitado no existe.');
                redirigir(url('finanzas'));
            }
            $movimiento = $encontrado;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $movimiento['fecha']       = (string) ($_POST['fecha'] ?? '');
            $movimiento['tipo']        = (string) ($_POST['tipo'] ?? 'egreso');
            $movimiento['categoria']   = (string) ($_POST['categoria'] ?? '');
            $movimiento['descripcion'] = trim((string) ($_POST['descripcion'] ?? ''));
            $movimiento['monto']       = (string) ($_POST['monto'] ?? '');

            $fecha = DateTime::createFromFormat('Y-m-d', $movimiento['fecha']);
            if (!$fecha || $fecha->format('Y-m-d') !== $movimiento['fecha']) {
                $errores['fecha'] = 'Indica una fecha válida.';
            }

            if (!in_array($movimiento['tipo'], ['ingreso', 'egreso'], true)) {
                $errores['tipo'] = 'Selecciona si es un ingreso o un egreso.';
            }

            if (!in_array($movimiento['categoria'], self::CATEGORIAS_INGRESO, true)
                && !in_array($movimiento['categoria'], self::CATEGORIAS_EGRESO, true)) {
                $errores['categoria'] = 'Selecciona una categoría de la lista.';
            } elseif ($movimiento['tipo'] === 'ingreso' && !in_array($movimiento['categoria'], self::CATEGORIAS_INGRESO, true)) {
                $errores['categoria'] = 'Esa categoría es de egreso; cambia el tipo o elige una de ingreso.';
            } elseif ($movimiento['tipo'] === 'egreso' && !in_array($movimiento['categoria'], self::CATEGORIAS_EGRESO, true)) {
                $errores['categoria'] = 'Esa categoría es de ingreso; cambia el tipo o elige una de egreso.';
            }

            if ($movimiento['descripcion'] === '' || mb_strlen($movimiento['descripcion']) > 160) {
                $errores['descripcion'] = 'Describe el movimiento en 160 caracteres como máximo.';
            }

            $monto = filter_var($movimiento['monto'], FILTER_VALIDATE_FLOAT);
            if ($monto === false || $monto <= 0 || $monto > 999999) {
                $errores['monto'] = 'El monto debe ser mayor que 0 y menor que un millón.';
            }

            if ($errores === []) {
                $datos = [
                    'fecha'       => $movimiento['fecha'],
                    'tipo'        => $movimiento['tipo'],
                    'categoria'   => $movimiento['categoria'],
                    'descripcion' => $movimiento['descripcion'],
                    'monto'       => round((float) $monto, 2),
                    'id_usuario'  => Auth::id(),
                ];

                if ($editando) {
                    Finanza::actualizar($id, $datos);
                    flash('ok', 'El movimiento se actualizó.');
                } else {
                    Finanza::crear($datos);
                    flash('ok', 'Se registró el movimiento de ' . dinero((float) $monto) . '.');
                }

                redirigir(url('finanzas', ['mes' => substr($datos['fecha'], 0, 7)]));
            }
        }

        $this->vista('finanzas/form', [
            'titulo'      => $editando ? 'Editar movimiento' : 'Nuevo movimiento',
            'subtitulo'   => 'Tesorería del acuario',
            'activo'      => 'finanzas',
            'movimiento'  => $movimiento,
            'errores'     => $errores,
            'editando'    => $editando,
            'id'          => $id,
            'categoriasIngreso' => self::CATEGORIAS_INGRESO,
            'categoriasEgreso'  => self::CATEGORIAS_EGRESO,
        ]);
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1, 3]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $movimiento = Finanza::buscar($id);

        if (!$movimiento) {
            flash('error', 'El movimiento ya no existe.');
            redirigir(url('finanzas'));
        }

        Finanza::eliminar($id);
        flash('ok', 'Se eliminó el movimiento «' . $movimiento['descripcion'] . '».');
        redirigir(url('finanzas', ['mes' => substr($movimiento['fecha'], 0, 7)]));
    }
}
