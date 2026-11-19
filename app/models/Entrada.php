<?php
/**
 * Modelo de entradas vendidas en taquilla.
 */

declare(strict_types=1);

final class Entrada
{
    public static function todas(?string $busqueda = null): array
    {
        $sql = "SELECT en.*, vi.nombre AS visitante, vi.correo, u.nombre AS vendedor
                FROM entradas en
                INNER JOIN visitantes vi ON vi.id = en.id_visitante
                LEFT JOIN usuarios u ON u.id = en.id_usuario";

        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE en.codigo LIKE :busqueda OR vi.nombre LIKE :busqueda OR vi.correo LIKE :busqueda';
        }

        $sql .= ' ORDER BY en.fecha DESC, en.hora DESC, en.id DESC';

        $consulta = db()->prepare($sql);
        $consulta->execute($busqueda !== null && $busqueda !== '' ? ['busqueda' => '%' . $busqueda . '%'] : []);

        return $consulta->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare(
            'SELECT en.*, vi.nombre AS visitante, vi.correo, u.nombre AS vendedor
             FROM entradas en
             INNER JOIN visitantes vi ON vi.id = en.id_visitante
             LEFT JOIN usuarios u ON u.id = en.id_usuario
             WHERE en.id = :id'
        );
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function crear(array $datos): int
    {
        db()->prepare(
            'INSERT INTO entradas (codigo, id_visitante, fecha, hora, cantidad, tarifa,
             precio_unitario, total, metodo_pago, id_usuario)
             VALUES (:codigo, :id_visitante, :fecha, :hora, :cantidad, :tarifa,
             :precio_unitario, :total, :metodo_pago, :id_usuario)'
        )->execute($datos);

        return (int) db()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $datos['id'] = $id;
        db()->prepare(
            'UPDATE entradas SET id_visitante = :id_visitante, fecha = :fecha, hora = :hora,
             cantidad = :cantidad, tarifa = :tarifa, precio_unitario = :precio_unitario,
             total = :total, metodo_pago = :metodo_pago, id_usuario = :id_usuario
             WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM entradas WHERE id = :id')->execute(['id' => $id]);
    }

    public static function siguienteCodigo(): string
    {
        $ultimo = (int) db()->query(
            'SELECT COALESCE(MAX(CAST(SUBSTRING(codigo, 4) AS UNSIGNED)), 100) FROM entradas'
        )->fetchColumn();

        return 'AV-' . str_pad((string) ($ultimo + 1), 6, '0', STR_PAD_LEFT);
    }

    public static function resumenMes(): array
    {
        return db()->query(
            "SELECT COUNT(*) AS ventas,
                    COALESCE(SUM(cantidad), 0) AS personas,
                    COALESCE(SUM(total), 0) AS ingresos
             FROM entradas
             WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetch();
    }

    public static function ultimas(int $limite = 6): array
    {
        $consulta = db()->prepare(
            'SELECT en.*, vi.nombre AS visitante
             FROM entradas en
             INNER JOIN visitantes vi ON vi.id = en.id_visitante
             ORDER BY en.fecha DESC, en.hora DESC, en.id DESC
             LIMIT :limite'
        );
        $consulta->bindValue('limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }
}
