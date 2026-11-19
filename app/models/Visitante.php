<?php
/**
 * Modelo de visitantes y su historial de taquilla.
 */

declare(strict_types=1);

final class Visitante
{
    public static function todos(?string $busqueda = null): array
    {
        $sql = "SELECT v.*,
                       COUNT(en.id) AS total_entradas,
                       COALESCE(SUM(en.cantidad), 0) AS total_personas,
                       COALESCE(SUM(en.total), 0) AS total_gastado
                FROM visitantes v
                LEFT JOIN entradas en ON en.id_visitante = v.id";

        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE v.nombre LIKE :busqueda OR v.correo LIKE :busqueda OR v.procedencia LIKE :busqueda';
        }

        $sql .= ' GROUP BY v.id ORDER BY v.fecha_registro DESC, v.nombre';

        $consulta = db()->prepare($sql);
        $consulta->execute($busqueda !== null && $busqueda !== '' ? ['busqueda' => '%' . $busqueda . '%'] : []);

        return $consulta->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM visitantes WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function crear(array $datos): void
    {
        db()->prepare(
            'INSERT INTO visitantes (nombre, correo, telefono, procedencia, tipo, fecha_registro)
             VALUES (:nombre, :correo, :telefono, :procedencia, :tipo, :fecha_registro)'
        )->execute($datos);
    }

    public static function actualizar(int $id, array $datos): void
    {
        $datos['id'] = $id;
        db()->prepare(
            'UPDATE visitantes SET nombre = :nombre, correo = :correo, telefono = :telefono,
             procedencia = :procedencia, tipo = :tipo WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM visitantes WHERE id = :id')->execute(['id' => $id]);
    }

    public static function correoExiste(string $correo, int $excepto = 0): bool
    {
        $consulta = db()->prepare('SELECT COUNT(*) FROM visitantes WHERE correo = :correo AND id <> :id');
        $consulta->execute(['correo' => $correo, 'id' => $excepto]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public static function contarEntradas(int $id): int
    {
        $consulta = db()->prepare('SELECT COUNT(*) FROM entradas WHERE id_visitante = :id');
        $consulta->execute(['id' => $id]);

        return (int) $consulta->fetchColumn();
    }

    public static function entradas(int $id): array
    {
        $consulta = db()->prepare(
            'SELECT * FROM entradas WHERE id_visitante = :id ORDER BY fecha DESC, hora DESC LIMIT 8'
        );
        $consulta->execute(['id' => $id]);

        return $consulta->fetchAll();
    }

    public static function resumen(): array
    {
        return db()->query(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(fecha_registro >= DATE_FORMAT(CURDATE(), '%Y-%m-01')), 0) AS nuevos
             FROM visitantes"
        )->fetch();
    }
}
