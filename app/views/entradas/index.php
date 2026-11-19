<?php
/**
 * Taquilla: ventas registradas.
 * Variables: $entradas, $resumen, $busqueda.
 */
?>
<div class="rejilla-datos" style="grid-template-columns: repeat(3, 1fr);">
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Entradas del mes</p>
        <p class="dato-valor"><?= (int) $resumen['ventas'] ?></p>
        <p class="dato-pie">ventas registradas en taquilla</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Personas atendidas</p>
        <p class="dato-valor"><?= (int) $resumen['personas'] ?></p>
        <p class="dato-pie">sumando todas las entradas del mes</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Ingresos del mes</p>
        <p class="dato-valor"><?= e(dinero($resumen['ingresos'])) ?></p>
        <p class="dato-pie">solo taquilla, sin tienda ni cafetería</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Ventas registradas</h2>
        <form method="get" action="index.php" class="buscador" role="search">
            <input type="hidden" name="ruta" value="entradas">
            <?= icono('lupa', 17) ?>
            <input class="form-control" type="search" name="q" value="<?= e($busqueda) ?>"
                   placeholder="Código, nombre o correo" aria-label="Buscar entrada">
        </form>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Visitante</th>
                    <th scope="col">Fecha y hora</th>
                    <th scope="col" class="num">Cantidad</th>
                    <th scope="col">Tarifa</th>
                    <th scope="col">Pago</th>
                    <th scope="col" class="num">Total</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entradas === []): ?>
                    <tr>
                        <td colspan="8">
                            <div class="estado-vacio">
                                <strong>No hay entradas con ese criterio</strong>
                                <p>Vende la primera entrada del día o cambia el texto de búsqueda.</p>
                                <a class="boton" href="<?= url('entradas/crear') ?>">Vender entrada</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($entradas as $entrada): ?>
                    <tr>
                        <td>
                            <a class="principal" href="<?= url('entradas/ticket', ['id' => $entrada['id']]) ?>"><?= e($entrada['codigo']) ?></a>
                        </td>
                        <td>
                            <?= e($entrada['visitante']) ?>
                            <span class="secundario"><?= e($entrada['correo']) ?></span>
                        </td>
                        <td>
                            <?= e(fecha_corta($entrada['fecha'])) ?>
                            <span class="secundario"><?= e(hora_corta($entrada['hora'])) ?> · <?= e($entrada['vendedor'] ?? 'Sin registro') ?></span>
                        </td>
                        <td class="num"><?= (int) $entrada['cantidad'] ?></td>
                        <td>
                            <?= e(texto_tarifa($entrada['tarifa'])) ?>
                            <span class="secundario"><?= e(dinero($entrada['precio_unitario'])) ?> c/u</span>
                        </td>
                        <td><?= e(texto_pago($entrada['metodo_pago'])) ?></td>
                        <td class="num principal"><?= e(dinero($entrada['total'])) ?></td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('entradas/ticket', ['id' => $entrada['id']]) ?>" title="Ver ticket">
                                <?= icono('ojo', 17) ?>
                            </a>
                            <a class="boton-icono" href="<?= url('entradas/editar', ['id' => $entrada['id']]) ?>" title="Editar venta">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <form class="js-eliminar" method="post" action="<?= url('entradas/eliminar') ?>" style="display: inline;"
                                  data-mensaje="Se anulará la entrada <?= e($entrada['codigo']) ?> de <?= e($entrada['visitante']) ?> por <?= e(dinero($entrada['total'])) ?>.">
                                <?= campo_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $entrada['id'] ?>">
                                <button class="boton-icono" type="submit" title="Anular venta">
                                    <?= icono('basura', 17) ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
