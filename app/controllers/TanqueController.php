<?php
/**
 * Controlador de tanques: listado, alta, edición y baja.
 */

declare(strict_types=1);

final class TanqueController extends Controller
{
    public function index(): void
    {
        Auth::requerirRol([1, 2]);

        $tanques = Tanque::todos();
        $rangos = Tanque::rangos();

        $totalLitros = 0;
        $totalPeces = 0;
        $totalAlertas = 0;

        foreach ($tanques as $indice => $tanque) {
            $rango = $rangos[(int) $tanque['id']] ?? null;
            $tanque['diagnostico'] = diagnostico_tanque($tanque, $rango);
            $tanque['carga'] = clase_carga((float) $tanque['ocupacion_pct'], $tanque['estado']);
            $tanques[$indice] = $tanque;

            $totalLitros += (int) $tanque['capacidad'];
            $totalPeces += (int) $tanque['total_peces'];
            if ($tanque['diagnostico']['clase'] === 'alerta') {
                $totalAlertas++;
            }
        }

        $this->vista('tanques/index', [
            'titulo'      => 'Tanques',
            'subtitulo'   => 'Colecciones e instalaciones',
            'activo'      => 'tanques',
            'acciones'    => '<a class="boton" href="' . url('tanques/crear') . '">' . icono('mas', 17) . ' Nuevo tanque</a>',
            'tanques'     => $tanques,
            'totalLitros' => $totalLitros,
            'totalPeces'  => $totalPeces,
            'totalAlertas' => $totalAlertas,
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
        Auth::requerirRol([1, 2]);

        $editando = $id > 0;
        $errores = [];
        $tanque = [
            'codigo'      => '',
            'nombre'      => '',
            'habitat'     => 'marino',
            'capacidad'   => '',
            'temperatura' => '',
            'ph'          => '',
            'iluminacion' => 'media',
            'estado'      => 'operativo',
            'notas'       => '',
        ];

        if ($editando) {
            $encontrado = Tanque::buscar($id);
            if (!$encontrado) {
                flash('error', 'El tanque solicitado no existe.');
                redirigir(url('tanques'));
            }
            $tanque = $encontrado;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $tanque['codigo']      = strtoupper(trim((string) ($_POST['codigo'] ?? '')));
            $tanque['nombre']      = trim((string) ($_POST['nombre'] ?? ''));
            $tanque['habitat']     = (string) ($_POST['habitat'] ?? 'marino');
            $tanque['capacidad']   = (string) ($_POST['capacidad'] ?? '');
            $tanque['temperatura'] = (string) ($_POST['temperatura'] ?? '');
            $tanque['ph']          = (string) ($_POST['ph'] ?? '');
            $tanque['iluminacion'] = (string) ($_POST['iluminacion'] ?? 'media');
            $tanque['estado']      = (string) ($_POST['estado'] ?? 'operativo');
            $tanque['notas']       = trim((string) ($_POST['notas'] ?? ''));

            if ($tanque['codigo'] === '' || !preg_match('/^[A-Z0-9-]{1,12}$/', $tanque['codigo'])) {
                $errores['codigo'] = 'Usa entre 1 y 12 caracteres: letras, números o guiones (por ejemplo T-08).';
            } elseif (Tanque::codigoExiste($tanque['codigo'], $id)) {
                $errores['codigo'] = 'Ya existe un tanque con ese código.';
            }

            if ($tanque['nombre'] === '' || mb_strlen($tanque['nombre']) > 70) {
                $errores['nombre'] = 'El nombre es obligatorio y admite hasta 70 caracteres.';
            }

            if (!in_array($tanque['habitat'], ['marino', 'dulce', 'salobre'], true)) {
                $errores['habitat'] = 'Selecciona un tipo de agua válido.';
            }

            $capacidad = filter_var($tanque['capacidad'], FILTER_VALIDATE_INT);
            if ($capacidad === false || $capacidad < 50 || $capacidad > 100000) {
                $errores['capacidad'] = 'La capacidad debe estar entre 50 y 100.000 litros.';
            }

            $temperatura = filter_var($tanque['temperatura'], FILTER_VALIDATE_FLOAT);
            if ($temperatura === false || $temperatura < 5 || $temperatura > 35) {
                $errores['temperatura'] = 'La temperatura debe estar entre 5 y 35 °C.';
            }

            $ph = filter_var($tanque['ph'], FILTER_VALIDATE_FLOAT);
            if ($ph === false || $ph < 5 || $ph > 9.5) {
                $errores['ph'] = 'El pH debe estar entre 5.0 y 9.5.';
            }

            if (!in_array($tanque['iluminacion'], ['baja', 'media', 'alta'], true)) {
                $errores['iluminacion'] = 'Selecciona una iluminación válida.';
            }

            if (!in_array($tanque['estado'], ['operativo', 'mantenimiento', 'cuarentena'], true)) {
                $errores['estado'] = 'Selecciona un estado válido.';
            }

            if (mb_strlen($tanque['notas']) > 255) {
                $errores['notas'] = 'Las notas admiten hasta 255 caracteres.';
            }

            if ($editando && $capacidad !== false) {
                $centimetros = Tanque::cargaCm($id);
                if ($capacidad < $centimetros * 10) {
                    $errores['capacidad'] = 'Con ' . $capacidad . ' litros no alcanza para los ' . $centimetros
                        . ' cm de pez que ya viven aquí (mínimo ' . number_format($centimetros * 10, 0, ',', '.') . ' litros).';
                }
            }

            if ($errores === []) {
                $datos = [
                    'codigo'      => $tanque['codigo'],
                    'nombre'      => $tanque['nombre'],
                    'habitat'     => $tanque['habitat'],
                    'capacidad'   => $capacidad,
                    'temperatura' => $temperatura,
                    'ph'          => $ph,
                    'iluminacion' => $tanque['iluminacion'],
                    'estado'      => $tanque['estado'],
                    'notas'       => $tanque['notas'] === '' ? null : $tanque['notas'],
                ];

                if ($editando) {
                    Tanque::actualizar($id, $datos);
                    flash('ok', 'El tanque ' . $datos['codigo'] . ' se actualizó correctamente.');
                } else {
                    Tanque::crear($datos);
                    flash('ok', 'Se registró el tanque ' . $datos['codigo'] . '.');
                }

                redirigir(url('tanques'));
            }
        }

        $this->vista('tanques/form', [
            'titulo'       => $editando ? 'Editar tanque' : 'Nuevo tanque',
            'subtitulo'    => $editando ? 'Instalación ' . $tanque['codigo'] : 'Colecciones e instalaciones',
            'activo'       => 'tanques',
            'tanque'       => $tanque,
            'errores'      => $errores,
            'editando'     => $editando,
            'id'           => $id,
            'ocupacion'    => $editando ? Tanque::ocupacion($id) : null,
            'pecesTanque'  => $editando ? Pez::porTanque($id) : [],
        ]);
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1, 2]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $tanque = Tanque::buscar($id);

        if (!$tanque) {
            flash('error', 'El tanque ya no existe.');
            redirigir(url('tanques'));
        }

        Tanque::eliminar($id);
        flash('ok', 'Se eliminó el tanque ' . $tanque['codigo'] . ' · ' . $tanque['nombre'] . '. Sus peces quedaron sin tanque asignado.');
        redirigir(url('tanques'));
    }
}
