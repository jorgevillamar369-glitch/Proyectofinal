<?php
/**
 * Página 404.
 */
?>
<section class="seccion" style="max-width: 720px; padding-top: 80px;">
    <h1 style="font-size: 44px; margin-bottom: 14px;">No encontramos esa página.</h1>
    <p style="color: var(--tinta-2); max-width: 52ch; margin-bottom: 24px;">
        La dirección que abriste no existe o cambió de lugar. Puedes volver a la portada
        del acuario o entrar al panel del personal.
    </p>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a class="boton" href="<?= url() ?>">Ir a la portada</a>
        <a class="boton-secundario" href="<?= url('panel') ?>">Ir al panel</a>
    </div>
</section>
