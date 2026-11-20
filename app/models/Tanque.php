<?php
/**
 * Modelo de tanques y su ocupación.
 */

declare(strict_types=1);

final class Tanque
{
    private const COLUMNAS_OCUPACION = "
        SELECT t.id, t.codigo, t.nombre, t.habitat, t.capacidad, t.temperatura, t.ph,
               t.iluminacion, t.estado, t.notas,
               COALESCE(SUM(p.tamanio_cm), 0) AS carga_cm,
               ROUND(t.capacidad / 10, 0) AS carga_maxima_cm,
               ROUND(COALESCE(SUM(p.tamanio_cm), 0) * 100 / (t.capacidad / 10), 1) AS ocupacion_pct,
               COUNT(p.id) AS total_peces
        FROM tanques t
        LEFT JOIN peces p ON p.id_tanque = t.id AND p.estado <> 'trasladado'";

    private const AGRUPAR_OCUPACION = "
        GROUP BY t.id, t.codigo, t.nombre, t.habitat, t.capacidad, t.temperatura, t.ph,
                 t.iluminacion, t.estado, t.notas";

    public static function todos(): array
    {
        return db()->query(
            self::COLUMNAS_OCUPACION . self::AGRUPAR_OCUPACION . ' ORDER BY t.codigo'
        )->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM tanques WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function ocupacion(int $id): ?array
    {
        $consulta = db()->prepare(
            self::COLUMNAS_OCUPACION . ' WHERE t.id = :id' . self::AGRUPAR_OCUPACION
        );
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }

    public static function cargaCm(int $id): int
    {
        $consulta = db()->prepare(
            "SELECT COALESCE(SUM(tamanio_cm), 0) FROM peces
             WHERE id_tanque = :id AND estado <> 'trasladado'"
        );
        $consulta->execute(['id' => $id]);

        return (int) $consulta->fetchColumn();
    }

    public static function codigoExiste(string $codigo, int $excepto = 0): bool
    {
        $consulta = db()->prepare('SELECT COUNT(*) FROM tanques WHERE codigo = :codigo AND id <> :id');
        $consulta->execute(['codigo' => $codigo, 'id' => $excepto]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public static function crear(array $datos): void
    {
        db()->prepare(
            'INSERT INTO tanques (codigo, nombre, habitat, capacidad, temperatura, ph, iluminacion, estado, notas)
             VALUES (:codigo, :nombre, :habitat, :capacidad, :temperatura, :ph, :iluminacion, :estado, :notas)'
        )->execute($datos);
    }

    public static function actualizar(int $id, array $datos): void
    {
        $datos['id'] = $id;
        db()->prepare(
            'UPDATE tanques SET codigo = :codigo, nombre = :nombre, habitat = :habitat,
             capacidad = :capacidad, temperatura = :temperatura, ph = :ph,
             iluminacion = :iluminacion, estado = :estado, notas = :notas
             WHERE id = :id'
        )->execute($datos);
    }

    public static function eliminar(int $id): void
    {
        db()->prepare('DELETE FROM tanques WHERE id = :id')->execute(['id' => $id]);
    }

    /** Ventana de temperatura y pH que toleran todas las especies de cada tanque. */
    public static function rangos(): array
    {
        $filas = db()->query(
            "SELECT p.id_tanque,
                    MAX(e.temp_min) AS temp_min,
                    MIN(e.temp_max) AS temp_max,
                    MAX(e.ph_min)   AS ph_min,
                    MIN(e.ph_max)   AS ph_max
             FROM peces p
             INNER JOIN especies e ON e.id = p.id_especie
             WHERE p.id_tanque IS NOT NULL AND p.estado <> 'trasladado'
             GROUP BY p.id_tanque"
        )->fetchAll();

        $mapa = [];
        foreach ($filas as $fila) {
            $mapa[(int) $fila['id_tanque']] = $fila;
        }

        return $mapa;
    }
}
