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
    <link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css?v=2">
</head>
<body class="sitio">

<header class="sitio-barra">
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
        <a class="enlace-acceso" href="<?= url('acceso') ?>">Acceso del personal</a>
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
</footer>

<button class="firma-boton" type="button" id="botonFirma" aria-haspopup="dialog">
    <span class="firma-monograma" aria-hidden="true">JV</span>
    <span>Creado por <strong>Jorge Villamar</strong></span>
</button>

<dialog class="firma-dialogo" id="firmaDialogo" aria-label="Créditos del sitio">
    <div class="firma-dialogo-caja">
        <button class="firma-cerrar" type="button" id="cerrarFirma" aria-label="Cerrar">&times;</button>
        <p class="firma-copyright">© <?= date('Y') ?> Jorge Villamar</p>
    </div>
</dialog>

<script>
(function () {
    var boton = document.getElementById('botonFirma');
    var dialogo = document.getElementById('firmaDialogo');
    var cerrar = document.getElementById('cerrarFirma');

    if (!boton || !dialogo || !cerrar) {
        return;
    }

    boton.addEventListener('click', function () {
        dialogo.showModal();
    });

    cerrar.addEventListener('click', function () {
        dialogo.close();
    });

    dialogo.addEventListener('click', function (evento) {
        if (evento.target === dialogo) {
            dialogo.close();
        }
    });
})();
</script>

</body>
</html>
