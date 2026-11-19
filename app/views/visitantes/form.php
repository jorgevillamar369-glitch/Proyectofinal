<?php
/**
 * Formulario de visitante.
 * Variables: $visitante, $errores, $editando, $id, $historial.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos de la persona</h2>
            <span class="panel-nota">Los campos con * son obligatorios</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('visitantes/editar', ['id' => $id]) : url('visitantes/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div class="campo <?= isset($errores['nombre']) ? 'campo-error' : '' ?>">
                <label for="nombre">Nombre completo *</label>
                <input class="form-control" type="text" id="nombre" name="nombre"
                       value="<?= e($visitante['nombre']) ?>" maxlength="80" required placeholder="Camila Herrera">
                <?php if (isset($errores['nombre'])): ?>
                    <span class="error-campo"><?= e($errores['nombre']) ?></span>
                <?php endif; ?>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['correo']) ? 'campo-error' : '' ?>">
                    <label for="correo">Correo electrónico *</label>
                    <input class="form-control" type="email" id="correo" name="correo"
                           value="<?= e($visitante['correo']) ?>" maxlength="100" required placeholder="nombre@correo.ec">
                    <?php if (isset($errores['correo'])): ?>
                        <span class="error-campo"><?= e($errores['correo']) ?></span>
                    <?php else: ?>
                        <span class="ayuda">Identifica a la persona; no se puede repetir.</span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['telefono']) ? 'campo-error' : '' ?>">
                    <label for="telefono">Teléfono</label>
                    <input class="form-control" type="tel" id="telefono" name="telefono"
                           value="<?= e($visitante['telefono']) ?>" maxlength="15" placeholder="0991234567">
                    <?php if (isset($errores['telefono'])): ?>
                        <span class="error-campo"><?= e($errores['telefono']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['procedencia']) ? 'campo-error' : '' ?>">
                    <label for="procedencia">Ciudad de procedencia *</label>
                    <input class="form-control" type="text" id="procedencia" name="procedencia"
                           value="<?= e($visitante['procedencia']) ?>" maxlength="80" required placeholder="Quito">
                    <?php if (isset($errores['procedencia'])): ?>
                        <span class="error-campo"><?= e($errores['procedencia']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['tipo']) ? 'campo-error' : '' ?>">
                    <label for="tipo">Tarifa habitual *</label>
                    <select class="form-select" id="tipo" name="tipo">
                        <?php foreach (tarifas() as $valor => $precio): ?>
                            <option value="<?= e($valor) ?>" <?= $visitante['tipo'] === $valor ? 'selected' : '' ?>>
                                <?= e(texto_tarifa($valor)) ?> · <?= e(dinero($precio)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('visitantes') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar visitante' ?></button>
            </div>
        </form>
    </section>

    <aside style="display: grid; gap: 18px;">
        <?php if ($editando): ?>
            <section class="panel">
                <div class="panel-cabeza">
                    <h2>Historial de visitas</h2>
                    <span class="panel-nota"><?= count($historial) ?> registros</span>
                </div>
                <?php if ($historial === []): ?>
                    <div class="panel-cuerpo">
                        <p class="texto-tenue" style="font-size: 13px;">Todavía no tiene entradas vendidas.</p>
                    </div>
                <?php else: ?>
                    <div class="tabla-envoltura">
                        <table class="tabla tabla-compacta">
                            <tbody>
                                <?php foreach ($historial as $entrada): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('entradas/ticket', ['id' => $entrada['id']]) ?>" class="principal"><?= e($entrada['codigo']) ?></a>
                                            <span class="secundario"><?= e(fecha_corta($entrada['fecha'])) ?> · <?= (int) $entrada['cantidad'] ?> personas</span>
                                        </td>
                                        <td class="num"><?= e(dinero($entrada['total'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="panel-pie">
                    <a class="boton-enlace" href="<?= url('entradas/crear', ['visitante' => $id]) ?>">
                        Vender una entrada nueva <?= icono('flecha', 15) ?>
                    </a>
                </div>
            </section>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-cabeza">
                <h2>Datos de contacto</h2>
            </div>
            <div class="panel-cuerpo">
                <div class="ficha-lista">
                    <div>
                        <dt>Correo</dt>
                        <dd><?= e($visitante['correo'] ?: '—') ?></dd>
                    </div>
                    <div>
                        <dt>Teléfono</dt>
                        <dd><?= e($visitante['telefono'] ?: '—') ?></dd>
                    </div>
                    <div>
                        <dt>Registro</dt>
                        <dd><?= e(fecha_corta($visitante['fecha_registro'])) ?></dd>
                    </div>
                </div>
            </div>
        </section>
    </aside>
</div>
