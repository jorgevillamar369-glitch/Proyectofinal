<?php
/**
 * Controlador de peces: listado, alta, edición y baja.
 */

declare(strict_types=1);

final class PezController extends Controller
{
    public function index(): void
    {
        Auth::requerirRol([1, 2]);

        $busqueda = trim((string) ($_GET['q'] ?? ''));

        $this->vista('peces/index', [
            'titulo'    => 'Peces',
            'subtitulo' => 'Colección biológica',
            'activo'    => 'peces',
            'acciones'  => '<a class="boton" href="' . url('peces/crear') . '">' . icono('mas', 17) . ' Registrar pez</a>',
            'peces'     => Pez::todos($busqueda),
            'resumen'   => Pez::resumen(),
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
        Auth::requerirRol([1, 2]);

        $editando = $id > 0;
        $errores = [];
        $especies = Especie::todas();
        $tanques = Tanque::todos();

        $pez = [
            'nombre'        => '',
            'id_especie'    => '',
            'id_tanque'     => '',
            'tamanio_cm'    => '',
            'edad_meses'    => '',
            'sexo'          => 'indefinido',
            'fecha_ingreso' => date('Y-m-d'),
            'estado'        => 'observacion',
        ];

        if ($editando) {
            $encontrado = Pez::buscar($id);
            if (!$encontrado) {
                flash('error', 'La ficha del pez no existe.');
                redirigir(url('peces'));
            }
            $pez = $encontrado;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $pez['nombre']        = trim((string) ($_POST['nombre'] ?? ''));
            $pez['id_especie']    = (string) ($_POST['id_especie'] ?? '');
            $pez['id_tanque']     = (string) ($_POST['id_tanque'] ?? '');
            $pez['tamanio_cm']    = (string) ($_POST['tamanio_cm'] ?? '');
            $pez['edad_meses']    = (string) ($_POST['edad_meses'] ?? '');
            $pez['sexo']          = (string) ($_POST['sexo'] ?? 'indefinido');
            $pez['fecha_ingreso'] = (string) ($_POST['fecha_ingreso'] ?? '');
            $pez['estado']        = (string) ($_POST['estado'] ?? 'saludable');

            if ($pez['nombre'] === '' || mb_strlen($pez['nombre']) > 60) {
                $errores['nombre'] = 'El nombre es obligatorio y admite hasta 60 caracteres.';
            }

            $especieElegida = null;
            foreach ($especies as $especie) {
                if ((int) $especie['id'] === (int) $pez['id_especie']) {
                    $especieElegida = $especie;
                    break;
                }
            }
            if (!$especieElegida) {
                $errores['id_especie'] = 'Selecciona una especie registrada.';
            }

            $tanqueElegido = null;
            if ($pez['id_tanque'] !== '') {
                foreach ($tanques as $tanque) {
                    if ((int) $tanque['id'] === (int) $pez['id_tanque']) {
                        $tanqueElegido = $tanque;
                        break;
                    }
                }
                if (!$tanqueElegido) {
                    $errores['id_tanque'] = 'El tanque elegido no existe.';
                }
            }

            $tamanio = filter_var($pez['tamanio_cm'], FILTER_VALIDATE_INT);
            if ($tamanio === false || $tamanio < 1 || $tamanio > 500) {
                $errores['tamanio_cm'] = 'Indica un tamaño entre 1 y 500 cm.';
                $tamanio = 0;
            } elseif ($especieElegida && $tamanio > (int) $especieElegida['tamanio_max_cm']) {
                $errores['tamanio_cm'] = 'El tamaño supera el máximo de la especie ('
                    . (int) $especieElegida['tamanio_max_cm'] . ' cm). Verifica la medición.';
            }

            $edad = filter_var($pez['edad_meses'], FILTER_VALIDATE_INT);
            if ($edad === false || $edad < 0 || $edad > 600) {
                $errores['edad_meses'] = 'La edad debe estar entre 0 y 600 meses.';
                $edad = 0;
            }

            if (!in_array($pez['sexo'], ['macho', 'hembra', 'indefinido'], true)) {
                $errores['sexo'] = 'Selecciona un sexo válido.';
            }

            $fecha = DateTime::createFromFormat('Y-m-d', $pez['fecha_ingreso']);
            if (!$fecha || $fecha->format('Y-m-d') !== $pez['fecha_ingreso']) {
                $errores['fecha_ingreso'] = 'Indica una fecha de ingreso válida.';
            } elseif ($pez['fecha_ingreso'] > date('Y-m-d')) {
                $errores['fecha_ingreso'] = 'La fecha de ingreso no puede estar en el futuro.';
            }

            if (!in_array($pez['estado'], ['saludable', 'observacion', 'tratamiento', 'trasladado'], true)) {
                $errores['estado'] = 'Selecciona un estado válido.';
            }

            if ($tanqueElegido && $tamanio > 0 && $pez['estado'] !== 'trasladado') {
                $centimetros = Pez::cargaEnTanque((int) $pez['id_tanque'], $id);

                if ($centimetros + $tamanio > (int) $tanqueElegido['capacidad'] / 10) {
                    $disponible = max(0, (int) $tanqueElegido['capacidad'] / 10 - $centimetros);
                    $errores['id_tanque'] = 'El tanque ' . $tanqueElegido['codigo'] . ' solo admite '
                        . number_format($disponible, 0, ',', '.') . ' cm más. El ejemplar mide ' . $tamanio . ' cm.';
                }
            }

            if ($errores === []) {
                $datos = [
                    'nombre'        => $pez['nombre'],
                    'id_especie'    => (int) $pez['id_especie'],
                    'id_tanque'     => $pez['id_tanque'] === '' ? null : (int) $pez['id_tanque'],
                    'tamanio_cm'    => $tamanio,
                    'edad_meses'    => $edad,
                    'sexo'          => $pez['sexo'],
                    'fecha_ingreso' => $pez['fecha_ingreso'],
                    'estado'        => $pez['estado'],
                ];

                if ($editando) {
                    Pez::actualizar($id, $datos);
                    flash('ok', 'La ficha de ' . $datos['nombre'] . ' se actualizó.');
                } else {
                    Pez::crear($datos);
                    flash('ok', 'Se registró a ' . $datos['nombre'] . ' en la colección.');
                }

                redirigir(url('peces'));
            }
        }

        $datosEspecies = [];
        foreach ($especies as $especie) {
            $datosEspecies[(int) $especie['id']] = [
                'nombre_cientifico' => $especie['nombre_cientifico'],
                'habitat_texto'     => texto_habitat($especie['habitat']),
                'temp_min'          => (float) $especie['temp_min'],
                'temp_max'          => (float) $especie['temp_max'],
                'ph_min'            => (float) $especie['ph_min'],
                'ph_max'            => (float) $especie['ph_max'],
                'tamanio_max_cm'    => (int) $especie['tamanio_max_cm'],
                'dieta'             => $especie['dieta'],
            ];
        }

        $this->vista('peces/form', [
            'titulo'        => $editando ? 'Editar ficha' : 'Registrar pez',
            'subtitulo'     => 'Colección biológica',
            'activo'        => 'peces',
            'pez'           => $pez,
            'errores'       => $errores,
            'editando'      => $editando,
            'id'            => $id,
            'especies'      => $especies,
            'tanques'       => $tanques,
            'datosEspecies' => json_encode($datosEspecies, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1, 2]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $pez = Pez::buscar($id);

        if (!$pez) {
            flash('error', 'La ficha ya no existe.');
            redirigir(url('peces'));
        }

        Pez::eliminar($id);
        flash('ok', 'Se eliminó la ficha de ' . $pez['nombre'] . '.');
        redirigir(url('peces'));
    }
}
