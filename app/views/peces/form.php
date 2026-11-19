<?php
/**
 * Formulario de pez.
 * Variables: $pez, $errores, $editando, $id, $especies, $tanques, $datosEspecies.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos del ejemplar</h2>
            <span class="panel-nota">Los campos con * son obligatorios</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('peces/editar', ['id' => $id]) : url('peces/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['nombre']) ? 'campo-error' : '' ?>">
                    <label for="nombre">Nombre del ejemplar *</label>
                    <input class="form-control" type="text" id="nombre" name="nombre"
                           value="<?= e($pez['nombre']) ?>" maxlength="60" required placeholder="Capitán">
                    <?php if (isset($errores['nombre'])): ?>
                        <span class="error-campo"><?= e($errores['nombre']) ?></span>
                    <?php else: ?>
                        <span class="ayuda">El nombre con el que lo reconoce el equipo.</span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['id_especie']) ? 'campo-error' : '' ?>">
                    <label for="id_especie">Especie *</label>
                    <select class="form-select" id="id_especie" name="id_especie" required>
                        <option value="">Selecciona una especie</option>
                        <?php foreach ($especies as $especie): ?>
                            <option value="<?= (int) $especie['id'] ?>"
                                <?= (int) $pez['id_especie'] === (int) $especie['id'] ? 'selected' : '' ?>>
                                <?= e($especie['nombre_comun']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['id_especie'])): ?>
                        <span class="error-campo"><?= e($errores['id_especie']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <label for="nombre_cientifico">Nombre científico</label>
                <input class="form-control" type="text" id="nombre_cientifico" value="" readonly
                       style="background: var(--superficie-2); color: var(--tinta-2);">
                <span class="ayuda" id="fichaEspecie">Elige una especie para ver su ficha técnica.</span>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['id_tanque']) ? 'campo-error' : '' ?>">
                    <label for="id_tanque">Tanque asignado</label>
                    <select class="form-select" id="id_tanque" name="id_tanque">
                        <option value="">Sin tanque (cuarentena)</option>
                        <?php foreach ($tanques as $tanque):
                            $disponible = max(0, (int) $tanque['carga_maxima_cm'] - (int) $tanque['carga_cm']); ?>
                            <option value="<?= (int) $tanque['id'] ?>"
                                <?= (string) $pez['id_tanque'] === (string) $tanque['id'] ? 'selected' : '' ?>>
                                <?= e($tanque['codigo'] . ' · ' . $tanque['nombre']) ?>
                                (<?= $disponible ?> cm disponibles)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['id_tanque'])): ?>
                        <span class="error-campo"><?= e($errores['id_tanque']) ?></span>
                    <?php else: ?>
                        <span class="ayuda">Solo se muestran tanques existentes; el sistema valida el espacio.</span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['tamanio_cm']) ? 'campo-error' : '' ?>">
                    <label for="tamanio_cm">Tamaño actual en cm *</label>
                    <input class="form-control" type="number" id="tamanio_cm" name="tamanio_cm"
                           value="<?= e($pez['tamanio_cm']) ?>" min="1" max="500" required placeholder="210">
                    <?php if (isset($errores['tamanio_cm'])): ?>
                        <span class="error-campo"><?= e($errores['tamanio_cm']) ?></span>
                    <?php endif; ?>
                    <span class="error-campo" id="avisoTamanio" hidden></span>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['edad_meses']) ? 'campo-error' : '' ?>">
                    <label for="edad_meses">Edad en meses *</label>
                    <input class="form-control" type="number" id="edad_meses" name="edad_meses"
                           value="<?= e($pez['edad_meses']) ?>" min="0" max="600" required placeholder="132">
                    <?php if (isset($errores['edad_meses'])): ?>
                        <span class="error-campo"><?= e($errores['edad_meses']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['sexo']) ? 'campo-error' : '' ?>">
                    <label for="sexo">Sexo</label>
                    <select class="form-select" id="sexo" name="sexo">
                        <?php foreach (['macho' => 'Macho', 'hembra' => 'Hembra', 'indefinido' => 'Sin determinar'] as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $pez['sexo'] === $valor ? 'selected' : '' ?>>
                                <?= e($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['fecha_ingreso']) ? 'campo-error' : '' ?>">
                    <label for="fecha_ingreso">Fecha de ingreso *</label>
                    <input class="form-control" type="date" id="fecha_ingreso" name="fecha_ingreso"
                           value="<?= e($pez['fecha_ingreso']) ?>" max="<?= date('Y-m-d') ?>" required>
                    <?php if (isset($errores['fecha_ingreso'])): ?>
                        <span class="error-campo"><?= e($errores['fecha_ingreso']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['estado']) ? 'campo-error' : '' ?>">
                    <label for="estado">Estado sanitario</label>
                    <select class="form-select" id="estado" name="estado">
                        <?php foreach (['saludable' => 'Saludable', 'observacion' => 'En observación', 'tratamiento' => 'En tratamiento', 'trasladado' => 'Trasladado'] as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $pez['estado'] === $valor ? 'selected' : '' ?>>
                                <?= e($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('peces') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar pez' ?></button>
            </div>
        </form>
    </section>

    <aside style="display: grid; gap: 18px;">
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Espacio disponible</h2>
                <span class="panel-nota">Regla de 10 L por cm</span>
            </div>
            <div class="tabla-envoltura">
                <table class="tabla tabla-compacta">
                    <thead>
                        <tr>
                            <th scope="col">Tanque</th>
                            <th scope="col" class="num">Libres</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tanques as $tanque):
                            $disponible = max(0, (int) $tanque['carga_maxima_cm'] - (int) $tanque['carga_cm']); ?>
                            <tr>
                                <td>
                                    <span class="codigo-tanque"><?= e($tanque['codigo']) ?></span>
                                    <span class="secundario"><?= e($tanque['nombre']) ?></span>
                                </td>
                                <td class="num"><?= $disponible ?> cm</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <div class="panel-cabeza">
                <h2>Antes de guardar</h2>
            </div>
            <div class="panel-cuerpo">
                <ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: var(--tinta-2); display: grid; gap: 8px;">
                    <li>Un ejemplar nuevo entra en observación durante 21 días.</li>
                    <li>El tamaño registrado debe ser el actual, no el máximo de la especie.</li>
                    <li>Si el tanque está sobre el 80% de carga, el sistema avisa en el resumen.</li>
                    <li>Los peces trasladados no cuentan para la ocupación.</li>
                </ul>
            </div>
        </section>
    </aside>
</div>

<script type="application/json" id="datosEspecies"><?= $datosEspecies ?></script>
