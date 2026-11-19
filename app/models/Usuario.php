<?php
/**
 * Modelo de usuarios del personal.
 */

declare(strict_types=1);

final class Usuario
{
    public static function porCorreo(string $correo): ?array
    {
        $consulta = db()->prepare(
            'SELECT u.id, u.nombre, u.correo, u.contrasena, u.id_rol, u.activo, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.id_rol
             WHERE u.correo = :correo
             LIMIT 1'
        );
        $consulta->execute(['correo' => $correo]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function todos(): array
    {
        return db()->query(
            'SELECT u.*, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.id_rol
             ORDER BY u.activo DESC, r.id, u.nombre'
        )->fetchAll();
    }

    public static function roles(): array
    {
        return db()->query('SELECT * FROM roles ORDER BY id')->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM usuarios WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function crear(array $datos): void
    {
        db()->prepare(
            'INSERT INTO usuarios (nombre, correo, contrasena, id_rol, activo)
             VALUES (:nombre, :correo, :contrasena, :id_rol, :activo)'
        )->execute($datos);
    }

    public static function actualizar(int $id, array $datos, ?string $contrasena = null): void
    {
        $datos['id'] = $id;

        if ($contrasena !== null && $contrasena !== '') {
            $datos['contrasena'] = $contrasena;
            db()->prepare(
                'UPDATE usuarios SET nombre = :nombre, correo = :correo, id_rol = :id_rol,
                 activo = :activo, contrasena = :contrasena WHERE id = :id'
            )->execute($datos);
            return;
        }

        db()->prepare(
            'UPDATE usuarios SET nombre = :nombre, correo = :correo, id_rol = :id_rol,
             activo = :activo WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM usuarios WHERE id = :id')->execute(['id' => $id]);
    }

    public static function correoExiste(string $correo, int $excepto = 0): bool
    {
        $consulta = db()->prepare('SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id <> :id');
        $consulta->execute(['correo' => $correo, 'id' => $excepto]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public static function adminsActivos(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM usuarios WHERE id_rol = 1 AND activo = 1')->fetchColumn();
    }

    public static function resumen(): array
    {
        return db()->query('SELECT COUNT(*) AS total, COALESCE(SUM(activo = 1), 0) AS activos FROM usuarios')->fetch();
    }
}
