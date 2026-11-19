<?php
/**
 * Vista: carga la plantilla y dentro coloca la vista de la página.
 */

declare(strict_types=1);

final class View
{
    public static function render(string $vista, array $datos = [], string $layout = 'panel'): void
    {
        $contenido = dirname(__DIR__) . '/views/' . $vista . '.php';
        $plantilla = dirname(__DIR__) . '/views/layouts/' . $layout . '.php';

        if (!is_file($contenido) || !is_file($plantilla)) {
            http_response_code(500);
            exit('No se encontró la vista solicitada.');
        }

        extract($datos, EXTR_SKIP);
        require $plantilla;
    }
}
