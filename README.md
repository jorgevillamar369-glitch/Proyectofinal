# Acuario Aqua Viva

Sistema de administración para un acuario, hecho como proyecto final de
**Programación Web Avanzada** (ejercicio 45: administración de un acuario).

Usa HTML5, Bootstrap 5, CSS propio, JavaScript sin librerías adicionales, MySQL
(phpMyAdmin) y PHP con una arquitectura **MVC** y un único punto de entrada,
sin ningún framework externo.

## Cómo está organizado (MVC)

```
acuariovivo/
├── index.php                  Punto de entrada: recibe todas las peticiones
├── acuario_pwa.sql            Base de datos completa (raíz del proyecto)
├── acuario_pwa_hosting.sql    La misma base, sin crear ni seleccionar la BD
├── .htaccess                  Índice del sitio
├── .gitignore                 Excluye config.php y los comprimidos
├── app/
│   ├── bootstrap.php          Configuración, utilidades y autoload
│   ├── config/
│   │   ├── config.php         Conexión y datos del servidor
│   │   └── config.example.php Plantilla para publicar
│   ├── core/
│   │   ├── Database.php       Conexión PDO única
│   │   ├── Router.php         Traduce ?ruta=recurso/accion
│   │   ├── Controller.php     Controlador base
│   │   ├── View.php           Carga plantilla + vista
│   │   ├── Auth.php           Sesión, roles y permisos
│   │   └── helpers.php        Formatos, CSRF, iconos y diagnósticos
│   ├── controllers/           Lógica de cada módulo
│   │   ├── InicioController.php
│   │   ├── AccesoController.php
│   │   ├── PanelController.php
│   │   ├── TanqueController.php
│   │   ├── PezController.php
│   │   ├── VisitanteController.php
│   │   ├── EntradaController.php
│   │   ├── FinanzaController.php
│   │   └── UsuarioController.php
│   ├── models/                Consultas a MySQL
│   │   ├── Usuario.php
│   │   ├── Tanque.php
│   │   ├── Especie.php
│   │   ├── Pez.php
│   │   ├── Visitante.php
│   │   ├── Entrada.php
│   │   ├── Finanza.php
│   │   └── Reporte.php
│   └── views/
│       ├── layouts/           Plantillas (pública, panel, acceso, ticket)
│       ├── inicio.php, acceso.php, panel.php
│       ├── tanques/, peces/, visitantes/, entradas/, finanzas/, usuarios/
│       └── errores/404.php
├── assets/
│   ├── css/estilos.css
│   ├── js/app.js
│   ├── fonts/                 Newsreader y Work Sans (locales)
│   ├── img/
│   └── vendor/bootstrap/      Bootstrap 5 local
└── README.md
```

Las URLs internas se generan con `url('recurso/accion')`, por ejemplo
`index.php?ruta=tanques/editar&id=3`. Así el proyecto funciona en cualquier
hospedaje sin configurar reglas de reescritura.

## Módulos y permisos

| Módulo | Administrador | Encargado de peces | Encargado de visitantes |
| --- | :---: | :---: | :---: |
| Resumen | Sí | Sí | Sí |
| Tanques | Sí | Sí | No |
| Peces | Sí | Sí | No |
| Visitantes | Sí | No | Sí |
| Entradas y ticket | Sí | No | Sí |
| Finanzas | Sí | No | Sí |
| Personal | Sí | No | No |

Funciones destacadas:

- Contraseñas cifradas con `password_hash` y sesiones seguras.
- Permisos comprobados en el controlador, no solo en el menú.
- Semáforo de parámetros: compara la temperatura y el pH de cada tanque con la
  ventana que toleran todas las especies que lo habitan.
- Carga biológica con la regla de 10 litros de agua por centímetro de pez.
- Diagnóstico del agua en vivo mientras se edita un tanque (JavaScript).
- Ficha de la especie que se completa al elegirla en el formulario de peces.
- Venta de entradas con total calculado en el servidor y ticket imprimible.
- Protección CSRF en todos los formularios y confirmación antes de eliminar.
- Portada pública con horarios, tarifas y colecciones.

## Requisitos

- PHP 8.1 o superior con `pdo_mysql`.
- MySQL 8 o MariaDB 10.4 o superior.
- Laragon, XAMPP o WAMP (también funciona con el servidor integrado de PHP).

## Instalación local

1. Copia la carpeta del proyecto dentro de tu servidor web.
2. Importa `acuario_pwa.sql` desde phpMyAdmin. Ese archivo crea la base
   `acuario_pwa`, las tablas, la vista `vista_ocupacion` y los datos de ejemplo.
3. Si no existe `app/config/config.php`, copia `app/config/config.example.php`
   con ese nombre y ajusta los datos de MySQL. En Laragon el puerto puede ser
   `3307`; en XAMPP normalmente `3306`.
4. Abre `http://localhost/<carpeta>/index.php`.

Con el servidor integrado:

```bash
php -S localhost:8091 -t .
```

## Publicar en el hosting

1. Comprueba que el hosting tenga **PHP 8.1 o superior** con `pdo_mysql` y
   `mbstring`.
2. Crea la base de datos desde el panel del hosting. Allí te dan un nombre con
   prefijo (por ejemplo `if0_123456_acuario`), un usuario y una contraseña.
3. En phpMyAdmin, selecciona esa base e importa **`acuario_pwa_hosting.sql`**.
   Ese archivo no intenta crear ni cambiar de base, así que funciona en
   cualquier hospedaje compartido. Las tablas y los datos son los mismos.
4. Edita `app/config/config.php` con los datos del paso 2. Normalmente:
   `DB_HOST = 'localhost'`, `DB_PORT = '3306'`, `DB_NOMBRE` con el prefijo,
   `DB_USUARIO`, `DB_CLAVE`. Deja `APP_DEBUG` en `false`.
5. Sube el **contenido** de la carpeta a `htdocs` (o `public_html`), no la
   carpeta contenedora. La estructura que debe verse es `index.php`, `app/`,
   `assets/` y los dos `.sql`.
6. Abre `https://tu-sitio/index.php` y prueba el acceso con las cuentas de
   demostración.
7. Cambia las contraseñas desde **Personal** y elimina o desactiva las cuentas
   que no uses.

El archivo `app/.htaccess` bloquea el acceso directo al código interno en
servidores Apache, que es lo habitual en los hostings gratuitos.

## Subir a GitHub

```bash
git init
git add .
git commit -m "Proyecto Acuario Aqua Viva en MVC"
git branch -M main
git remote add origin https://github.com/TU-USUARIO/TU-REPO.git
git push -u origin main
```

- `app/config/config.php` está en `.gitignore` porque contiene contraseñas; el
  repositorio conserva `config.example.php` como plantilla.
- La base de datos debe quedar en la **raíz** del repositorio (`acuario_pwa.sql`),
  como pide la tarea.
- Si otra persona clona el proyecto, debe copiar `config.example.php` como
  `config.php` y completar sus datos.

## Cuentas de demostración

| Perfil | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@aquaviva.ec` | `acuario2026` |
| Encargado de peces | `peces@aquaviva.ec` | `acuario2026` |
| Encargado de visitantes | `visitantes@aquaviva.ec` | `acuario2026` |

Cambia estas claves desde **Personal** antes de publicar.

## Rutas principales

| Ruta | Acción |
| --- | --- |
| `index.php` | Portada pública |
| `index.php?ruta=acceso` | Formulario de acceso |
| `index.php?ruta=panel` | Resumen según el rol |
| `index.php?ruta=tanques` | Listado de tanques |
| `index.php?ruta=tanques/crear` | Nuevo tanque |
| `index.php?ruta=peces` | Listado de peces |
| `index.php?ruta=visitantes` | Directorio de visitantes |
| `index.php?ruta=entradas` | Taquilla |
| `index.php?ruta=finanzas` | Ingresos y egresos |
| `index.php?ruta=usuarios` | Personal |

Los formularios usan POST y las bajas pasan por `.../eliminar` con token CSRF.

## Créditos

- Fotografías: Wikimedia Commons (CC0 y dominio público).
- Tipografías: Newsreader y Work Sans (SIL Open Font License), locales.
- Bootstrap 5.3.3 (MIT).

Proyecto académico. No representa un acuario real.
