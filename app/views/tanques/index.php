<?php
/**
 * Listado de tanques.
 * Variables: $tanques, $totalLitros, $totalPeces, $totalAlertas.
 */
?>
<div class="rejilla-datos" style="grid-template-columns: repeat(3, 1fr);">
    <article class="dato dato--acento">
        <p class="dato-etiqueta">Tanques registrados</p>
        <p class="dato-valor"><?= count($tanques) ?></p>
        <p class="dato-pie"><?= number_format($totalLitros, 0, ',', '.') ?> litros en total</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Peces alojados</p>
        <p class="dato-valor"><?= $totalPeces ?></p>
        <p class="dato-pie">sin contar los trasladados</p>
    </article>
    <article class="dato">
        <p class="dato-etiqueta">Tanques por revisar</p>
        <p class="dato-valor"><?= $totalAlertas ?></p>
        <p class="dato-pie">parámetros fuera de la ventana de sus especies</p>
    </article>
</div>

<section class="panel">
    <div class="panel-cabeza">
        <h2>Instalaciones</h2>
        <div class="buscador">
            <?= icono('lupa', 17) ?>
            <input class="form-control" type="search" placeholder="Código o nombre"
                   data-filtro="#tablaTanques" aria-label="Buscar tanque">
        </div>
    </div>

    <div class="tabla-envoltura">
        <table class="tabla" id="tablaTanques">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Tanque</th>
                    <th scope="col" class="num">Capacidad</th>
                    <th scope="col" class="num">Agua</th>
                    <th scope="col">Ocupación</th>
                    <th scope="col">Diagnóstico</th>
                    <th scope="col" class="acciones"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tanques === []): ?>
                    <tr>
                        <td colspan="7">
                            <div class="estado-vacio">
                                <strong>Todavía no hay tanques</strong>
                                <p>Registra el primero para empezar a controlar el agua y los peces.</p>
                                <a class="boton" href="<?= url('tanques/crear') ?>">Crear tanque</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tanques as $tanque):
                    $estado = etiqueta_estado_tanque($tanque['estado']); ?>
                    <tr>
                        <td><span class="codigo-tanque"><?= e($tanque['codigo']) ?></span></td>
                        <td>
                            <span class="principal"><?= e($tanque['nombre']) ?></span>
                            <span class="secundario"><?= e(texto_habitat($tanque['habitat'])) ?> · luz <?= e($tanque['iluminacion']) ?></span>
                        </td>
                        <td class="num">
                            <?= number_format((int) $tanque['capacidad'], 0, ',', '.') ?> L
                            <span class="secundario"><?= (int) $tanque['total_peces'] ?> peces · <?= (int) $tanque['carga_cm'] ?> cm</span>
                        </td>
                        <td class="num">
                            <?= number_format((float) $tanque['temperatura'], 1) ?> °C
                            <span class="secundario">pH <?= number_format((float) $tanque['ph'], 1) ?></span>
                        </td>
                        <td>
                            <div class="barra-carga">
                                <span class="barra-carga-pista">
                                    <span class="barra-carga-relleno" data-valor="<?= e($tanque['ocupacion_pct']) ?>"
                                          data-clase="<?= e($tanque['carga']['clase']) ?>"></span>
                                </span>
                                <span class="barra-carga-valor"><?= number_format((float) $tanque['ocupacion_pct'], 0, ',', '.') ?>%</span>
                            </div>
                            <span class="secundario"><?= e($tanque['carga']['texto']) ?></span>
                        </td>
                        <td>
                            <span class="pildora pildora--<?= e($tanque['diagnostico']['clase']) ?>"
                                  title="<?= e($tanque['diagnostico']['problemas'] ? implode(' · ', $tanque['diagnostico']['problemas']) : 'Temperatura y pH dentro del rango de todas las especies.') ?>">
                                <?= e($tanque['diagnostico']['texto']) ?>
                            </span>
                            <span class="secundario">Estado: <?= e($estado['texto']) ?></span>
                        </td>
                        <td class="acciones">
                            <a class="boton-icono" href="<?= url('tanques/editar', ['id' => $tanque['id']]) ?>" title="Editar tanque">
                                <?= icono('lapiz', 17) ?>
                            </a>
                            <form class="js-eliminar" method="post" action="<?= url('tanques/eliminar') ?>" style="display: inline;"
                                  data-mensaje="Se eliminará el tanque <?= e($tanque['codigo'] . ' · ' . $tanque['nombre']) ?>. Los peces quedarán sin tanque asignado.">
                                <?= campo_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $tanque['id'] ?>">
                                <button class="boton-icono" type="submit" title="Eliminar tanque">
                                    <?= icono('basura', 17) ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
