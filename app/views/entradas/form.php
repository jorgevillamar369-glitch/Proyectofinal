<?php
/**
 * Venta y edición de entradas.
 * Variables: $entrada, $errores, $editando, $id, $visitantes, $datosTarifas.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos de la venta</h2>
            <span class="panel-nota">Precios en dólares</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('entradas/editar', ['id' => $id]) : url('entradas/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div class="campo <?= isset($errores['id_visitante']) ? 'campo-error' : '' ?>">
                <label for="id_visitante">Visitante *</label>
                <select class="form-select" id="id_visitante" name="id_visitante" required>
                    <?php foreach ($visitantes as $visitante): ?>
                        <option value="<?= (int) $visitante['id'] ?>"
                            <?= (string) $entrada['id_visitante'] === (string) $visitante['id'] ? 'selected' : '' ?>>
                            <?= e($visitante['nombre'] . ' · ' . $visitante['correo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['id_visitante'])): ?>
                    <span class="error-campo"><?= e($errores['id_visitante']) ?></span>
                <?php else: ?>
                    <span class="ayuda">Si la persona no está registrada, <a href="<?= url('visitantes/crear') ?>">créala primero</a>.</span>
                <?php endif; ?>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['fecha']) ? 'campo-error' : '' ?>">
                    <label for="fecha">Fecha de visita *</label>
                    <input class="form-control" type="date" id="fecha" name="fecha"
                           value="<?= e($entrada['fecha']) ?>" max="<?= date('Y-m-d') ?>" required>
                    <?php if (isset($errores['fecha'])): ?>
                        <span class="error-campo"><?= e($errores['fecha']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['hora']) ? 'campo-error' : '' ?>">
                    <label for="hora">Hora de la venta *</label>
                    <input class="form-control" type="time" id="hora" name="hora"
                           value="<?= e($entrada['hora']) ?>" required>
                    <?php if (isset($errores['hora'])): ?>
                        <span class="error-campo"><?= e($errores['hora']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['tarifa']) ? 'campo-error' : '' ?>">
                    <label for="tarifa">Tarifa *</label>
                    <select class="form-select" id="tarifa" name="tarifa" required>
                        <?php foreach (tarifas() as $clave => $precio): ?>
                            <option value="<?= e($clave) ?>" <?= $entrada['tarifa'] === $clave ? 'selected' : '' ?>>
                                <?= e(texto_tarifa($clave)) ?> · <?= e(dinero($precio)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['tarifa'])): ?>
                        <span class="error-campo"><?= e($errores['tarifa']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['cantidad']) ? 'campo-error' : '' ?>">
                    <label for="cantidad">Cantidad de personas *</label>
                    <input class="form-control" type="number" id="cantidad" name="cantidad"
                           value="<?= e($entrada['cantidad']) ?>" min="1" max="20" required>
                    <?php if (isset($errores['cantidad'])): ?>
                        <span class="error-campo"><?= e($errores['cantidad']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo <?= isset($errores['metodo_pago']) ? 'campo-error' : '' ?>">
                <span class="etiqueta-campo">Método de pago</span>
                <div style="display: flex; gap: 18px; flex-wrap: wrap; padding-top: 2px;">
                    <?php foreach (['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia'] as $valor => $texto): ?>
                        <label class="casilla">
                            <input type="radio" name="metodo_pago" value="<?= e($valor) ?>"
                                <?= $entrada['metodo_pago'] === $valor ? 'checked' : '' ?>>
                            <?= e($texto) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($errores['metodo_pago'])): ?>
                    <span class="error-campo"><?= e($errores['metodo_pago']) ?></span>
                <?php endif; ?>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('entradas') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar venta' ?></button>
            </div>
        </form>
    </section>

    <aside style="display: grid; gap: 18px; position: sticky; top: 82px;">
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Total a cobrar</h2>
                <span class="panel-nota">Calculado en vivo</span>
            </div>
            <div class="panel-cuerpo">
                <p class="etiqueta-superior">Total</p>
                <p id="totalEntrada" style="font-family: 'Newsreader', Georgia, serif; font-size: 46px; line-height: 1.05; margin: 4px 0 10px;">
                    $0.00
                </p>
                <p class="texto-tenue" style="font-size: 12.5px;">
                    El servidor vuelve a calcular el precio con la tabla oficial al guardar,
                    así que el monto del ticket siempre coincide con la tarifa vigente.
                </p>
            </div>
            <div class="panel-pie">
                Las entradas son válidas solo para la fecha indicada.
            </div>
        </section>

        <section class="panel">
            <div class="panel-cabeza">
                <h2>Tarifas vigentes</h2>
            </div>
            <div class="panel-cuerpo">
                <div class="ficha-lista">
                    <?php foreach (tarifas() as $clave => $precio): ?>
                        <div>
                            <dt><?= e(texto_tarifa($clave)) ?></dt>
                            <dd><?= e(dinero($precio)) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="texto-tenue" style="font-size: 12px; margin-top: 12px;">
                    Menores de 3 años sin costo. Grupos escolares con reserva previa.
                </p>
            </div>
        </section>
    </aside>
</div>

<script type="application/json" id="datosTarifas"><?= $datosTarifas ?></script>
