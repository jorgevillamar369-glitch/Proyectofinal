<?php
/**
 * Modelo del libro de finanzas.
 */

declare(strict_types=1);

final class Finanza
{
    public static function porMes(string $desde, string $hasta, string $tipo = ''): array
    {
        $sql = 'SELECT f.*, u.nombre AS responsable
                FROM finanzas f
                LEFT JOIN usuarios u ON u.id = f.id_usuario
                WHERE f.fecha BETWEEN :desde AND :hasta';
        $parametros = ['desde' => $desde, 'hasta' => $hasta];

        if ($tipo === 'ingreso' || $tipo === 'egreso') {
            $sql .= ' AND f.tipo = :tipo';
            $parametros['tipo'] = $tipo;
        }

        $sql .= ' ORDER BY f.fecha DESC, f.id DESC';

        $consulta = db()->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM finanzas WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function crear(array $datos): void
    {
        db()->prepare(
            'INSERT INTO finanzas (fecha, tipo, categoria, descripcion, monto, id_usuario)
             VALUES (:fecha, :tipo, :categoria, :descripcion, :monto, :id_usuario)'
        )->execute($datos);
    }

    public static function actualizar(int $id, array $datos): void
    {
        $datos['id'] = $id;
        db()->prepare(
            'UPDATE finanzas SET fecha = :fecha, tipo = :tipo, categoria = :categoria,
             descripcion = :descripcion, monto = :monto, id_usuario = :id_usuario
             WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM finanzas WHERE id = :id')->execute(['id' => $id]);
    }

    public static function resumen(string $desde, string $hasta): array
    {
        $consulta = db()->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN monto END), 0) AS ingresos,
                COALESCE(SUM(CASE WHEN tipo = 'egreso' THEN monto END), 0) AS egresos
             FROM finanzas WHERE fecha BETWEEN :desde AND :hasta"
        );
        $consulta->execute(['desde' => $desde, 'hasta' => $hasta]);

        return $consulta->fetch();
    }

    public static function taquilla(string $desde, string $hasta): float
    {
        $consulta = db()->prepare(
            'SELECT COALESCE(SUM(total), 0) FROM entradas WHERE fecha BETWEEN :desde AND :hasta'
        );
        $consulta->execute(['desde' => $desde, 'hasta' => $hasta]);

        return (float) $consulta->fetchColumn();
    }

    public static function ultimos(int $limite = 5): array
    {
        $consulta = db()->prepare('SELECT * FROM finanzas ORDER BY fecha DESC, id DESC LIMIT :limite');
        $consulta->bindValue('limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }
}
