<?php
/**
 * Listado de peces.
 * Variables: $peces, $resumen, $busqueda.
 */
?>
<div class="rejilla-datos rejilla-datos--tres">
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Peces registrados</p>
        <p class="dato-valor"><?= (int) $resumen['total'] ?></p>
        <p class="dato-pie">tamaño promedio de <?= number_format((float) $resumen['promedio'], 1, ',', '.') ?> cm</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">En tratamiento</p>
        <p class="dato-valor"><?= (int) $resumen['tratamiento'] ?></p>
        <p class="dato-pie">con seguimiento veterinario</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">En observación</p>
        <p class="dato-valor"><?= (int) $resumen['observacion'] ?></p>
        <p class="dato-pie">recién llegados o en revisión</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Fichas de los ejemplares</h2>
        <form method="get" action="index.php" class="buscador" role="search">
            <input type="hidden" name="ruta" value="peces">
            <?= icono('lupa', 17) ?>
            <input class="form-control" type="search" name="q" value="<?= e($busqueda) ?>"
                   placeholder="Nombre, especie o tanque" aria-label="Buscar pez">
        </form>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Ejemplar</th>
                    <th scope="col">Tanque</th>
                    <th scope="col" class="num">Tamaño</th>
                    <th scope="col" class="num">Edad</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Ingreso</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($peces === []): ?>
                    <tr>
                        <td colspan="8">
                            <div class="estado-vacio">
                                <strong>No hay ejemplares con ese criterio</strong>
                                <p>Prueba con otro nombre o limpia la búsqueda para ver toda la colección.</p>
                                <a class="boton-secundario" href="<?= url('peces') ?>">Ver todos</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($peces as $pez):
                    $estado = etiqueta_estado_pez($pez['estado']); ?>
                    <tr>
                        <td>
                            <span class="principal"><?= e($pez['nombre']) ?></span>
                            <span class="secundario"><?= e($pez['nombre_comun']) ?> · <em><?= e($pez['nombre_cientifico']) ?></em></span>
                        </td>
                        <td>
                            <?php if ($pez['tanque_codigo']): ?>
                                <span class="codigo-tanque"><?= e($pez['tanque_codigo']) ?></span>
                                <span class="secundario"><?= e($pez['tanque_nombre']) ?></span>
                            <?php else: ?>
                                <span class="texto-tenue">Sin tanque</span>
                            <?php endif; ?>
                        </td>
                        <td class="num">
                            <?= (int) $pez['tamanio_cm'] ?> cm
                            <span class="secundario">máx. <?= (int) $pez['tamanio_max_cm'] ?> cm</span>
                        </td>
                        <td class="num">
                            <?= (int) $pez['edad_meses'] ?> meses
                            <span class="secundario"><?= number_format($pez['edad_meses'] / 12, 1, ',', '.') ?> años</span>
                        </td>
                        <td style="text-transform: capitalize;"><?= e($pez['sexo']) ?></td>
                        <td><?= e(fecha_corta($pez['fecha_ingreso'])) ?></td>
                        <td><span class="pildora pildora--<?= e($estado['clase']) ?>"><?= e($estado['texto']) ?></span></td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('peces/editar', ['id' => $pez['id']]) ?>" title="Editar ficha">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <form class="js-eliminar" method="post" action="<?= url('peces/eliminar') ?>" style="display: inline;"
                                  data-mensaje="Se eliminará la ficha de <?= e($pez['nombre']) ?> (<?= e($pez['nombre_comun']) ?>).">
                                <?= campo_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $pez['id'] ?>">
                                <button class="boton-icono" type="submit" title="Eliminar ficha">
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
