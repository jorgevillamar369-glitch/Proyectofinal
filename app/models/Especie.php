<?php
/**
 * Modelo del catálogo de especies.
 */

declare(strict_types=1);

final class Especie
{
    public static function todas(): array
    {
        return db()->query('SELECT * FROM especies ORDER BY nombre_comun')->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $consulta = db()->prepare('SELECT * FROM especies WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $fila = $consulta->fetch();

        return $fila ?: null;
    }
}
