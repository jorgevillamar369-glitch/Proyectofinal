<?php
/**
 * Listado del personal.
 * Variables: $usuarios, $resumen.
 */
?>
<div class="rejilla-datos rejilla-datos--tres">
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Cuentas registradas</p>
        <p class="dato-valor"><?= (int) $resumen['total'] ?></p>
        <p class="dato-pie">con acceso al panel interno</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Cuentas activas</p>
        <p class="dato-valor"><?= (int) $resumen['activos'] ?></p>
        <p class="dato-pie">pueden iniciar sesión hoy</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Perfiles de trabajo</p>
        <p class="dato-valor"><?= (int) $totalRoles ?></p>
        <p class="dato-pie">administrador, peces y visitantes</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Equipo del acuario</h2>
        <span class="panel-nota">Las contraseñas se guardan cifradas</span>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Perfil</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Alta</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuarioFila): ?>
                    <tr>
                        <td>
                            <span class="principal"><?= e($usuarioFila['nombre']) ?></span>
                            <?php if ((int) $usuarioFila['id'] === Auth::id()): ?>
                                <span class="secundario">Tu cuenta</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($usuarioFila['correo']) ?></td>
                        <td>
                            <span class="pildora pildora--neutral"><?= e($usuarioFila['rol']) ?></span>
                        </td>
                        <td>
                            <span class="pildora pildora--<?= $usuarioFila['activo'] ? 'ok' : 'neutral' ?>">
                                <?= $usuarioFila['activo'] ? 'Activa' : 'Desactivada' ?>
                            </span>
                        </td>
                        <td><?= e(fecha_corta(substr((string) $usuarioFila['creado_en'], 0, 10))) ?></td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('usuarios/editar', ['id' => $usuarioFila['id']]) ?>" title="Editar cuenta">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <?php if ((int) $usuarioFila['id'] !== Auth::id()): ?>
                                <form class="js-eliminar" method="post" action="<?= url('usuarios/eliminar') ?>" style="display: inline;"
                                      data-mensaje="Se eliminará la cuenta de <?= e($usuarioFila['nombre']) ?> (<?= e($usuarioFila['correo']) ?>).">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= (int) $usuarioFila['id'] ?>">
                                    <button class="boton-icono" type="submit" title="Eliminar cuenta">
                                        <?= icono('basura', 17) ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-pie">
        Un administrador no puede eliminar su propia cuenta ni dejar el sistema sin administradores activos.
    </div>
</section>
