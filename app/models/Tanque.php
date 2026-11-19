<?php
/**
 * Modelo de tanques y su ocupación.
 */

declare(strict_types=1);

final class Tanque
{
    public static function todos(): array
    {
        return db()->query(
            'SELECT v.*, t.habitat, t.temperatura, t.ph, t.iluminacion, t.estado, t.notas
             FROM vista_ocupacion v
             INNER JOIN tanques t ON t.id = v.id
             ORDER BY t.codigo'
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
        $consulta = db()->prepare('SELECT * FROM vista_ocupacion WHERE id = :id');
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
