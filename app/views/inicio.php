<?php
/**
 * Portada pública del acuario.
 * Variables: $tanquesActivos, $totalEspecies, $litros, $destacados, $imagenes, $numeros, $horarios.
 */
?>
<section class="portada">
    <img class="portada-imagen" src="assets/img/hero-tanque.jpg"
         alt="Tanque principal del acuario visto desde el ventanal, con visitantes y un buceador dentro del agua">
    <div class="portada-interior">
        <div class="portada-texto">
            <p class="kicker">Quito · Ecuador</p>
            <h1>Acuario en el norte de Quito</h1>
            <p>
                El acuario tiene <?= (int) $tanquesTotal ?> tanques instalados y <?= (int) $tanquesActivos ?> abiertos al público.
                La visita dura una hora y pasa por tiburones toro, mantas, medusas y los peces del Amazonas.
            </p>

            <div class="portada-datos">
                <div>
                    <strong><?= (int) $tanquesActivos ?></strong>
                    <span>tanques en exhibición</span>
                </div>
                <div>
                    <strong><?= (int) $totalEspecies ?></strong>
                    <span>especies registradas</span>
                </div>
                <div>
                    <strong><?= number_format((int) $litros, 0, ',', '.') ?> L</strong>
                    <span>de agua en circulación</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="seccion" id="colecciones">
    <div class="seccion-cabeza">
        <div>
            <p class="kicker" style="color: var(--acento);">Colecciones permanentes</p>
            <h2>Los tanques principales</h2>
        </div>
    </div>

    <div class="colecciones">
        <?php foreach ($destacados as $tanque):
            $imagen = $imagenes[$tanque['codigo']] ?? null;
            if (!$imagen) {
                continue;
            }
            ?>
            <article class="coleccion">
                <figure>
                    <img src="<?= e($imagen['archivo']) ?>" alt="<?= e($imagen['alt']) ?>" loading="lazy">
                </figure>
                <p class="coleccion-numero"><?= e($numeros[$tanque['codigo']] ?? '00') ?></p>
                <h3><?= e($tanque['nombre']) ?></h3>
                <p>
                    <?= $tanque['habitat'] === 'dulce'
                        ? 'Agua dulce del Amazonas, con filtro de turba y corriente lenta.'
                        : ($tanque['habitat'] === 'salobre'
                            ? 'Mezcla de agua de río y de mar; aquí vive el tiburón toro.'
                            : 'Arrecife marino con anémonas, corales y peces de cardumen.') ?>
                </p>
                <p class="coleccion-meta">
                    <?= e($tanque['codigo']) ?> · <?= number_format((int) $tanque['capacidad'], 0, ',', '.') ?> litros ·
                    <?= number_format((float) $tanque['temperatura'], 1) ?> °C
                </p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="visita" id="visita">
    <div class="visita-interior">
        <div>
            <p class="kicker" style="color: var(--acento);">Planifica tu visita</p>
            <h2>Horarios</h2>
            <p class="visita-intro">
                La taquilla cierra 45 minutos antes. Los lunes el acuario permanece cerrado
                para el cambio de agua y la revisión de los sistemas de bombeo.
            </p>

            <ul class="lista-horarios">
                <?php foreach ($horarios as $dia => $hora): ?>
                    <li>
                        <span><?= e($dia) ?></span>
                        <span><?= e($hora) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <p class="kicker" style="color: var(--acento);">Taquilla</p>
            <h2>Tarifas</h2>
            <p class="visita-intro">Precios en dólares, por persona. Los menores de 3 años no pagan.</p>

            <table class="tarifas">
                <caption>Precios por persona</caption>
                <tbody>
                    <?php foreach (tarifas() as $clave => $precio): ?>
                        <tr>
                            <th scope="row"><?= e(texto_tarifa($clave)) ?></th>
                            <td><?= e(dinero($precio)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="nota-tarifas">
                Los grupos escolares deben reservar con 48 horas de anticipación.
                La visita guiada está incluida en la entrada del sábado.
            </p>
        </div>
    </div>
</section>

<section class="banda">
    <div class="banda-interior">
        <div>
            <h2>¿Trabajas en el acuario?</h2>
            <p>El panel de administración permite registrar peces, tanques, visitantes y entradas.</p>
        </div>
        <a class="boton-claro" href="<?= url('acceso') ?>">Entrar al sistema</a>
    </div>
</section>
