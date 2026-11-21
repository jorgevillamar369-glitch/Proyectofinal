<?php
/**
 * Plantilla pública: portada, cabecera, pie y botón de créditos.
 * Variables esperadas: $contenido, $tituloPagina (opcional).
 */

$tituloPagina = $tituloPagina ?? 'Aqua Viva · Acuario de Quito';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina) ?></title>
    <meta name="description" content="Acuario Aqua Viva en el norte de Quito: tanques marinos y de agua dulce, horarios, tarifas y visitas guiadas.">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <?php if (!empty($preloadImagen)): ?>
        <link rel="preload" as="image" href="<?= e($preloadImagen) ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/css/estilos.css?v=5">
</head>
<body class="sitio">

<header class="sitio-barra <?= !empty($cabeceraSolida) ? 'sitio-barra--solida' : '' ?>">
    <a class="sitio-marca" href="<?= url() ?>">
        <span class="marca-monograma"><?= logoMarca(26) ?></span>
        <span>
            <strong>Aqua Viva</strong>
            <small>Acuario de Quito</small>
        </span>
    </a>

    <nav class="sitio-nav" aria-label="Secciones del sitio">
        <a href="<?= url() ?>#colecciones">Colecciones</a>
        <a href="<?= url() ?>#visita">Visita</a>
        <a class="enlace-acceso" href="<?= url('acceso') ?>">
            <span class="acceso-largo">Acceso del personal</span>
            <span class="acceso-corto">Acceso</span>
        </a>
    </nav>
</header>

<main>
    <?php require $contenido; ?>
</main>

<footer class="pie-sitio">
    <div class="pie-sitio-interior">
        <div>
            <strong>Aqua Viva</strong>
            <p>Av. de los Shyris y Eloy Alfaro, Quito · Ecuador</p>
        </div>
        <div>
            <p>Fotografías: Wikimedia Commons (CC0 y dominio público).</p>
            <p><a href="<?= url('acceso') ?>">Acceso del personal</a></p>
        </div>
    </div>

    <?php $variante = 'flotante'; require __DIR__ . '/../partials/creador.php'; ?>
</footer>

</body>
</html>
