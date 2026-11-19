<?php
/**
 * Plantilla de acceso: imagen a la izquierda y formulario a la derecha.
 */

$avisos = tomar_flash();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso del personal · Aqua Viva</title>
    <meta name="description" content="Ingreso al sistema de administración del acuario Aqua Viva.">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

<main class="acceso">
    <section class="acceso-imagen" aria-hidden="true">
        <img src="assets/img/acceso-acuario.jpg" alt="">
        <div class="acceso-imagen-texto">
            <p class="kicker">Panel interno</p>
            <h1>Administración del acuario</h1>
            <p>
                Control de peces, tanques, entradas y finanzas
                del acuario.
            </p>
        </div>
    </section>

    <section class="acceso-formulario">
        <div class="acceso-caja">
            <div class="acceso-marca">
                <span class="marca-monograma">AV</span>
                <span>
                    <strong>Aqua Viva</strong>
                    <small>Acuario de Quito</small>
                </span>
            </div>

            <?php if ($avisos): ?>
                <?php foreach ($avisos as $aviso): ?>
                    <div class="aviso aviso--<?= e($aviso['tipo']) ?>" role="status" style="margin-bottom: 16px;">
                        <?= icono($aviso['tipo'] === 'ok' ? 'ok' : 'alerta', 18) ?>
                        <span><?= e($aviso['mensaje']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php require $contenido; ?>

            <p class="acceso-pie">
                <a href="<?= url() ?>">Volver a la página del acuario</a>
            </p>
        </div>
    </section>
</main>

</body>
</html>
