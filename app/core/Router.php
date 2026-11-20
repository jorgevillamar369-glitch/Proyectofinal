<?php
/**
 * Enrutador: traduce index.php?ruta=recurso/accion al controlador correcto.
 */

declare(strict_types=1);

final class Router
{
    private const SIMPLES = [
        'inicio' => [InicioController::class, 'index'],
        'acceso' => [AccesoController::class, 'index'],
        'entrar' => [AccesoController::class, 'entrar'],
        'salir'  => [AccesoController::class, 'salir'],
        'panel'  => [PanelController::class, 'index'],
    ];

    private const RECURSOS = [
        'tanques'    => [TanqueController::class, ['index', 'crear', 'editar', 'eliminar']],
        'peces'      => [PezController::class, ['index', 'crear', 'editar', 'eliminar']],
        'visitantes' => [VisitanteController::class, ['index', 'crear', 'editar', 'eliminar']],
        'entradas'   => [EntradaController::class, ['index', 'crear', 'editar', 'ticket', 'eliminar']],
        'finanzas'   => [FinanzaController::class, ['index', 'crear', 'editar', 'eliminar']],
        'usuarios'   => [UsuarioController::class, ['index', 'crear', 'editar', 'eliminar']],
    ];

    public static function dispatch(): void
    {
        $ruta = trim((string) ($_GET['ruta'] ?? ''), '/');
        $partes = $ruta === '' ? [] : explode('/', $ruta);
        $recurso = $partes[0] ?? 'inicio';
        $accion = $partes[1] ?? 'index';

        if ($recurso === '') {
            $recurso = 'inicio';
        }

        if (isset(self::SIMPLES[$recurso])) {
            [$clase, $metodo] = self::SIMPLES[$recurso];
            (new $clase())->$metodo();
            return;
        }

        if (isset(self::RECURSOS[$recurso])) {
            [$clase, $acciones] = self::RECURSOS[$recurso];

            if (!in_array($accion, $acciones, true)) {
                self::noEncontrado();
                return;
            }

            (new $clase())->$accion();
            return;
        }

        self::noEncontrado();
    }

    private static function noEncontrado(): void
    {
        http_response_code(404);
        View::render('errores/404', ['tituloPagina' => 'Página no encontrada', 'cabeceraSolida' => true], 'publica');
    }
}
