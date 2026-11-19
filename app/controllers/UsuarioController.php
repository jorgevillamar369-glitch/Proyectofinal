<?php
/**
 * Controlador del personal con acceso al panel (solo administrador).
 */

declare(strict_types=1);

final class UsuarioController extends Controller
{
    public function index(): void
    {
        Auth::requerirRol([1]);

        $this->vista('usuarios/index', [
            'titulo'    => 'Personal',
            'subtitulo' => 'Administración del sistema',
            'activo'    => 'usuarios',
            'acciones'  => '<a class="boton" href="' . url('usuarios/crear') . '">' . icono('mas', 17) . ' Nuevo usuario</a>',
            'usuarios'  => Usuario::todos(),
            'resumen'   => Usuario::resumen(),
            'totalRoles' => count(Usuario::roles()),
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
        Auth::requerirRol([1]);

        $editando = $id > 0;
        $errores = [];
        $roles = Usuario::roles();

        $cuenta = [
            'nombre' => '',
            'correo' => '',
            'id_rol' => '2',
            'activo' => '1',
        ];

        if ($editando) {
            $encontrado = Usuario::buscar($id);
            if (!$encontrado) {
                flash('error', 'La cuenta solicitada no existe.');
                redirigir(url('usuarios'));
            }
            $cuenta = $encontrado;
        }

        $esMiCuenta = $editando && $id === Auth::id();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $cuenta['nombre'] = trim((string) ($_POST['nombre'] ?? ''));
            $cuenta['correo'] = strtolower(trim((string) ($_POST['correo'] ?? '')));
            $cuenta['id_rol'] = (string) ($_POST['id_rol'] ?? '');
            $cuenta['activo'] = isset($_POST['activo']) ? '1' : '0';
            $clave = (string) ($_POST['contrasena'] ?? '');

            if ($cuenta['nombre'] === '' || mb_strlen($cuenta['nombre']) > 70) {
                $errores['nombre'] = 'El nombre es obligatorio y admite hasta 70 caracteres.';
            }

            if (!filter_var($cuenta['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($cuenta['correo']) > 100) {
                $errores['correo'] = 'Escribe un correo válido.';
            } elseif (Usuario::correoExiste($cuenta['correo'], $id)) {
                $errores['correo'] = 'Ese correo ya tiene una cuenta en el sistema.';
            }

            $rolElegido = null;
            foreach ($roles as $rol) {
                if ((int) $rol['id'] === (int) $cuenta['id_rol']) {
                    $rolElegido = $rol;
                    break;
                }
            }
            if (!$rolElegido) {
                $errores['id_rol'] = 'Selecciona un perfil válido.';
            }

            if ($esMiCuenta && (int) $cuenta['id_rol'] !== 1) {
                $errores['id_rol'] = 'No puedes quitarte a ti mismo el perfil de administrador.';
            }
            if ($esMiCuenta && $cuenta['activo'] === '0') {
                $errores['activo'] = 'No puedes desactivar tu propia cuenta.';
            }

            if ((!$editando || $clave !== '') && mb_strlen($clave) < 8) {
                $errores['contrasena'] = 'La contraseña debe tener al menos 8 caracteres.';
            }

            if ($errores === []) {
                $hash = $clave !== '' ? password_hash($clave, PASSWORD_DEFAULT) : null;

                if ($editando) {
                    Usuario::actualizar($id, [
                        'nombre' => $cuenta['nombre'],
                        'correo' => $cuenta['correo'],
                        'id_rol' => (int) $cuenta['id_rol'],
                        'activo' => $cuenta['activo'],
                    ], $hash);
                    flash('ok', 'La cuenta de ' . $cuenta['nombre'] . ' se actualizó.');
                } else {
                    Usuario::crear([
                        'nombre'     => $cuenta['nombre'],
                        'correo'     => $cuenta['correo'],
                        'contrasena' => $hash,
                        'id_rol'     => (int) $cuenta['id_rol'],
                        'activo'     => $cuenta['activo'],
                    ]);
                    flash('ok', 'Se creó la cuenta de ' . $cuenta['nombre'] . '.');
                }

                redirigir(url('usuarios'));
            }
        }

        $this->vista('usuarios/form', [
            'titulo'     => $editando ? 'Editar cuenta' : 'Nueva cuenta',
            'subtitulo'  => 'Administración del sistema',
            'activo'     => 'usuarios',
            'cuenta'     => $cuenta,
            'errores'    => $errores,
            'editando'   => $editando,
            'id'         => $id,
            'roles'      => $roles,
        ]);
    }

    public function eliminar(): void
    {
        Auth::requerirRol([1]);
        exigir_post();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id === Auth::id()) {
            flash('error', 'No puedes eliminar tu propia cuenta.');
            redirigir(url('usuarios'));
        }

        $cuenta = Usuario::buscar($id);
        if (!$cuenta) {
            flash('error', 'La cuenta ya no existe.');
            redirigir(url('usuarios'));
        }

        if ((int) $cuenta['id_rol'] === 1 && Usuario::adminsActivos() <= 1) {
            flash('error', 'No se puede eliminar al último administrador activo del sistema.');
            redirigir(url('usuarios'));
        }

        Usuario::eliminar($id);
        flash('ok', 'Se eliminó la cuenta de ' . $cuenta['nombre'] . ' (' . $cuenta['correo'] . ').');
        redirigir(url('usuarios'));
    }
}
