<?php
/**
 * Plantilla de configuración para el repositorio o el hospedaje.
 *
 * Copia este archivo como config.php y completa los datos que te dio
 * el panel del hosting. Nunca subas config.php con contraseñas reales.
 */

declare(strict_types=1);

const APP_NOMBRE = 'Aqua Viva';
const APP_LEMA   = 'Acuario de Quito';
const APP_DEBUG  = false;

const DB_HOST    = 'localhost';
const DB_PORT    = '3306';
const DB_NOMBRE  = 'nombre_de_la_base';
const DB_USUARIO = 'usuario_de_la_base';
const DB_CLAVE   = 'contrasena_de_la_base';

date_default_timezone_set('America/Guayaquil');

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $https,
    ]);
    session_start();
}
