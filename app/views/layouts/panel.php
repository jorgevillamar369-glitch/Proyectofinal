<?php
/**
 * Plantilla del panel interno: barra lateral, barra superior y contenido.
 * Variables: $contenido y los datos de la página.
 */

$usuario = Auth::usuario();
$titulo = $titulo ?? 'Panel';
$subtitulo = $subtitulo ?? 'Aqua Viva · Administración';
$activo = $activo ?? '';
$acciones = $acciones ?? '';
$avisos = tomar_flash();

$enlaces = [
    ['id' => 'dashboard',  'texto' => 'Resumen',    'url' => url('panel'),      'icono' => 'panel',    'roles' => [1, 2, 3], 'grupo' => 'Operación'],
    ['id' => 'tanques',    'texto' => 'Tanques',    'url' => url('tanques'),    'icono' => 'tanque',   'roles' => [1, 2],    'grupo' => 'Operación'],
    ['id' => 'peces',      'texto' => 'Peces',      'url' => url('peces'),      'icono' => 'pez',      'roles' => [1, 2],    'grupo' => 'Operación'],
    ['id' => 'visitantes', 'texto' => 'Visitantes', 'url' => url('visitantes'), 'icono' => 'personas', 'roles' => [1, 3],    'grupo' => 'Taquilla'],
    ['id' => 'entradas',   'texto' => 'Entradas',   'url' => url('entradas'),   'icono' => 'entrada',  'roles' => [1, 3],    'grupo' => 'Taquilla'],
    ['id' => 'finanzas',   'texto' => 'Finanzas',   'url' => url('finanzas'),   'icono' => 'dinero',   'roles' => [1, 3],    'grupo' => 'Taquilla'],
    ['id' => 'usuarios',   'texto' => 'Personal',   'url' => url('usuarios'),   'icono' => 'carnet',   'roles' => [1],       'grupo' => 'Administración'],
];

$iniciales = '';
foreach (preg_split('/\s+/', trim((string) $usuario['nombre'])) as $parte) {
    if ($parte !== '') {
        $iniciales .= mb_substr($parte, 0, 1);
    }
    if (mb_strlen($iniciales) >= 2) {
        break;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · <?= e(APP_NOMBRE) ?></title>
    <meta name="description" content="Sistema de administración del acuario Aqua Viva.">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="app">
<a class="salto-contenido" href="#contenido">Saltar al contenido</a>

<div class="app-marco">
    <aside class="lateral" id="lateral" aria-label="Menú principal">
        <div class="lateral-marca">
            <span class="marca-monograma">AV</span>
            <span>
                <strong>Aqua Viva</strong>
                <small>Administración</small>
            </span>
        </div>

        <nav class="lateral-nav">
            <?php
            $grupoActual = '';
            foreach ($enlaces as $enlace):
                if (!in_array(Auth::rol(), $enlace['roles'], true)) {
                    continue;
                }
                if ($enlace['grupo'] !== $grupoActual):
                    $grupoActual = $enlace['grupo'];
                    ?>
                    <p class="lateral-grupo"><?= e($grupoActual) ?></p>
                <?php endif; ?>
                <a class="lateral-enlace <?= $activo === $enlace['id'] ? 'esta-activo' : '' ?>"
                   href="<?= e($enlace['url']) ?>"
                   <?= $activo === $enlace['id'] ? 'aria-current="page"' : '' ?>>
                    <?= icono($enlace['icono'], 19) ?>
                    <span><?= e($enlace['texto']) ?></span>
                </a>
            <?php endforeach; ?>

            <p class="lateral-grupo">Sitio público</p>
            <a class="lateral-enlace" href="<?= url() ?>" target="_blank" rel="noopener">
                <?= icono('ojo', 19) ?>
                <span>Ver la página del acuario</span>
            </a>
        </nav>

        <div class="lateral-pie">
            <p class="lateral-clima">
                <span class="punto"></span>
                Sistemas de bombeo activos
            </p>
            <p class="lateral-nota">Turno de la mañana · 08:00 a 16:00</p>
        </div>
    </aside>

    <div class="velo" id="velo" hidden></div>

    <div class="app-cuerpo">
        <header class="barra">
            <button class="boton-menu" id="botonMenu" type="button"
                    aria-label="Abrir menú" aria-expanded="false" aria-controls="lateral">
                <?= icono('menu', 22) ?>
            </button>

            <div class="barra-contexto">
                <p class="barra-sistema">Acuario Aqua Viva</p>
                <p class="barra-fecha"><?= e(fecha_larga(date('Y-m-d'))) ?></p>
            </div>

            <div class="barra-usuario">
                <span class="rol-chip"><?= e(Auth::nombreRol(Auth::rol())) ?></span>
                <div class="barra-identidad">
                    <strong><?= e($usuario['nombre']) ?></strong>
                    <span><?= e($usuario['correo']) ?></span>
                </div>
                <span class="avatar" aria-hidden="true"><?= e($iniciales) ?></span>
                <a class="boton-salir" href="<?= url('salir') ?>" title="Cerrar sesión">
                    <?= icono('salir', 19) ?>
                    <span class="visualmente-oculto">Cerrar sesión</span>
                </a>
            </div>
        </header>

        <main class="contenido" id="contenido">
            <?php if ($avisos): ?>
                <div class="avisos">
                    <?php foreach ($avisos as $aviso): ?>
                        <div class="aviso aviso--<?= e($aviso['tipo']) ?>" role="status">
                            <?= icono($aviso['tipo'] === 'ok' ? 'ok' : 'alerta', 18) ?>
                            <span><?= e($aviso['mensaje']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pagina-cabeza">
                <div>
                    <p class="sobretitulo"><?= e($subtitulo) ?></p>
                    <h1><?= e($titulo) ?></h1>
                </div>
                <?php if ($acciones !== ''): ?>
                    <div class="pagina-acciones"><?= $acciones ?></div>
                <?php endif; ?>
            </div>

            <?php require $contenido; ?>
        </main>

        <footer class="pie">
            <p>Sistema de administración · Acuario Aqua Viva</p>
            <p class="pie-autor">© <?= date('Y') ?> <strong>Jorge Villamar</strong></p>
        </footer>
    </div>
</div>

<script src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
