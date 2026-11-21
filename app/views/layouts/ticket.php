<?php
/**
 * Plantilla mínima para el ticket imprimible.
 */

$tituloPagina = $tituloPagina ?? 'Ticket · Aqua Viva';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina) ?></title>
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
        <link rel="stylesheet" href="assets/css/estilos.css?v=5">
</head>
<body class="app">

<div style="padding: 28px 16px 48px;">
    <?php require $contenido; ?>
    <?php $variante = 'pie'; require __DIR__ . '/../partials/creador.php'; ?>
</div>

</body>
</html>
