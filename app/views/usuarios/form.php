<?php
/**
 * Formulario de cuenta del personal.
 * Variables: $cuenta, $errores, $editando, $id, $roles.
 */
?>
<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Datos de la cuenta</h2>
            <span class="panel-nota">Los campos con * son obligatorios</span>
        </div>

        <form class="panel-cuerpo formulario" method="post"
              action="<?= $editando ? url('usuarios/editar', ['id' => $id]) : url('usuarios/crear') ?>" novalidate>
            <?= campo_csrf() ?>

            <div class="formulario-doble">
                <div class="campo <?= isset($errores['nombre']) ? 'campo-error' : '' ?>">
                    <label for="nombre">Nombre completo *</label>
                    <input class="form-control" type="text" id="nombre" name="nombre"
                           value="<?= e($cuenta['nombre']) ?>" maxlength="70" required placeholder="Andrea Salazar">
                    <?php if (isset($errores['nombre'])): ?>
                        <span class="error-campo"><?= e($errores['nombre']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo <?= isset($errores['correo']) ? 'campo-error' : '' ?>">
                    <label for="correo">Correo electrónico *</label>
                    <input class="form-control" type="email" id="correo" name="correo"
                           value="<?= e($cuenta['correo']) ?>" maxlength="100" required placeholder="nombre@aquaviva.ec">
                    <?php if (isset($errores['correo'])): ?>
                        <span class="error-campo"><?= e($errores['correo']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo <?= isset($errores['id_rol']) ? 'campo-error' : '' ?>">
                <label for="id_rol">Perfil de acceso *</label>
                <select class="form-select" id="id_rol" name="id_rol" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int) $rol['id'] ?>"
                            <?= (int) $cuenta['id_rol'] === (int) $rol['id'] ? 'selected' : '' ?>>
                            <?= e($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['id_rol'])): ?>
                    <span class="error-campo"><?= e($errores['id_rol']) ?></span>
                <?php else: ?>
                    <span class="ayuda">
                        Administrador: todo el sistema. Peces: colección biológica.
                        Visitantes: taquilla y finanzas.
                    </span>
                <?php endif; ?>
            </div>

            <div class="campo <?= isset($errores['contrasena']) ? 'campo-error' : '' ?>">
                <label for="contrasena">
                    Contraseña <?= $editando ? '(dejar vacía para conservarla)' : '*' ?>
                </label>
                <input class="form-control" type="password" id="contrasena" name="contrasena"
                       autocomplete="new-password" <?= $editando ? '' : 'required' ?>
                       placeholder="Mínimo 8 caracteres">
                <?php if (isset($errores['contrasena'])): ?>
                    <span class="error-campo"><?= e($errores['contrasena']) ?></span>
                <?php else: ?>
                    <span class="ayuda">Se guarda cifrada con password_hash; nadie puede leerla después.</span>
                <?php endif; ?>
            </div>

            <div class="campo">
                <label class="casilla" for="activo">
                    <input type="checkbox" id="activo" name="activo" value="1"
                        <?= $cuenta['activo'] ? 'checked' : '' ?>>
                    Cuenta activa (puede iniciar sesión)
                </label>
                <?php if (isset($errores['activo'])): ?>
                    <span class="error-campo"><?= e($errores['activo']) ?></span>
                <?php endif; ?>
            </div>

            <div class="formulario-pie">
                <a class="boton-secundario" href="<?= url('usuarios') ?>">Cancelar</a>
                <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Crear cuenta' ?></button>
            </div>
        </form>
    </section>

    <aside>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Permisos por perfil</h2>
            </div>
            <div class="panel-cuerpo">
                <div class="ficha-lista">
                    <div>
                        <dt>Administrador</dt>
                        <dd>Todo el sistema</dd>
                    </div>
                    <div>
                        <dt>Encargado de peces</dt>
                        <dd>Tanques y peces</dd>
                    </div>
                    <div>
                        <dt>Encargado de visitantes</dt>
                        <dd>Visitantes, entradas y finanzas</dd>
                    </div>
                </div>
                <p class="texto-tenue" style="font-size: 12px; margin-top: 14px;">
                    El menú se arma según el perfil, pero las páginas también verifican
                    el rol en el servidor antes de mostrar o guardar datos.
                </p>
            </div>
        </section>
    </aside>
</div>
