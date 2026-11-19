<?php
/**
 * Sesión, permisos y roles del personal.
 *
 * Roles: 1 = Administrador, 2 = Encargado de peces, 3 = Encargado de visitantes.
 */

declare(strict_types=1);

final class Auth
{
    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function autenticado(): bool
    {
        return self::usuario() !== null;
    }

    public static function id(): int
    {
        return (int) (self::usuario()['id'] ?? 0);
    }

    public static function rol(): int
    {
        return (int) (self::usuario()['id_rol'] ?? 0);
    }

    public static function esAdmin(): bool
    {
        return self::rol() === 1;
    }

    public static function puedePeces(): bool
    {
        return in_array(self::rol(), [1, 2], true);
    }

    public static function puedeVisitantes(): bool
    {
        return in_array(self::rol(), [1, 3], true);
    }

    public static function nombreRol(int $idRol): string
    {
        return [
            1 => 'Administrador',
            2 => 'Encargado de peces',
            3 => 'Encargado de visitantes',
        ][$idRol] ?? 'Sin rol';
    }

    public static function iniciar(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'      => (int) $usuario['id'],
            'nombre'  => $usuario['nombre'],
            'correo'  => $usuario['correo'],
            'id_rol'  => (int) $usuario['id_rol'],
            'rol'     => $usuario['rol'],
            'ingreso' => date('Y-m-d H:i:s'),
        ];
    }

    public static function cerrar(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
        }
        session_destroy();
    }

    public static function requerirLogin(): void
    {
        if (!self::autenticado()) {
            flash('aviso', 'Inicia sesión para entrar al sistema.');
            redirigir(url('acceso'));
        }
    }

    public static function requerirRol(array $roles): void
    {
        self::requerirLogin();

        if (!in_array(self::rol(), $roles, true)) {
            flash('error', 'Tu perfil no tiene permiso para abrir esa sección.');
            redirigir(url('panel'));
        }
    }
}
