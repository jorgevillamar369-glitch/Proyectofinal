<?php
/**
 * Crédito del creador con su diálogo.
 * Variable: $variante = flotante | lateral | pie
 */
$variante = $variante ?? 'flotante';
?>
<button class="firma-boton firma-boton--<?= e($variante) ?>" type="button" id="botonFirma" aria-haspopup="dialog">
    <span class="firma-monograma" aria-hidden="true">JV</span>
    <span>Creado por <strong>Jorge Villamar</strong></span>
</button>

<dialog class="firma-dialogo" id="firmaDialogo" aria-label="Créditos del sitio">
    <div class="firma-dialogo-caja">
        <button class="firma-cerrar" type="button" id="cerrarFirma" aria-label="Cerrar">&times;</button>
        <p class="firma-copyright">© <?= date('Y') ?> Jorge Villamar</p>
    </div>
</dialog>

<script>
(function () {
    var boton = document.getElementById('botonFirma');
    var dialogo = document.getElementById('firmaDialogo');
    var cerrar = document.getElementById('cerrarFirma');

    if (!boton || !dialogo || !cerrar) {
        return;
    }

    boton.addEventListener('click', function () {
        dialogo.showModal();
    });

    cerrar.addEventListener('click', function () {
        dialogo.close();
    });

    dialogo.addEventListener('click', function (evento) {
        if (evento.target === dialogo) {
            dialogo.close();
        }
    });
})();
</script>
