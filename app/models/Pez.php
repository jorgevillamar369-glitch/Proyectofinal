<?php
/**
 * Modelo de peces y fichas biológicas.
 */

declare(strict_types=1);

final class Pez
{
    public static function todos(?string $busqueda = null): array
    {
        $sql = "SELECT p.*, e.nombre_comun, e.nombre_cientifico, e.tamanio_max_cm,
                       t.codigo AS tanque_codigo, t.nombre AS tanque_nombre
                FROM peces p
                INNER JOIN especies e ON e.id = p.id_especie
                LEFT JOIN tanques t ON t.id = p.id_tanque";

        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE p.nombre LIKE :busqueda OR e.nombre_comun LIKE :busqueda
                      OR e.nombre_cientifico LIKE :busqueda OR t.codigo LIKE :busqueda';
        }

        $sql .= ' ORDER BY e.nombre_comun, p.nombre';

        $consulta = db()->prepare($sql);
        $consulta->execute($busqueda !== null && $busqueda !== '' ? ['busqueda' => '%' . $busqueda . '%'] : []);

        return $consulta->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM peces WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function porTanque(int $idTanque): array
    {
        $consulta = db()->prepare(
            "SELECT p.id, p.nombre, p.tamanio_cm, p.estado, e.nombre_comun
             FROM peces p
             INNER JOIN especies e ON e.id = p.id_especie
             WHERE p.id_tanque = :id AND p.estado <> 'trasladado'
             ORDER BY p.nombre"
        );
        $consulta->execute(['id' => $idTanque]);

        return $consulta->fetchAll();
    }

    public static function resumen(): array
    {
        return db()->query(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(estado = 'tratamiento'), 0) AS tratamiento,
                    COALESCE(SUM(estado = 'observacion'), 0) AS observacion,
                    ROUND(AVG(tamanio_cm), 1) AS promedio
             FROM peces WHERE estado <> 'trasladado'"
        )->fetch();
    }

    public static function seguimiento(): array
    {
        return db()->query(
            "SELECT p.*, e.nombre_comun, e.nombre_cientifico,
                    t.codigo AS tanque_codigo, t.nombre AS tanque_nombre
             FROM peces p
             INNER JOIN especies e ON e.id = p.id_especie
             LEFT JOIN tanques t ON t.id = p.id_tanque
             WHERE p.estado IN ('observacion', 'tratamiento')
             ORDER BY FIELD(p.estado, 'tratamiento', 'observacion'), p.nombre"
        )->fetchAll();
    }

    public static function crear(array $datos): void
    {
        db()->prepare(
            'INSERT INTO peces (nombre, id_especie, id_tanque, tamanio_cm, edad_meses, sexo, fecha_ingreso, estado)
             VALUES (:nombre, :id_especie, :id_tanque, :tamanio_cm, :edad_meses, :sexo, :fecha_ingreso, :estado)'
        )->execute($datos);
    }

    public static function actualizar(int $id, array $datos): void
    {
        $datos['id'] = $id;
        db()->prepare(
            'UPDATE peces SET nombre = :nombre, id_especie = :id_especie, id_tanque = :id_tanque,
             tamanio_cm = :tamanio_cm, edad_meses = :edad_meses, sexo = :sexo,
             fecha_ingreso = :fecha_ingreso, estado = :estado
             WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM peces WHERE id = :id')->execute(['id' => $id]);
    }

    /** Centímetros ya ocupados en un tanque, sin contar al pez que se edita. */
    public static function cargaEnTanque(int $idTanque, int $excepto = 0): int
    {
        $consulta = db()->prepare(
            "SELECT COALESCE(SUM(tamanio_cm), 0) FROM peces
             WHERE id_tanque = :tanque AND estado <> 'trasladado' AND id <> :id"
        );
        $consulta->execute(['tanque' => $idTanque, 'id' => $excepto]);

        return (int) $consulta->fetchColumn();
    }
}
