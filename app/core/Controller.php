<?php
/**
 * Controlador base: atajo para renderizar vistas.
 */

declare(strict_types=1);

abstract class Controller
{
    protected function vista(string $vista, array $datos = [], string $layout = 'panel'): void
    {
        View::render($vista, $datos, $layout);
    }
}
