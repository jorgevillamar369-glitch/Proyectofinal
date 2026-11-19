<?php
/**
 * Modelo de consultas agregadas para el panel de resumen.
 */

declare(strict_types=1);

final class Reporte
{
    public static function totales(): array
    {
        return [
            'peces'             => (int) db()->query("SELECT COUNT(*) FROM peces WHERE estado <> 'trasladado'")->fetchColumn(),
            'especies'          => (int) db()->query('SELECT COUNT(*) FROM especies')->fetchColumn(),
            'tanquesOperativos' => (int) db()->query("SELECT COUNT(*) FROM tanques WHERE estado = 'operativo'")->fetchColumn(),
            'tanquesTotal'      => (int) db()->query('SELECT COUNT(*) FROM tanques')->fetchColumn(),
        ];
    }

    public static function mes(): array
    {
        $personas = (int) db()->query(
            "SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();

        $entradas = (int) db()->query(
            "SELECT COUNT(*) FROM entradas WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();

        $taquilla = (float) db()->query(
            "SELECT COALESCE(SUM(total), 0) FROM entradas WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();

        $otrosIngresos = (float) db()->query(
            "SELECT COALESCE(SUM(monto), 0) FROM finanzas
             WHERE tipo = 'ingreso' AND fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();

        $egresos = (float) db()->query(
            "SELECT COALESCE(SUM(monto), 0) FROM finanzas
             WHERE tipo = 'egreso' AND fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();

        return [
            'personas'      => $personas,
            'entradas'      => $entradas,
            'taquilla'      => $taquilla,
            'otrosIngresos' => $otrosIngresos,
            'egresos'       => $egresos,
            'balance'       => $taquilla + $otrosIngresos - $egresos,
        ];
    }

    /** Tanques con su diagnóstico de agua y su nivel de carga. */
    public static function tanquesConDiagnostico(): array
    {
        $tanques = Tanque::todos();
        $rangos = Tanque::rangos();

        foreach ($tanques as $indice => $tanque) {
            $rango = $rangos[(int) $tanque['id']] ?? null;
            $tanque['diagnostico'] = diagnostico_tanque($tanque, $rango);
            $tanque['carga'] = clase_carga((float) $tanque['ocupacion_pct'], $tanque['estado']);
            $tanques[$indice] = $tanque;
        }

        return $tanques;
    }

    public static function litrosTotales(): int
    {
        return (int) db()->query('SELECT COALESCE(SUM(capacidad), 0) FROM tanques')->fetchColumn();
    }
}
