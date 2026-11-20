<?php
/**
 * Formulario de acceso.
 * Variables: $error, $correo.
 */
?>
<h2>Entrar al sistema</h2>
<p class="acceso-intro">Usa la cuenta que te asignó el administrador.</p>

<?php if ($error !== ''): ?>
    <div class="aviso aviso--error" role="alert" style="margin-bottom: 16px;">
        <?= icono('alerta', 18) ?>
        <span><?= e($error) ?></span>
    </div>
<?php endif; ?>

<form method="post" action="<?= url('entrar') ?>" class="formulario" novalidate>
    <?= campo_csrf() ?>

    <div class="campo">
        <label for="correo">Correo electrónico</label>
        <input class="form-control" type="email" id="correo" name="correo"
               value="<?= e($correo) ?>" autocomplete="username" required autofocus
               placeholder="nombre@aquaviva.ec">
    </div>

    <div class="campo">
        <label for="contrasena">Contraseña</label>
        <input class="form-control" type="password" id="contrasena" name="contrasena"
               autocomplete="current-password" required placeholder="Tu contraseña">
    </div>

    <button class="boton" type="submit" style="justify-content: center;">
        Ingresar
        <?= icono('flecha', 17) ?>
    </button>
</form>

<div class="demo-cuentas">
    <strong>Cuentas de demostración</strong>
    <p><span>Administrador</span> <code>admin@aquaviva.ec</code></p>
    <p><span>Encargado de peces</span> <code>peces@aquaviva.ec</code></p>
    <p><span>Encargado de visitantes</span> <code>visitantes@aquaviva.ec</code></p>
    <p><span>Contraseña de las tres cuentas</span> <code>acuario2026</code></p>
</div>
