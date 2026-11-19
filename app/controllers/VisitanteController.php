<?php
/**
 * Controlador de visitantes.
 */

declare(strict_types=1);

final class VisitanteController extends Controller
{
    public function index(): void
    {
        Auth::requerirRol([1, 3]);

        $busqueda = trim((string) ($_GET['q'] ?? ''));
        $visitantes = Visitante::todos($busqueda);

        $totalEntradas = 0;
        foreach ($visitantes as $visitante) {
            $totalEntradas += (int) $visitante['total_entradas'];
        }

        $this->vista('visitantes/index', [
            'titulo'         => 'Visitantes',
            'subtitulo'      => 'Taquilla y atención al público',
            'activo'         => 'visitantes',
            'acciones'       => '<a class="boton" href="' . url('visitantes/crear') . '">' . icono('mas', 17) . ' Nuevo visitante</a>',
            'visitantes'     => $visitantes,
            'resumen'        => Visitante::resumen(),
            'busqueda'       => $busqueda,
            'totalEntradas'  => $totalEntradas,
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
        $visitante = [
            'nombre'         => '',
            'correo'         => '',
            'telefono'       => '',
            'procedencia'    => 'Quito',
            'tipo'           => 'general',
            'fecha_registro' => date('Y-m-d'),
        ];

        if ($editando) {
            $encontrado = Visitante::buscar($id);
            if (!$encontrado) {
                flash('error', 'El visitante solicitado no existe.');
                redirigir(url('visitantes'));
            }
            $visitante = $encontrado;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $visitante['nombre']      = trim((string) ($_POST['nombre'] ?? ''));
            $visitante['correo']      = strtolower(trim((string) ($_POST['correo'] ?? '')));
            $visitante['telefono']    = trim((string) ($_POST['telefono'] ?? ''));
            $visitante['procedencia'] = trim((string) ($_POST['procedencia'] ?? ''));
            $visitante['tipo']        = (string) ($_POST['tipo'] ?? 'general');

            if ($visitante['nombre'] === '' || mb_strlen($visitante['nombre']) > 80) {
                $errores['nombre'] = 'El nombre es obligatorio y admite hasta 80 caracteres.';
            }

            if (!filter_var($visitante['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($visitante['correo']) > 100) {
                $errores['correo'] = 'Escribe un correo válido.';
            } elseif (Visitante::correoExiste($visitante['correo'], $id)) {
                $errores['correo'] = 'Ese correo ya está registrado con otro visitante.';
            }

            if ($visitante['telefono'] !== '' && !preg_match('/^[0-9+\s-]{7,15}$/', $visitante['telefono'])) {
                $errores['telefono'] = 'Usa solo números, espacios o guiones (entre 7 y 15 caracteres).';
            }

            if ($visitante['procedencia'] === '' || mb_strlen($visitante['procedencia']) > 80) {
                $errores['procedencia'] = 'Indica la ciudad de procedencia.';
            }

            if (!in_array($visitante['tipo'], ['general', 'estudiante', 'nino', 'adulto_mayor'], true)) {
                $errores['tipo'] = 'Selecciona una tarifa válida.';
            }

            if ($errores === []) {
                $datos = [
                    'nombre'      => $visitante['nombre'],
                    'correo'      => $visitante['correo'],
                    'telefono'    => $visitante['telefono'] === '' ? null : $visitante['telefono'],
                    'procedencia' => $visitante['procedencia'],
                    'tipo'        => $visitante['tipo'],
                ];

                if ($editando) {
                    Visitante::actualizar($id, $datos);
                    flash('ok', 'Se actualizaron los datos de ' . $datos['nombre'] . '.');
                } else {
                    $datos['fecha_registro'] = date('Y-m-d');
                    Visitante::crear($datos);
                    flash('ok', 'Se registró a ' . $datos['nombre'] . ' como visitante.');
                }

                redirigir(url('visitantes'));
            }
        }

        $this->vista('visitantes/form', [
            'titulo'     => $editando ? 'Editar visitante' : 'Nuevo visitante',
            'subtitulo'  => 'Taquilla y atención al público',
            'activo'     => 'visitantes',
            'visitante'  => $visitante,
            'errores'    => $errores,
            'editando'   => $editando,
            'id'         => $id,
            'historial'  => $editando ? Visitante::entradas($id) : [],
        ]);
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1, 3]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $visitante = Visitante::buscar($id);

        if (!$visitante) {
            flash('error', 'El visitante ya no existe.');
            redirigir(url('visitantes'));
        }

        if (Visitante::contarEntradas($id) > 0) {
            flash('error', 'No se puede eliminar a ' . $visitante['nombre'] . ': tiene entradas vendidas. Elimina primero esas ventas si corresponde.');
            redirigir(url('visitantes'));
        }

        Visitante::eliminar($id);
        flash('ok', 'Se eliminó el registro de ' . $visitante['nombre'] . '.');
        redirigir(url('visitantes'));
    }
}
