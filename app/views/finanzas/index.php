<?php
/**
 * Libro de finanzas.
 * Variables: $movimientos, $resumen, $taquilla, $balance, $mes, $tipo, $nombreMes.
 */
?>
<form method="get" action="index.php" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 18px;">
    <input type="hidden" name="ruta" value="finanzas">
    <div class="campo" style="max-width: 210px;">
        <label for="mes">Mes</label>
        <input class="form-control" type="month" id="mes" name="mes" value="<?= e($mes) ?>">
    </div>
    <div class="campo" style="max-width: 190px;">
        <label for="tipo">Tipo</label>
        <select class="form-select" id="tipo" name="tipo">
            <option value="">Todo</option>
            <option value="ingreso" <?= $tipo === 'ingreso' ? 'selected' : '' ?>>Solo ingresos</option>
            <option value="egreso" <?= $tipo === 'egreso' ? 'selected' : '' ?>>Solo egresos</option>
        </select>
    </div>
    <button class="boton-secundario" type="submit">Aplicar filtros</button>
</form>

<div class="rejilla-datos">
    <article class="dato">
        <p class="dato-etiqueta">Taquilla</p>
        <p class="dato-valor"><?= e(dinero($taquilla)) ?></p>
        <p class="dato-pie">entradas vendidas en el mes</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Otros ingresos</p>
        <p class="dato-valor"><?= e(dinero($resumen['ingresos'])) ?></p>
        <p class="dato-pie">tienda, cafetería, donaciones</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Egresos</p>
        <p class="dato-valor"><?= e(dinero($resumen['egresos'])) ?></p>
        <p class="dato-pie">gastos registrados en el mes</p>
    </article>
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Balance</p>
        <p class="dato-valor" style="color: <?= $balance >= 0 ? 'var(--ok)' : 'var(--alto)' ?>;">
            <?= e(dinero($balance)) ?>
        </p>
        <p class="dato-pie">taquilla + otros ingresos − egresos</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Movimientos de <?= e($nombreMes) ?></h2>
        <span class="panel-nota"><?= count($movimientos) ?> registros</span>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Concepto</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Registró</th>
                    <th scope="col" class="num">Monto</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($movimientos === []): ?>
                    <tr>
                        <td colspan="6">
                            <div class="estado-vacio">
                                <strong>Sin movimientos en este mes</strong>
                                <p>Cambia el mes o registra un ingreso, un gasto o un aporte de taquilla.</p>
                                <a class="boton" href="<?= url('finanzas/crear') ?>">Registrar movimiento</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?= e(fecha_corta($movimiento['fecha'])) ?></td>
                        <td>
                            <span class="principal"><?= e($movimiento['descripcion']) ?></span>
                            <span class="secundario"><?= e($movimiento['tipo'] === 'ingreso' ? 'Ingreso' : 'Egreso') ?></span>
                        </td>
                        <td><?= e($movimiento['categoria']) ?></td>
                        <td><?= e($movimiento['responsable'] ?? 'Sin registro') ?></td>
                        <td class="num" style="color: <?= $movimiento['tipo'] === 'ingreso' ? 'var(--ok)' : 'var(--alto)' ?>;">
                            <?= $movimiento['tipo'] === 'ingreso' ? '+' : '−' ?><?= e(dinero($movimiento['monto'])) ?>
                        </td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('finanzas/editar', ['id' => $movimiento['id']]) ?>" title="Editar movimiento">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <form class="js-eliminar" method="post" action="<?= url('finanzas/eliminar') ?>" style="display: inline;"
                                  data-mensaje="Se eliminará el movimiento «<?= e($movimiento['descripcion']) ?>» por <?= e(dinero($movimiento['monto'])) ?>.">
                                <?= campo_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $movimiento['id'] ?>">
                                <button class="boton-icono" type="submit" title="Eliminar movimiento">
                                    <?= icono('basura', 17) ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-pie">
        La taquilla se calcula con las entradas vendidas y no se duplica en esta tabla.
    </div>
</section>
