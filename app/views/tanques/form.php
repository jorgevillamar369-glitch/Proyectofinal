<?php
/**
 * Formulario de tanque.
 * Variables: $tanque, $errores, $editando, $id, $ocupacion, $pecesTanque.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos del tanque</h2>
            <span class="panel-nota">Los campos con * son obligatorios</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('tanques/editar', ['id' => $id]) : url('tanques/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['codigo']) ? 'campo-error' : '' ?>">
                    <label for="codigo">Código *</label>
                    <input class="form-control" type="text" id="codigo" name="codigo"
                           value="<?= e($tanque['codigo']) ?>" maxlength="12" required placeholder="T-08">
                    <?php if (isset($errores['codigo'])): ?>
                        <span class="error-campo"><?= e($errores['codigo']) ?></span>
                    <?php else: ?>
                        <span class="ayuda">Identificador corto que aparece en los reportes.</span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['nombre']) ? 'campo-error' : '' ?>">
                    <label for="nombre">Nombre del tanque *</label>
                    <input class="form-control" type="text" id="nombre" name="nombre"
                           value="<?= e($tanque['nombre']) ?>" maxlength="70" required placeholder="Arrecife norte">
                    <?php if (isset($errores['nombre'])): ?>
                        <span class="error-campo"><?= e($errores['nombre']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['habitat']) ? 'campo-error' : '' ?>">
                    <label for="habitat">Tipo de agua *</label>
                    <select class="form-select" id="habitat" name="habitat" required>
                        <?php foreach (['marino' => 'Agua marina', 'dulce' => 'Agua dulce', 'salobre' => 'Agua salobre'] as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $tanque['habitat'] === $valor ? 'selected' : '' ?>>
                                <?= e($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['habitat'])): ?>
                        <span class="error-campo"><?= e($errores['habitat']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['capacidad']) ? 'campo-error' : '' ?>">
                    <label for="capacidad">Capacidad en litros *</label>
                    <input class="form-control" type="number" id="capacidad" name="capacidad"
                           value="<?= e($tanque['capacidad']) ?>" min="50" max="100000" step="50" required placeholder="1500">
                    <?php if (isset($errores['capacidad'])): ?>
                        <span class="error-campo"><?= e($errores['capacidad']) ?></span>
                    <?php else: ?>
                        <span class="ayuda">Regla del acuario: 10 litros por cada centímetro de pez.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['temperatura']) ? 'campo-error' : '' ?>">
                    <label for="temperatura">Temperatura (°C) *</label>
                    <input class="form-control" type="number" id="temperatura" name="temperatura"
                           value="<?= e($tanque['temperatura']) ?>" min="5" max="35" step="0.1" required placeholder="25.5">
                    <?php if (isset($errores['temperatura'])): ?>
                        <span class="error-campo"><?= e($errores['temperatura']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['ph']) ? 'campo-error' : '' ?>">
                    <label for="ph">pH del agua *</label>
                    <input class="form-control" type="number" id="ph" name="ph"
                           value="<?= e($tanque['ph']) ?>" min="5" max="9.5" step="0.1" required placeholder="8.2">
                    <?php if (isset($errores['ph'])): ?>
                        <span class="error-campo"><?= e($errores['ph']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <span class="etiqueta-campo">Diagnóstico inmediato</span>
                <div class="diagnostico" id="diagnosticoAgua"></div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['iluminacion']) ? 'campo-error' : '' ?>">
                    <label for="iluminacion">Iluminación</label>
                    <select class="form-select" id="iluminacion" name="iluminacion">
                        <?php foreach (['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta'] as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $tanque['iluminacion'] === $valor ? 'selected' : '' ?>>
                                <?= e($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo <?= isset($errores['estado']) ? 'campo-error' : '' ?>">
                    <label for="estado">Estado operativo</label>
                    <select class="form-select" id="estado" name="estado">
                        <?php foreach (['operativo' => 'Operativo', 'mantenimiento' => 'En mantenimiento', 'cuarentena' => 'Cuarentena'] as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $tanque['estado'] === $valor ? 'selected' : '' ?>>
                                <?= e($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="campo <?= isset($errores['notas']) ? 'campo-error' : '' ?>">
                <label for="notas">Notas de mantenimiento</label>
                <textarea class="form-control" id="notas" name="notas" maxlength="255"
                          placeholder="Filtro de turba, revisión de salinidad los martes..."><?= e($tanque['notas']) ?></textarea>
                <?php if (isset($errores['notas'])): ?>
                    <span class="error-campo"><?= e($errores['notas']) ?></span>
                <?php endif; ?>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('tanques') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar tanque' ?></button>
            </div>
        </form>
    </section>

    <aside style="display: grid; gap: 18px;">
        <?php if ($editando && $ocupacion): ?>
            <section class="panel">
                <div class="panel-cabeza">
                    <h2>Ocupación actual</h2>
                    <span class="panel-nota"><?= (int) $ocupacion['total_peces'] ?> peces</span>
                </div>
                <div class="panel-cuerpo">
                    <div class="barra-carga" style="margin-bottom: 6px;">
                        <span class="barra-carga-pista">
                            <span class="barra-carga-relleno" data-valor="<?= e($ocupacion['ocupacion_pct']) ?>"
                                  data-clase="<?= e(clase_carga((float) $ocupacion['ocupacion_pct'], $tanque['estado'])['clase']) ?>"></span>
                        </span>
                        <span class="barra-carga-valor"><?= number_format((float) $ocupacion['ocupacion_pct'], 1, ',', '.') ?>%</span>
                    </div>
                    <p class="texto-tenue" style="font-size: 12px;">
                        <?= (int) $ocupacion['carga_cm'] ?> cm de pez sobre un máximo recomendado de
                        <?= (int) $ocupacion['carga_maxima_cm'] ?> cm.
                    </p>
                </div>
            </section>

            <section class="panel">
                <div class="panel-cabeza">
                    <h2>Peces en este tanque</h2>
                </div>
                <?php if ($pecesTanque === []): ?>
                    <div class="panel-cuerpo">
                        <p class="texto-tenue" style="font-size: 13px;">Este tanque todavía no tiene peces asignados.</p>
                    </div>
                <?php else: ?>
                    <div class="tabla-envoltura">
                        <table class="tabla tabla-compacta">
                            <tbody>
                                <?php foreach ($pecesTanque as $pez): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('peces/editar', ['id' => $pez['id']]) ?>" class="principal"><?= e($pez['nombre']) ?></a>
                                            <span class="secundario"><?= e($pez['nombre_comun']) ?> · <?= (int) $pez['tamanio_cm'] ?> cm</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="panel">
                <div class="panel-cabeza">
                    <h2>Buenas prácticas</h2>
                </div>
                <div class="panel-cuerpo">
                    <ul class="ficha-lista" style="font-size: 12.5px; color: var(--tinta-2); padding-left: 0; list-style: none;">
                        <li style="padding-left: 0;">Revisa la temperatura y el pH al abrir y al cerrar el turno.</li>
                        <li style="padding-left: 0;">Deja 21 días de cuarentena antes de sumar un ejemplar nuevo.</li>
                        <li style="padding-left: 0;">No superes el 80% de la carga recomendada del tanque.</li>
                    </ul>
                </div>
            </section>
        <?php endif; ?>
    </aside>
</div>
