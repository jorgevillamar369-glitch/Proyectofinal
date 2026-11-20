<?php
/**
 * Resumen del acuario. El contenido se adapta al rol del usuario.
 * Variables: $totales, $mes, $tanques, $alertas, $seguimiento, $ultimasEntradas, $ultimosMovimientos.
 */

$esAdmin = Auth::esAdmin();
$soloPeces = Auth::puedePeces() && !Auth::puedeVisitantes();
$soloVisitantes = Auth::puedeVisitantes() && !Auth::puedePeces();
?>

<?php if ($soloPeces): ?>
    <div class="rejilla-datos">
        <article class="dato dato--acento">
            <p class="dato-etiqueta">Peces registrados</p>
            <p class="dato-valor"><?= (int) $totales['peces'] ?></p>
            <p class="dato-pie">de <?= (int) $totales['especies'] ?> especies registradas</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Tanques operativos</p>
            <p class="dato-valor"><?= (int) $totales['tanquesOperativos'] ?><span class="texto-tenue" style="font-size: 16px;"> / <?= (int) $totales['tanquesTotal'] ?></span></p>
            <p class="dato-pie">revisión de parámetros dos veces al día</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Alertas de parámetros</p>
            <p class="dato-valor"><?= count(array_filter($alertas, static fn (array $a): bool => $a['tipo'] === 'agua')) ?></p>
            <p class="dato-pie">tanques fuera del rango de sus especies</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Bajo seguimiento</p>
            <p class="dato-valor"><?= count($seguimiento) ?></p>
            <p class="dato-pie">ejemplares en observación o tratamiento</p>
        </article>
    </div>
<?php elseif ($soloVisitantes): ?>
    <div class="rejilla-datos">
        <article class="dato dato--acento">
            <p class="dato-etiqueta">Personas este mes</p>
            <p class="dato-valor"><?= (int) $mes['personas'] ?></p>
            <p class="dato-pie">personas registradas en <?= (int) $mes['entradas'] ?> ventas</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Ingresos por taquilla</p>
            <p class="dato-valor"><?= e(dinero($mes['taquilla'])) ?></p>
            <p class="dato-pie">entradas del mes en curso</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Otros ingresos</p>
            <p class="dato-valor"><?= e(dinero($mes['otrosIngresos'])) ?></p>
            <p class="dato-pie">tienda, cafetería y donaciones</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Balance del mes</p>
            <p class="dato-valor"><?= e(dinero($mes['balance'])) ?></p>
            <p class="dato-pie">después de <?= e(dinero($mes['egresos'])) ?> en egresos</p>
        </article>
    </div>
<?php else: ?>
    <div class="rejilla-datos">
        <article class="dato dato--acento">
            <p class="dato-etiqueta">Peces registrados</p>
            <p class="dato-valor"><?= (int) $totales['peces'] ?></p>
            <p class="dato-pie">en <?= (int) $totales['tanquesTotal'] ?> tanques del edificio</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Personas este mes</p>
            <p class="dato-valor"><?= (int) $mes['personas'] ?></p>
            <p class="dato-pie"><?= (int) $mes['entradas'] ?> entradas vendidas</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Ingresos del mes</p>
            <p class="dato-valor"><?= e(dinero($mes['taquilla'] + $mes['otrosIngresos'])) ?></p>
            <p class="dato-pie">taquilla y servicios</p>
        </article>
        <article class="dato">
            <p class="dato-etiqueta">Balance del mes</p>
            <p class="dato-valor"><?= e(dinero($mes['balance'])) ?></p>
            <p class="dato-pie">egresos por <?= e(dinero($mes['egresos'])) ?></p>
        </article>
    </div>
<?php endif; ?>

<div class="rejilla-dos">
    <section class="panel">
        <div class="panel-cabeza">
            <h2>Estado de los tanques</h2>
            <a class="boton-enlace" href="<?= Auth::puedePeces() ? url('tanques') : url() . '#colecciones' ?>">
                <?= Auth::puedePeces() ? 'Ver todos' : 'Ver colecciones' ?>
                <?= icono('flecha', 15) ?>
            </a>
        </div>
        <div class="panel-cuerpo">
            <div class="lista-tanques">
                <?php foreach ($tanques as $tanque): ?>
                    <article class="tanque-mini tanque-mini--carga">
                        <span class="codigo-tanque"><?= e($tanque['codigo']) ?></span>
                        <div>
                            <p class="tanque-mini-nombre">
                                <?= e($tanque['nombre']) ?>
                                <span class="pildora pildora--<?= e($tanque['diagnostico']['clase']) ?>"
                                      title="<?= e($tanque['diagnostico']['texto']) ?>">
                                    <?= e($tanque['diagnostico']['texto']) ?>
                                </span>
                            </p>
                            <p class="tanque-mini-datos">
                                <?= e(texto_habitat($tanque['habitat'])) ?> ·
                                <?= number_format((float) $tanque['temperatura'], 1) ?> °C ·
                                pH <?= number_format((float) $tanque['ph'], 1) ?> ·
                                <?= (int) $tanque['total_peces'] ?> peces
                            </p>
                        </div>
                        <div class="barra-carga">
                            <span class="barra-carga-pista">
                                <span class="barra-carga-relleno" data-valor="<?= e($tanque['ocupacion_pct']) ?>"
                                      data-clase="<?= e($tanque['carga']['clase']) ?>"></span>
                            </span>
                            <span class="barra-carga-valor"><?= number_format((float) $tanque['ocupacion_pct'], 0, ',', '.') ?>%</span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="texto-tenue" style="font-size: 12px; margin-top: 14px;">
                Cada centímetro de pez necesita 10 litros de agua.
            </p>
        </div>
    </section>

    <section class="panel">
        <div class="panel-cabeza">
            <h2>Requiere atención</h2>
            <span class="panel-nota"><?= count($alertas) ?> avisos</span>
        </div>
        <div class="panel-cuerpo">
            <?php if ($alertas === []): ?>
                <div class="estado-vacio">
                    <?= icono('ok', 30) ?>
                    <strong>Todo en orden</strong>
                    <p>Ningún tanque ni ejemplar necesita revisión en este momento.</p>
                </div>
            <?php else: ?>
                <div class="lista-tanques">
                    <?php foreach (array_slice($alertas, 0, 5) as $alerta): ?>
                        <a class="tanque-mini" href="<?= e($alerta['url']) ?>" style="text-decoration: none; color: inherit;">
                            <span class="pildora pildora--<?= $alerta['tipo'] === 'pez' ? 'alto' : 'alerta' ?> pildora--sin-punto"
                                  style="align-self: start;">
                                <?= $alerta['tipo'] === 'pez' ? 'Ejemplar' : 'Tanque' ?>
                            </span>
                            <div>
                                <p class="tanque-mini-nombre"><?= e($alerta['titulo']) ?></p>
                                <p class="tanque-mini-datos"><?= e($alerta['texto']) ?></p>
                            </div>
                            <?= icono('flecha', 16) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<div class="rejilla-dos">
    <?php if (Auth::puedeVisitantes()): ?>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Últimas entradas vendidas</h2>
                <a class="boton-enlace" href="<?= url('entradas') ?>">Ver taquilla <?= icono('flecha', 15) ?></a>
            </div>
            <div class="tabla-envoltura">
                <table class="tabla tabla-compacta">
                    <thead>
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Visitante</th>
                            <th scope="col">Fecha</th>
                            <th scope="col" class="num">Personas</th>
                            <th scope="col" class="num">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasEntradas as $entrada): ?>
                            <tr>
                                <td><a href="<?= url('entradas/ticket', ['id' => $entrada['id']]) ?>"><?= e($entrada['codigo']) ?></a></td>
                                <td>
                                    <?= e($entrada['visitante']) ?>
                                    <span class="secundario"><?= e(texto_tarifa($entrada['tarifa'])) ?></span>
                                </td>
                                <td><?= e(fecha_corta($entrada['fecha'])) ?></td>
                                <td class="num"><?= (int) $entrada['cantidad'] ?></td>
                                <td class="num"><?= e(dinero($entrada['total'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Ejemplares bajo seguimiento</h2>
                <a class="boton-enlace" href="<?= url('peces') ?>">Ver peces <?= icono('flecha', 15) ?></a>
            </div>
            <?php if ($seguimiento === []): ?>
                <div class="panel-cuerpo">
                    <div class="estado-vacio">
                        <?= icono('ok', 30) ?>
                        <strong>Sin casos abiertos</strong>
                        <p>Todos los ejemplares están saludables y en exhibición.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="tabla-envoltura">
                    <table class="tabla tabla-compacta">
                        <thead>
                            <tr>
                                <th scope="col">Ejemplar</th>
                                <th scope="col">Especie</th>
                                <th scope="col">Tanque</th>
                                <th scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seguimiento as $pez):
                                $estado = etiqueta_estado_pez($pez['estado']); ?>
                                <tr>
                                    <td class="principal"><?= e($pez['nombre']) ?></td>
                                    <td><?= e($pez['nombre_comun']) ?></td>
                                    <td><?= e($pez['tanque_codigo'] ?? '—') ?></td>
                                    <td><span class="pildora pildora--<?= e($estado['clase']) ?>"><?= e($estado['texto']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if (Auth::puedeVisitantes()): ?>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Movimiento del mes</h2>
                <a class="boton-enlace" href="<?= url('finanzas') ?>">Ver finanzas <?= icono('flecha', 15) ?></a>
            </div>
            <div class="panel-cuerpo">
                <?php
                $maximo = max($mes['taquilla'] + $mes['otrosIngresos'], $mes['egresos'], 1);
                $porcentajeIngresos = (int) round((($mes['taquilla'] + $mes['otrosIngresos']) / $maximo) * 100);
                $porcentajeEgresos = (int) round(($mes['egresos'] / $maximo) * 100);
                ?>
                <div class="ficha-lista">
                    <div>
                        <dt>Taquilla</dt>
                        <dd><?= e(dinero($mes['taquilla'])) ?></dd>
                    </div>
                    <div>
                        <dt>Tienda, cafetería y otros</dt>
                        <dd><?= e(dinero($mes['otrosIngresos'])) ?></dd>
                    </div>
                    <div>
                        <dt>Egresos</dt>
                        <dd><?= e(dinero($mes['egresos'])) ?></dd>
                    </div>
                </div>

                <div style="margin-top: 16px; display: grid; gap: 10px;">
                    <div>
                        <div class="barra-carga" style="justify-content: space-between; margin-bottom: 5px;">
                            <span class="etiqueta-superior">Ingresos</span>
                            <span class="barra-carga-valor"><?= $porcentajeIngresos ?>%</span>
                        </div>
                        <div class="medidor"><span class="es-ingreso" style="width: <?= $porcentajeIngresos ?>%"></span></div>
                    </div>
                    <div>
                        <div class="barra-carga" style="justify-content: space-between; margin-bottom: 5px;">
                            <span class="etiqueta-superior">Egresos</span>
                            <span class="barra-carga-valor"><?= $porcentajeEgresos ?>%</span>
                        </div>
                        <div class="medidor"><span class="es-egreso" style="width: <?= $porcentajeEgresos ?>%"></span></div>
                    </div>
                </div>

                <hr class="separador">

                <div class="ficha-lista">
                    <div>
                        <dt>Balance del mes</dt>
                        <dd style="color: <?= $mes['balance'] >= 0 ? 'var(--ok)' : 'var(--alto)' ?>;"><?= e(dinero($mes['balance'])) ?></dd>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="panel">
            <div class="panel-cabeza">
                <h2>Parámetros por hábitat</h2>
                <span class="panel-nota">Referencia técnica</span>
            </div>
            <div class="panel-cuerpo">
                <table class="tabla tabla-compacta">
                    <thead>
                        <tr>
                            <th scope="col">Hábitat</th>
                            <th scope="col" class="num">Temperatura</th>
                            <th scope="col" class="num">pH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="principal">Agua marina</td>
                            <td class="num">22 – 27 °C</td>
                            <td class="num">8.0 – 8.4</td>
                        </tr>
                        <tr>
                            <td class="principal">Agua dulce</td>
                            <td class="num">23 – 28 °C</td>
                            <td class="num">6.0 – 7.5</td>
                        </tr>
                        <tr>
                            <td class="principal">Agua salobre</td>
                            <td class="num">23 – 28 °C</td>
                            <td class="num">7.0 – 8.4</td>
                        </tr>
                    </tbody>
                </table>
                <p class="texto-tenue" style="font-size: 12px; margin-top: 14px;">
                    El semáforo de cada tanque compara estos valores con la ventana que toleran
                    todas las especies que lo habitan.
                </p>
            </div>
        </section>
    <?php endif; ?>
</div>
