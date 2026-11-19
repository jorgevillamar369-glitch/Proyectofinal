<?php
/**
 * Ticket imprimible.
 * Variable: $entrada (con visitante, vendedor y totales).
 */
?>
<div class="ticket-acciones" style="max-width: 430px; margin-bottom: 18px;">
    <a class="boton-tenue" href="<?= url('entradas') ?>"><?= icono('volver', 16) ?> Volver a taquilla</a>
    <button class="boton-secundario" type="button" onclick="window.print()">Imprimir ticket</button>
</div>

<article class="ticket">
    <div class="ticket-marca">
        <strong>Aqua Viva</strong>
        <span class="ticket-codigo"><?= e($entrada['codigo']) ?></span>
    </div>

    <h1>Entrada al acuario</h1>
    <p class="ticket-fecha">
        <?= e(fecha_larga($entrada['fecha'])) ?> · <?= e(hora_corta($entrada['hora'])) ?>
    </p>

    <div class="ficha-lista">
        <div>
            <dt>Visitante</dt>
            <dd><?= e($entrada['visitante']) ?></dd>
        </div>
        <div>
            <dt>Personas</dt>
            <dd><?= (int) $entrada['cantidad'] ?></dd>
        </div>
        <div>
            <dt>Tarifa</dt>
            <dd><?= e(texto_tarifa($entrada['tarifa'])) ?></dd>
        </div>
        <div>
            <dt>Precio unitario</dt>
            <dd><?= e(dinero($entrada['precio_unitario'])) ?></dd>
        </div>
        <div>
            <dt>Método de pago</dt>
            <dd><?= e(texto_pago($entrada['metodo_pago'])) ?></dd>
        </div>
        <div>
            <dt>Atendido por</dt>
            <dd><?= e($entrada['vendedor'] ?? 'Sin registro') ?></dd>
        </div>
    </div>

    <div class="ticket-total">
        <span>Total pagado</span>
        <strong><?= e(dinero($entrada['total'])) ?></strong>
    </div>

    <p class="ticket-pie">
        Presenta este código en la entrada del acuario.<br>
        Válido solo para la fecha indicada. No se admiten devoluciones.<br>
        Av. de los Shyris y Eloy Alfaro, Quito.
    </p>
</article>

<div class="ticket-acciones" style="max-width: 430px;">
    <a class="boton-tenue" href="<?= url('entradas/crear') ?>">Nueva venta</a>
    <a class="boton-tenue" href="<?= url('entradas/editar', ['id' => $entrada['id']]) ?>">Editar esta entrada</a>
</div>
