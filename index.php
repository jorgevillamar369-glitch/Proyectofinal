<?php
/**
 * Acuario Aqua Viva · único punto de entrada del sistema (MVC).
 *
 * Todas las peticiones pasan por aquí y el enrutador decide qué
 * controlador responde. Ejemplos:
 *   index.php                      -> InicioController
 *   index.php?ruta=panel           -> PanelController
 *   index.php?ruta=tanques/editar  -> TanqueController@editar
 */

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

Router::dispatch();
