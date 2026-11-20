/* =====================================================================
   Acuario Aqua Viva · Interacciones del panel
   JavaScript puro, sin librerías adicionales a Bootstrap 5.
   ===================================================================== */
(function () {
    'use strict';

    /* -----------------------------------------------------------------
       1. Menú lateral en pantallas pequeñas
       ----------------------------------------------------------------- */
    const botonMenu = document.getElementById('botonMenu');
    const velo = document.getElementById('velo');

    function cerrarMenu(devolverFoco) {
        const estabaAbierto = document.body.classList.contains('menu-abierto');
        document.body.classList.remove('menu-abierto');
        if (botonMenu) {
            botonMenu.setAttribute('aria-expanded', 'false');
        }
        if (velo) {
            velo.hidden = true;
        }
        if (estabaAbierto && devolverFoco !== false && botonMenu) {
            botonMenu.focus();
        }
    }

    if (botonMenu) {
        botonMenu.addEventListener('click', function () {
            const abierto = document.body.classList.toggle('menu-abierto');
            botonMenu.setAttribute('aria-expanded', String(abierto));
            if (velo) {
                velo.hidden = !abierto;
            }

            if (abierto) {
                const primerEnlace = document.querySelector('.lateral .lateral-enlace');
                if (primerEnlace) {
                    primerEnlace.focus();
                }
            } else {
                botonMenu.focus();
            }
        });
    }

    if (velo) {
        velo.addEventListener('click', cerrarMenu);
    }

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            cerrarMenu();
        }
    });

    /* -----------------------------------------------------------------
       2. Barras de ocupación de los tanques
       ----------------------------------------------------------------- */
    const barras = document.querySelectorAll('.barra-carga-relleno[data-valor]');

    if (barras.length) {
        window.requestAnimationFrame(function () {
            barras.forEach(function (barra) {
                const valor = Math.min(parseFloat(barra.dataset.valor) || 0, 100);
                barra.style.width = valor + '%';
                if (barra.dataset.clase) {
                    barra.classList.add('es-' + barra.dataset.clase);
                }
            });
        });
    }

    /* -----------------------------------------------------------------
       3. Filtro de tablas (busca en todas las celdas de la fila)
       ----------------------------------------------------------------- */
    document.querySelectorAll('[data-filtro]').forEach(function (caja) {
        const tabla = document.querySelector(caja.dataset.filtro);
        if (!tabla) {
            return;
        }

        const contador = caja.dataset.contador ? document.querySelector(caja.dataset.contador) : null;
        const filas = Array.prototype.slice.call(tabla.querySelectorAll('tbody tr'));

        caja.addEventListener('input', function () {
            const texto = caja.value.trim().toLowerCase();
            let visibles = 0;

            filas.forEach(function (fila) {
                const coincide = fila.textContent.toLowerCase().indexOf(texto) !== -1;
                fila.hidden = !coincide;
                if (coincide) {
                    visibles += 1;
                }
            });

            if (contador) {
                contador.textContent = visibles;
            }
        });
    });

    /* -----------------------------------------------------------------
       4. Confirmación antes de eliminar (modal de Bootstrap)
       ----------------------------------------------------------------- */
    const formulariosEliminar = document.querySelectorAll('form.js-eliminar');

    if (formulariosEliminar.length) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'modalEliminar';
        modal.tabIndex = -1;
        modal.setAttribute('aria-hidden', 'true');
        modal.setAttribute('aria-labelledby', 'modalEliminarTitulo');
        modal.innerHTML =
            '<div class="modal-dialog modal-dialog-centered">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<h2 class="modal-title" id="modalEliminarTitulo">Confirmar eliminación</h2>' +
                        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                        '<p id="modalEliminarTexto">Este registro se eliminará de forma permanente.</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<button type="button" class="boton-secundario" data-bs-dismiss="modal">Cancelar</button>' +
                        '<button type="button" class="boton" id="modalEliminarConfirmar">Sí, eliminar</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        document.body.appendChild(modal);

        const instancia = new bootstrap.Modal(modal);
        const texto = modal.querySelector('#modalEliminarTexto');
        const confirmar = modal.querySelector('#modalEliminarConfirmar');
        let formularioActivo = null;

        formulariosEliminar.forEach(function (formulario) {
            formulario.addEventListener('submit', function (evento) {
                evento.preventDefault();
                formularioActivo = formulario;
                texto.textContent = formulario.dataset.mensaje || 'Este registro se eliminará de forma permanente.';
                instancia.show();
            });
        });

        confirmar.addEventListener('click', function () {
            if (formularioActivo) {
                instancia.hide();
                formularioActivo.submit();
            }
        });
    }

    /* -----------------------------------------------------------------
       5. Diagnóstico en vivo del agua (formulario de tanques)
       ----------------------------------------------------------------- */
    const campoHabitat = document.getElementById('habitat');
    const campoTemperatura = document.getElementById('temperatura');
    const campoPh = document.getElementById('ph');
    const salidaDiagnostico = document.getElementById('diagnosticoAgua');

    if (campoHabitat && campoTemperatura && campoPh && salidaDiagnostico) {
        const rangos = {
            marino: { temp: [22, 27], ph: [8.0, 8.4] },
            dulce: { temp: [23, 28], ph: [6.0, 7.5] },
            salobre: { temp: [23, 28], ph: [7.0, 8.4] }
        };

        // Siempre un decimal, igual que los datos guardados en MySQL.
        const n1 = function (valor) {
            return Number(valor).toFixed(1);
        };

        function revisarAgua() {
            const rango = rangos[campoHabitat.value] || rangos.marino;
            const temperatura = parseFloat(campoTemperatura.value);
            const ph = parseFloat(campoPh.value);
            const problemas = [];

            if (!isNaN(temperatura) && (temperatura < rango.temp[0] || temperatura > rango.temp[1])) {
                problemas.push('temperatura');
            }
            if (!isNaN(ph) && (ph < rango.ph[0] || ph > rango.ph[1])) {
                problemas.push('pH');
            }

            const clase = problemas.length ? 'alerta' : 'ok';
            const mensaje = problemas.length
                ? 'Revisar ' + problemas.join(' y ') + ': el rango sugerido es ' +
                    n1(rango.temp[0]) + '–' + n1(rango.temp[1]) + ' °C y pH ' + n1(rango.ph[0]) + '–' + n1(rango.ph[1]) + '.'
                : 'Dentro del rango sugerido para ' + campoHabitat.options[campoHabitat.selectedIndex].text.toLowerCase() +
                    ': ' + n1(rango.temp[0]) + '–' + n1(rango.temp[1]) + ' °C y pH ' + n1(rango.ph[0]) + '–' + n1(rango.ph[1]) + '.';

            salidaDiagnostico.innerHTML =
                '<span class="pildora pildora--' + clase + '">' + (problemas.length ? 'Revisar parámetros' : 'Parámetros estables') + '</span>' +
                '<span>' + mensaje + '</span>';
        }

        [campoHabitat, campoTemperatura, campoPh].forEach(function (campo) {
            campo.addEventListener('input', revisarAgua);
            campo.addEventListener('change', revisarAgua);
        });

        revisarAgua();
    }

    /* -----------------------------------------------------------------
       6. Ficha de la especie en el formulario de peces
       ----------------------------------------------------------------- */
    const selectorEspecie = document.getElementById('id_especie');
    const datosEspecies = document.getElementById('datosEspecies');

    if (selectorEspecie && datosEspecies) {
        let especies = {};
        try {
            especies = JSON.parse(datosEspecies.textContent);
        } catch (error) {
            especies = {};
        }

        const nombreCientifico = document.getElementById('nombre_cientifico');
        const fichaEspecie = document.getElementById('fichaEspecie');
        const campoTamanio = document.getElementById('tamanio_cm');
        const avisoTamanio = document.getElementById('avisoTamanio');

        function mostrarEspecie() {
            const especie = especies[selectorEspecie.value];

            if (!especie) {
                if (nombreCientifico) {
                    nombreCientifico.value = '';
                }
                if (fichaEspecie) {
                    fichaEspecie.textContent = 'Elige una especie para ver su ficha técnica.';
                }
                return;
            }

            if (nombreCientifico) {
                nombreCientifico.value = especie.nombre_cientifico;
            }
            if (fichaEspecie) {
                fichaEspecie.textContent = especie.habitat_texto + ' · ' +
                    n1(especie.temp_min) + '–' + n1(especie.temp_max) + ' °C · pH ' +
                    n1(especie.ph_min) + '–' + n1(especie.ph_max) + ' · hasta ' + especie.tamanio_max_cm + ' cm · ' + especie.dieta;
            }
            revisarTamanio();
        }

        function revisarTamanio() {
            if (!avisoTamanio || !campoTamanio) {
                return;
            }
            const especie = especies[selectorEspecie.value];
            const tamanio = parseFloat(campoTamanio.value);

            if (especie && !isNaN(tamanio) && tamanio > especie.tamanio_max_cm) {
                avisoTamanio.textContent = 'El tamaño registrado supera el máximo de la especie (' +
                    especie.tamanio_max_cm + ' cm). Verifica la medición.';
                avisoTamanio.hidden = false;
            } else {
                avisoTamanio.hidden = true;
            }
        }

        selectorEspecie.addEventListener('change', mostrarEspecie);
        if (campoTamanio) {
            campoTamanio.addEventListener('input', revisarTamanio);
        }
        mostrarEspecie();
    }

    /* -----------------------------------------------------------------
       7. Total de la venta de entradas
       ----------------------------------------------------------------- */
    const campoCantidad = document.getElementById('cantidad');
    const campoTarifa = document.getElementById('tarifa');
    const salidaTotal = document.getElementById('totalEntrada');
    const datosTarifas = document.getElementById('datosTarifas');

    if (campoCantidad && campoTarifa && salidaTotal && datosTarifas) {
        let precios = {};
        try {
            precios = JSON.parse(datosTarifas.textContent);
        } catch (error) {
            precios = {};
        }

        function calcularTotal() {
            const cantidad = Math.max(parseInt(campoCantidad.value, 10) || 1, 1);
            const precio = precios[campoTarifa.value] || 0;
            const total = cantidad * precio;
            salidaTotal.textContent = '$' + total.toFixed(2);
        }

        campoCantidad.addEventListener('input', calcularTotal);
        campoTarifa.addEventListener('change', calcularTotal);
        calcularTotal();
    }

    /* -----------------------------------------------------------------
       8. Accesibilidad de formularios y botones
       ----------------------------------------------------------------- */
    document.querySelectorAll('.campo-error').forEach(function (grupo) {
        const control = grupo.querySelector('input, select, textarea');
        const error = grupo.querySelector('.error-campo');

        if (!control || !error) {
            return;
        }

        if (!error.id) {
            const base = control.id || 'campo';
            error.id = 'error-' + base + '-' + Math.random().toString(36).slice(2, 6);
        }

        control.setAttribute('aria-invalid', 'true');
        control.setAttribute('aria-describedby', error.id);
    });

    const primerError = document.querySelector('.campo-error input, .campo-error select, .campo-error textarea');
    if (primerError) {
        primerError.focus();
    }

    document.querySelectorAll('a[title], button[title]').forEach(function (elemento) {
        if (!elemento.hasAttribute('aria-label')) {
            elemento.setAttribute('aria-label', elemento.getAttribute('title'));
        }
    });
})();
