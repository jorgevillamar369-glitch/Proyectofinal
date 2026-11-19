<?php
/**
 * Directorio de visitantes.
 * Variables: $visitantes, $resumen, $busqueda, $totalEntradas.
 */
?>
<div class="rejilla-datos" style="grid-template-columns: repeat(3, 1fr);">
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Visitantes registrados</p>
        <p class="dato-valor"><?= (int) $resumen['total'] ?></p>
        <p class="dato-pie">con correo para enviar novedades</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Nuevos este mes</p>
        <p class="dato-valor"><?= (int) $resumen['nuevos'] ?></p>
        <p class="dato-pie">altas desde el día 1</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Entradas vinculadas</p>
        <p class="dato-valor"><?= (int) $totalEntradas ?></p>
        <p class="dato-pie">ventas asociadas a estas personas</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Directorio</h2>
        <form method="get" action="index.php" class="buscador" role="search">
            <input type="hidden" name="ruta" value="visitantes">
            <?= icono('lupa', 17) ?>
            <input class="form-control" type="search" name="q" value="<?= e($busqueda) ?>"
                   placeholder="Nombre, correo o ciudad" aria-label="Buscar visitante">
        </form>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Visitante</th>
                    <th scope="col">Contacto</th>
                    <th scope="col">Tarifa habitual</th>
                    <th scope="col" class="num">Visitas</th>
                    <th scope="col" class="num">Gasto acumulado</th>
                    <th scope="col">Registro</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($visitantes === []): ?>
                    <tr>
                        <td colspan="7">
                            <div class="estado-vacio">
                                <strong>No hay visitantes con ese criterio</strong>
                                <p>Registra a la persona antes de venderle una entrada o cambia la búsqueda.</p>
                                <a class="boton" href="<?= url('visitantes/crear') ?>">Registrar visitante</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($visitantes as $visitante): ?>
                    <tr>
                        <td>
                            <span class="principal"><?= e($visitante['nombre']) ?></span>
                            <span class="secundario"><?= e($visitante['correo']) ?></span>
                        </td>
                        <td>
                            <?= e($visitante['telefono'] ?: 'Sin teléfono') ?>
                            <span class="secundario"><?= e($visitante['procedencia']) ?></span>
                        </td>
                        <td><span class="pildora pildora--neutral"><?= e(texto_tarifa($visitante['tipo'])) ?></span></td>
                        <td class="num">
                            <?= (int) $visitante['total_entradas'] ?>
                            <span class="secundario"><?= (int) $visitante['total_personas'] ?> personas</span>
                        </td>
                        <td class="num"><?= e(dinero($visitante['total_gastado'])) ?></td>
                        <td><?= e(fecha_corta($visitante['fecha_registro'])) ?></td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('entradas/crear', ['visitante' => $visitante['id']]) ?>"
                               title="Vender entrada">
                                <?= icono('entrada', 17) ?>
                            </a>
                            <a class="boton-icono" href="<?= url('visitantes/editar', ['id' => $visitante['id']]) ?>" title="Editar visitante">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <form class="js-eliminar" method="post" action="<?= url('visitantes/eliminar') ?>" style="display: inline;"
                                  data-mensaje="Se eliminará el registro de <?= e($visitante['nombre']) ?>.">
                                <?= campo_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $visitante['id'] ?>">
                                <button class="boton-icono" type="submit" title="Eliminar visitante">
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
