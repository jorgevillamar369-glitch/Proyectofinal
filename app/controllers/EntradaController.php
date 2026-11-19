<?php
/**
 * Controlador de taquilla: venta, edición, ticket y anulación de entradas.
 */

declare(strict_types=1);

final class EntradaController extends Controller
{
    public function index(): void
    {
        Auth::requerirRol([1, 3]);

        $busqueda = trim((string) ($_GET['q'] ?? ''));

        $this->vista('entradas/index', [
            'titulo'    => 'Entradas',
            'subtitulo' => 'Taquilla del acuario',
            'activo'    => 'entradas',
            'acciones'  => '<a class="boton" href="' . url('entradas/crear') . '">' . icono('mas', 17) . ' Vender entrada</a>',
            'entradas'  => Entrada::todas($busqueda),
            'resumen'   => Entrada::resumenMes(),
            'busqueda'  => $busqueda,
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
        $visitantes = Visitante::todos();

        if ($visitantes === []) {
            flash('aviso', 'Primero registra un visitante para poder vender entradas.');
            redirigir(url('visitantes/crear'));
        }

        $entrada = [
            'id_visitante' => (string) ($_GET['visitante'] ?? ($visitantes[0]['id'] ?? '')),
            'fecha'        => date('Y-m-d'),
            'hora'         => date('H:i'),
            'cantidad'     => '1',
            'tarifa'       => 'general',
            'metodo_pago'  => 'efectivo',
        ];

        if ($editando) {
            $encontrada = Entrada::buscar($id);
            if (!$encontrada) {
                flash('error', 'La entrada solicitada no existe.');
                redirigir(url('entradas'));
            }
            $entrada = $encontrada;
            $entrada['hora'] = substr((string) $encontrada['hora'], 0, 5);
        } else {
            foreach ($visitantes as $visitante) {
                if ((string) $visitante['id'] === (string) $entrada['id_visitante']) {
                    $entrada['tarifa'] = $visitante['tipo'];
                    break;
                }
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $entrada['id_visitante'] = (string) ($_POST['id_visitante'] ?? '');
            $entrada['fecha']        = (string) ($_POST['fecha'] ?? '');
            $entrada['hora']         = (string) ($_POST['hora'] ?? '');
            $entrada['cantidad']     = (string) ($_POST['cantidad'] ?? '1');
            $entrada['tarifa']       = (string) ($_POST['tarifa'] ?? 'general');
            $entrada['metodo_pago']  = (string) ($_POST['metodo_pago'] ?? 'efectivo');

            $visitanteElegido = null;
            foreach ($visitantes as $visitante) {
                if ((int) $visitante['id'] === (int) $entrada['id_visitante']) {
                    $visitanteElegido = $visitante;
                    break;
                }
            }
            if (!$visitanteElegido) {
                $errores['id_visitante'] = 'Selecciona un visitante registrado.';
            }

            $fecha = DateTime::createFromFormat('Y-m-d', $entrada['fecha']);
            if (!$fecha || $fecha->format('Y-m-d') !== $entrada['fecha']) {
                $errores['fecha'] = 'Indica una fecha válida.';
            } elseif ($entrada['fecha'] > date('Y-m-d')) {
                $errores['fecha'] = 'La fecha de la venta no puede estar en el futuro.';
            }

            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $entrada['hora'])) {
                $errores['hora'] = 'Indica una hora válida en formato de 24 horas.';
            }

            $cantidad = filter_var($entrada['cantidad'], FILTER_VALIDATE_INT);
            if ($cantidad === false || $cantidad < 1 || $cantidad > 20) {
                $errores['cantidad'] = 'La cantidad debe estar entre 1 y 20 personas por venta.';
                $cantidad = 1;
            }

            $precios = tarifas();
            if (!array_key_exists($entrada['tarifa'], $precios)) {
                $errores['tarifa'] = 'Selecciona una tarifa válida.';
            }

            if (!in_array($entrada['metodo_pago'], ['efectivo', 'tarjeta', 'transferencia'], true)) {
                $errores['metodo_pago'] = 'Selecciona un método de pago válido.';
            }

            if ($errores === []) {
                $precioUnitario = $precios[$entrada['tarifa']];
                $datos = [
                    'id_visitante'    => (int) $entrada['id_visitante'],
                    'fecha'           => $entrada['fecha'],
                    'hora'            => $entrada['hora'] . ':00',
                    'cantidad'        => $cantidad,
                    'tarifa'          => $entrada['tarifa'],
                    'precio_unitario' => $precioUnitario,
                    'total'           => $precioUnitario * $cantidad,
                    'metodo_pago'     => $entrada['metodo_pago'],
                    'id_usuario'      => Auth::id(),
                ];

                if ($editando) {
                    Entrada::actualizar($id, $datos);
                    flash('ok', 'La entrada se actualizó. Nuevo total: ' . dinero($datos['total']) . '.');
                    redirigir(url('entradas/ticket', ['id' => $id]));
                }

                $datos['codigo'] = Entrada::siguienteCodigo();
                $nuevoId = Entrada::crear($datos);

                flash('ok', 'Venta registrada por ' . dinero($datos['total']) . '. Código ' . $datos['codigo'] . '.');
                redirigir(url('entradas/ticket', ['id' => $nuevoId]));
            }
        }

        $this->vista('entradas/form', [
            'titulo'       => $editando ? 'Editar entrada' : 'Vender entrada',
            'subtitulo'    => 'Taquilla del acuario',
            'activo'       => 'entradas',
            'entrada'      => $entrada,
            'errores'      => $errores,
            'editando'     => $editando,
            'id'           => $id,
            'visitantes'   => $visitantes,
            'datosTarifas' => json_encode(tarifas(), JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function ticket(): void
    {
        Auth::requerirRol([1, 3]);

        $id = (int) ($_GET['id'] ?? 0);
        $entrada = Entrada::buscar($id);

        if (!$entrada) {
            flash('error', 'La entrada solicitada no existe.');
            redirigir(url('entradas'));
        }

        $this->vista('entradas/ticket', [
            'tituloPagina' => 'Ticket ' . $entrada['codigo'] . ' · Aqua Viva',
            'entrada'      => $entrada,
        ], 'ticket');
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1, 3]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $entrada = Entrada::buscar($id);

        if (!$entrada) {
            flash('error', 'La entrada ya no existe.');
            redirigir(url('entradas'));
        }

        Entrada::eliminar($id);
        flash('ok', 'Se anuló la entrada ' . $entrada['codigo'] . ' de ' . $entrada['visitante']
            . ' por ' . dinero($entrada['total']) . '.');
        redirigir(url('entradas'));
    }
}
