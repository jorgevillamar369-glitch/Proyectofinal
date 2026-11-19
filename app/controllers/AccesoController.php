<?php
/**
 * Acceso del personal: formulario, validación y cierre de sesión.
 */

declare(strict_types=1);

final class AccesoController extends Controller
{
    public function index(): void
    {
        if (Auth::autenticado()) {
            redirigir(url('panel'));
        }

        $this->vista('acceso', ['error' => '', 'correo' => ''], 'acceso');
    }

    public function entrar(): void
    {
        if (Auth::autenticado()) {
            redirigir(url('panel'));
        }

        $error = '';
        $correo = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            verificar_csrf();

            $correo = trim((string) ($_POST['correo'] ?? ''));
            $clave = (string) ($_POST['contrasena'] ?? '');

            if ($correo === '' || $clave === '') {
                $error = 'Escribe tu correo y tu contraseña.';
            } else {
                $usuario = Usuario::porCorreo($correo);

                if ($usuario && (int) $usuario['activo'] === 1 && password_verify($clave, $usuario['contrasena'])) {
                    Auth::iniciar($usuario);
                    flash('ok', 'Hola de nuevo, ' . $usuario['nombre'] . '.');
                    redirigir(url('panel'));
                }

                $error = 'El correo o la contraseña no coinciden con ninguna cuenta activa.';
            }
        }

        $this->vista('acceso', ['error' => $error, 'correo' => $correo], 'acceso');
    }

    public function salir(): void
    {
        Auth::cerrar();
        flash('ok', 'Tu sesión se cerró correctamente.');
        redirigir(url('acceso'));
    }
}
