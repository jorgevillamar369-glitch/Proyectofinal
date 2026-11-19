<?php
/**
 * Punto de arranque de la aplicación: configuración, utilidades y autoload.
 */

declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/core/helpers.php';

spl_autoload_register(static function (string $clase): void {
    foreach (['core', 'models', 'controllers'] as $carpeta) {
        $archivo = __DIR__ . '/' . $carpeta . '/' . $clase . '.php';
        if (is_file($archivo)) {
            require_once $archivo;
            return;
        }
    }
});
