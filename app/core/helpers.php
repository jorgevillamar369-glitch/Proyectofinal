<?php
/**
 * Funciones de apoyo compartidas por controladores y vistas.
 */

declare(strict_types=1);

/** Conexión a la base de datos. */
function db(): PDO
{
    return Database::conexion();
}

/** URL interna del proyecto. */
function url(string $ruta = '', array $parametros = []): string
{
    $direccion = 'index.php';

    if ($ruta !== '') {
        $direccion .= '?ruta=' . ltrim($ruta, '/');
    }

    if ($parametros !== []) {
        $direccion .= ($ruta === '' ? '?' : '&') . http_build_query($parametros);
    }

    return $direccion;
}

/** Escapa texto antes de imprimirlo en HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function redirigir(string $direccion): never
{
    header('Location: ' . $direccion);
    exit;
}

/* ------------------------------------------------------------------
 * Mensajes de una sola lectura
 * ------------------------------------------------------------------ */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function tomar_flash(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}

/* ------------------------------------------------------------------
 * Formularios y CSRF
 * ------------------------------------------------------------------ */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verificar_csrf(): void
{
    $enviado = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals(csrf_token(), $enviado)) {
        http_response_code(419);
        exit('El formulario expiró. Vuelve atrás, recarga la página e inténtalo otra vez.');
    }
}

function exigir_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        redirigir(url('panel'));
    }
}

/* ------------------------------------------------------------------
 * Formatos
 * ------------------------------------------------------------------ */
function dinero(float|int|string $valor): string
{
    return '$' . number_format((float) $valor, 2, '.', ',');
}

function fecha_corta(?string $fecha): string
{
    return $fecha ? date('d/m/Y', strtotime($fecha)) : '—';
}

function fecha_hora(?string $fecha): string
{
    return $fecha ? date('d/m/Y · H:i', strtotime($fecha)) : '—';
}

function fecha_larga(?string $fecha): string
{
    if (!$fecha) {
        return '—';
    }
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $tiempo = strtotime($fecha);
    return date('j', $tiempo) . ' de ' . $meses[(int) date('n', $tiempo) - 1] . ' de ' . date('Y', $tiempo);
}

function nombre_mes(string $anioMes): string
{
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    [$anio, $mes] = array_pad(array_map('intval', explode('-', $anioMes)), 2, 1);
    if ($mes < 1 || $mes > 12) {
        $mes = 1;
    }
    return $meses[$mes - 1] . ' de ' . $anio;
}

function hora_corta(?string $hora): string
{
    return $hora ? substr($hora, 0, 5) : '—';
}

function texto_habitat(string $habitat): string
{
    return [
        'marino'  => 'Agua marina',
        'dulce'   => 'Agua dulce',
        'salobre' => 'Agua salobre',
    ][$habitat] ?? $habitat;
}

function etiqueta_estado_tanque(string $estado): array
{
    return [
        'operativo'     => ['texto' => 'Operativo',     'clase' => 'ok'],
        'mantenimiento' => ['texto' => 'Mantenimiento', 'clase' => 'alerta'],
        'cuarentena'    => ['texto' => 'Cuarentena',    'clase' => 'neutral'],
    ][$estado] ?? ['texto' => $estado, 'clase' => 'neutral'];
}

function etiqueta_estado_pez(string $estado): array
{
    return [
        'saludable'   => ['texto' => 'Saludable',      'clase' => 'ok'],
        'observacion' => ['texto' => 'En observación', 'clase' => 'alerta'],
        'tratamiento' => ['texto' => 'En tratamiento', 'clase' => 'alerta'],
        'trasladado'  => ['texto' => 'Trasladado',     'clase' => 'neutral'],
    ][$estado] ?? ['texto' => $estado, 'clase' => 'neutral'];
}

function texto_tarifa(string $tarifa): string
{
    return [
        'general'      => 'General',
        'estudiante'   => 'Estudiante',
        'nino'         => 'Niño / niña',
        'adulto_mayor' => 'Adulto mayor',
    ][$tarifa] ?? $tarifa;
}

function texto_pago(string $pago): string
{
    return [
        'efectivo'      => 'Efectivo',
        'tarjeta'       => 'Tarjeta',
        'transferencia' => 'Transferencia',
    ][$pago] ?? $pago;
}

function tarifas(): array
{
    return [
        'general'      => 6.00,
        'estudiante'   => 4.00,
        'nino'         => 3.00,
        'adulto_mayor' => 2.50,
    ];
}

/* ------------------------------------------------------------------
 * Ocupación y diagnóstico del agua
 * ------------------------------------------------------------------ */
function clase_carga(float $porcentaje, string $estadoTanque = 'operativo'): array
{
    if ($estadoTanque === 'mantenimiento') {
        return ['clase' => 'neutral', 'texto' => 'Fuera de servicio'];
    }
    if ($porcentaje > 100) {
        return ['clase' => 'alto', 'texto' => 'Sobrepoblado'];
    }
    if ($porcentaje >= 80) {
        return ['clase' => 'alerta', 'texto' => 'Carga alta'];
    }
    return ['clase' => 'ok', 'texto' => 'Carga adecuada'];
}

function diagnostico_tanque(array $tanque, ?array $rango): array
{
    if (!$rango || $rango['temp_min'] === null) {
        return ['clase' => 'neutral', 'texto' => 'Sin población', 'problemas' => []];
    }

    $problemas = [];
    $temperatura = (float) $tanque['temperatura'];
    $ph = (float) $tanque['ph'];

    $ventana = static function (float $minimo, float $maximo): string {
        return $minimo === $maximo
            ? number_format($minimo, 1)
            : number_format($minimo, 1) . '–' . number_format($maximo, 1);
    };

    if ($temperatura < (float) $rango['temp_min'] || $temperatura > (float) $rango['temp_max']) {
        $problemas[] = 'Temperatura de ' . number_format($temperatura, 1)
            . ' °C; las especies necesitan ' . $ventana((float) $rango['temp_min'], (float) $rango['temp_max']) . ' °C.';
    }
    if ($ph < (float) $rango['ph_min'] || $ph > (float) $rango['ph_max']) {
        $problemas[] = 'pH de ' . number_format($ph, 1)
            . '; las especies necesitan pH ' . $ventana((float) $rango['ph_min'], (float) $rango['ph_max']) . '.';
    }

    return [
        'clase'     => $problemas ? 'alerta' : 'ok',
        'texto'     => $problemas ? 'Revisar parámetros' : 'Parámetros estables',
        'problemas' => $problemas,
    ];
}

/* ------------------------------------------------------------------
 * Iconos SVG (sin emojis)
 * ------------------------------------------------------------------ */
function icono(string $nombre, int $tamano = 20): string
{
    $trazos = [
        'panel'    => '<rect x="3" y="3" width="7" height="7" rx="1.2"/><rect x="14" y="3" width="7" height="7" rx="1.2"/><rect x="3" y="14" width="7" height="7" rx="1.2"/><rect x="14" y="14" width="7" height="7" rx="1.2"/>',
        'tanque'   => '<rect x="3" y="5" width="18" height="14" rx="1.6"/><path d="M3 12c2.2 1.7 4.4 1.7 6.6 0s4.4-1.7 6.6 0 4.4 1.7 6.6 0"/><path d="M3 16.4c2.2 1.7 4.4 1.7 6.6 0s4.4-1.7 6.6 0 4.4 1.7 6.6 0"/>',
        'pez'      => '<path d="M3.5 12c3.6-4.6 8.6-4.6 12.2 0-3.6 4.6-8.6 4.6-12.2 0Z"/><path d="M15.7 12 20 8.8v6.4L15.7 12Z"/><circle cx="7.4" cy="11.2" r=".9"/>',
        'entrada'  => '<path d="M4 8.5V6.8A1.8 1.8 0 0 1 5.8 5h12.4A1.8 1.8 0 0 1 20 6.8v1.7a2.2 2.2 0 0 0 0 3.4v1.7"/><path d="M20 13.6v3.6a1.8 1.8 0 0 1-1.8 1.8H5.8A1.8 1.8 0 0 1 4 17.2v-1.7"/><path d="M14 3v18" stroke-dasharray="2.4 2.6"/>',
        'dinero'   => '<rect x="3" y="7" width="18" height="12" rx="2"/><path d="M3 10.5h18"/><path d="M16.5 15.2h2.2"/>',
        'personas' => '<circle cx="9" cy="8.5" r="3.2"/><path d="M3.4 19.2c0-3.1 2.5-5.2 5.6-5.2s5.6 2.1 5.6 5.2"/><path d="M16.2 6.2a2.6 2.6 0 0 1 0 5"/><path d="M18.2 14.6c1.7.6 2.8 2.1 2.8 4.6"/>',
        'carnet'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.6" cy="10.4" r="2"/><path d="M5.6 16.3c.6-1.6 1.6-2.4 3-2.4s2.4.8 3 2.4"/><path d="M14.2 9.6H18M14.2 13.2H18"/>',
        'salir'    => '<path d="M9 4H6.5A1.5 1.5 0 0 0 5 5.5v13A1.5 1.5 0 0 0 6.5 20H9"/><path d="M14 8l4 4-4 4"/><path d="M18 12H9"/>',
        'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'mas'      => '<path d="M12 5v14M5 12h14"/>',
        'lupa'     => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
        'lapiz'    => '<path d="m4 20 1-4.5L15.6 4.9a1.7 1.7 0 0 1 2.4 0l1.1 1.1a1.7 1.7 0 0 1 0 2.4L8.5 19 4 20Z"/><path d="M14.5 6.5 17.5 9.5"/>',
        'basura'   => '<path d="M4 7h16"/><path d="M9 7V5h6v2"/><path d="M6.5 7l.8 12A1.7 1.7 0 0 0 9 20.5h6a1.7 1.7 0 0 0 1.7-1.5l.8-12"/><path d="M10 11v6M14 11v6"/>',
        'alerta'   => '<path d="M12 4 2.8 20h18.4L12 4Z"/><path d="M12 10v4.5"/><circle cx="12" cy="17.4" r=".9"/>',
        'flecha'   => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'ojo'      => '<path d="M2.5 12S6 6.5 12 6.5 21.5 12 21.5 12 18 17.5 12 17.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
        'ok'       => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'volver'   => '<path d="M19 12H5"/><path d="m11 6-6 6 6 6"/>',
    ];

    $contenido = $trazos[$nombre] ?? $trazos['panel'];

    return '<svg class="icono" width="' . $tamano . '" height="' . $tamano . '" viewBox="0 0 24 24" fill="none" '
        . 'stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . $contenido . '</svg>';
}
