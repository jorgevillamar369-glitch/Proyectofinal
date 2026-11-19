<?php
/**
 * Formulario de movimiento de caja.
 * Variables: $movimiento, $errores, $editando, $id, $categorias.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos del movimiento</h2>
            <span class="panel-nota">Los campos con * son obligatorios</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('finanzas/editar', ['id' => $id]) : url('finanzas/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div>
                <span class="etiqueta-campo">Tipo de movimiento *</span>
                <div style="display: flex; gap: 18px; padding-top: 8px; flex-wrap: wrap;">
                    <label class="casilla">
                        <input type="radio" name="tipo" value="ingreso" <?= $movimiento['tipo'] === 'ingreso' ? 'checked' : '' ?>>
                        Ingreso
                    </label>
                    <label class="casilla">
                        <input type="radio" name="tipo" value="egreso" <?= $movimiento['tipo'] === 'egreso' ? 'checked' : '' ?>>
                        Egreso
                    </label>
                </div>
                <?php if (isset($errores['tipo'])): ?>
                    <span class="error-campo"><?= e($errores['tipo']) ?></span>
                <?php endif; ?>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['fecha']) ? 'campo-error' : '' ?>">
                    <label for="fecha">Fecha *</label>
                    <input class="form-control" type="date" id="fecha" name="fecha"
                           value="<?= e($movimiento['fecha']) ?>" required>
                    <?php if (isset($errores['fecha'])): ?>
                        <span class="error-campo"><?= e($errores['fecha']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['monto']) ? 'campo-error' : '' ?>">
                    <label for="monto">Monto en dólares *</label>
                    <input class="form-control" type="number" id="monto" name="monto"
                           value="<?= e($movimiento['monto']) ?>" min="0.01" step="0.01" required placeholder="186.40">
                    <?php if (isset($errores['monto'])): ?>
                        <span class="error-campo"><?= e($errores['monto']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo <?= isset($errores['categoria']) ? 'campo-error' : '' ?>">
                <label for="categoria">Categoría *</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <optgroup label="Ingresos">
                        <?php foreach ($categoriasIngreso as $categoria): ?>
                            <option value="<?= e($categoria) ?>" <?= $movimiento['categoria'] === $categoria ? 'selected' : '' ?>>
                                <?= e($categoria) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Egresos">
                        <?php foreach ($categoriasEgreso as $categoria): ?>
                            <option value="<?= e($categoria) ?>" <?= $movimiento['categoria'] === $categoria ? 'selected' : '' ?>>
                                <?= e($categoria) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
                <?php if (isset($errores['categoria'])): ?>
                    <span class="error-campo"><?= e($errores['categoria']) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= isset($errores['descripcion']) ? 'campo-error' : '' ?>">
                <label for="descripcion">Descripción *</label>
                <input class="form-control" type="text" id="descripcion" name="descripcion"
                       value="<?= e($movimiento['descripcion']) ?>" maxlength="160" required
                       placeholder="Cambio de bombas del tanque T-02">
                <?php if (isset($errores['descripcion'])): ?>
                    <span class="error-campo"><?= e($errores['descripcion']) ?></span>
                <?php endif; ?>
                <span class="ayuda">Escribe el detalle para que otra persona entienda el gasto.</span>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('finanzas') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar movimiento' ?></button>
            </div>
        </form>
    </section>

    <aside>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Cómo se calcula el balance</h2>
            </div>
            <div class="panel-cuerpo">
                <p style="font-size: 13px; color: var(--tinta-2); margin-bottom: 12px;">
                    El balance del mes suma la taquilla, los ingresos registrados aquí y resta los egresos.
                </p>
                <div class="ficha-lista">
                    <div>
                        <dt>Taquilla</dt>
                        <dd>desde Entradas</dd>
                    </div>
                    <div>
                        <dt>Ingresos</dt>
                        <dd>esta tabla, tipo ingreso</dd>
                    </div>
                    <div>
                        <dt>Egresos</dt>
                        <dd>esta tabla, tipo egreso</dd>
                    </div>
                </div>
                <p class="texto-tenue" style="font-size: 12px; margin-top: 14px;">
                    No registres la venta de entradas otra vez aquí: se contaría dos veces.
                </p>
            </div>
        </section>
    </aside>
</div>
