# AVANCE

Estado real del proyecto. **Se actualiza al cerrar cada sesión de trabajo y al terminar cada fase.**

Quien retome el trabajo —persona o asistente de IA— tiene que poder leer solo este archivo y saber en qué punto está todo. Las entradas nuevas van **arriba**. Las anteriores no se editan ni se borran: si algo cambió, se escribe una entrada nueva que lo diga.

---

## Estado general

| | |
|---|---|
| **Fase actual** | 3 — Gestión de videos (código completo, en revisión) |
| **Última actualización** | 31 de julio de 2026 |
| **Entorno de staging** | Sin desplegar |
| **Entorno de producción** | Sin desplegar |
| **Bloqueos activos** | Sin credenciales de Railway ni de Cloudflare (de tu lado). Ninguna fase puede darse por "Cerrada" hasta que haya un despliegue en staging probado a mano, según la regla de docs/04-plan-de-fases.md. Por eso las fases 0 a 3 quedan en "En revisión". |

### Progreso por fase

| Fase | Estado |
|---|---|
| 0 — Fundaciones | En revisión |
| 1 — Autenticación y roles | En revisión |
| 2 — Portfolio público | En revisión |
| 3 — Gestión de videos | En revisión |
| 4 — Clientes y accesos | Sin empezar |
| 5 — Agenda y calendario | Sin empezar |
| 6 — Publicaciones | Sin empezar |
| 7 — Importación y conciliación | Sin empezar |
| 8 — Métricas del período | Sin empezar |
| 9 — Pedidos y notificaciones | Sin empezar |
| 10 — Endurecimiento y producción | Sin empezar |

Estados posibles: Sin empezar · En curso · En revisión · Cerrada

---

## Decisiones vigentes

Decisiones tomadas que condicionan el desarrollo. Si una se revierte, se anota en la bitácora y se corrige acá.

| Fecha | Decisión | Motivo |
|---|---|---|
| 31/07/2026 | Laravel 12 con Livewire 3, sin API separada | Un solo consumidor y equipo chico |
| 31/07/2026 | Railway para aplicación y base; Cloudflare para DNS, WAF y R2 | Infraestructura ya elegida por el titular |
| 31/07/2026 | Videos del portfolio en YouTube o Vimeo sin listar | Evita el costo de transcodificar y servir video |
| 31/07/2026 | La versión 1 no se conecta a Meta Graph API; trabaja por importación de archivo | Menos permisos, menos tokens que renovar |
| 31/07/2026 | Los clientes se archivan, no se eliminan | Conserva historial y es reversible |
| 31/07/2026 | Sin estado de aprobación en pedidos | Se evalúa con datos de uso real |
| 31/07/2026 | El cliente no propone cambios de fecha desde el sistema | Exigiría negociación de estados |
| 31/07/2026 | PHP 8.4 en el entorno local de desarrollo (Herd), en vez del 8.3 de docs/02 | Es lo único disponible en esta máquina. Laravel 12 soporta 8.2 a 8.4 sin cambios de código; el `Dockerfile` de producción sigue fijado a `serversideup/php:8.3-fpm-nginx` tal como pide la documentación, así que no hay divergencia real entre entornos productivos |
| 31/07/2026 | SQLite para desarrollo y pruebas en este entorno, en vez de PostgreSQL | Este entorno no tiene Docker, PostgreSQL ni Redis instalados (se comprobó al arrancar la fase 0). `.env.example`, `docker-compose.yml` y `railway.json` siguen documentando y usando PostgreSQL 16 y Redis como pide docs/02; `.env` local es la única excepción, con una nota explícita en el archivo. Las migraciones evitan tipos exclusivos de un motor para no romper este acuerdo |
| 31/07/2026 | Sin Xdebug ni PCOV instalados localmente | No se pudo medir el porcentaje real de cobertura en esta máquina (`php artisan test --coverage` pide un driver). El paso de integración continua sí lo instala (`shivammathur/setup-php` con `coverage: xdebug`) y exige 70 % general; falta confirmarlo en la primera corrida real de CI |
| 31/07/2026 | Autenticación propia con Livewire, en vez de Laravel Fortify | Fortify resuelve menos de lo que parece para este caso (2FA obligatorio solo para admin, cliente archivado, límite por IP con Cloudflare de por medio) y termina exigiendo casi la misma cantidad de código para personalizarlo. Se implementó a mano sobre `Auth`, `RateLimiter` y `pragmarx/google2fa`, con pruebas de cada camino |
| 31/07/2026 | Hash Argon2id vía `config/hashing.php` (`HASH_DRIVER=argon2id`) | Cumple docs/02 §6.2. Se publicó el archivo de configuración porque Laravel 12 no lo trae por defecto |
| 31/07/2026 | El filtro de categorías del portfolio se resuelve en el cliente con Alpine (incluido en Livewire), sin round-trip al servidor | Cumple RF-03 ("no recarga la página") y el margen de 100 ms de la fase 2 sin depender de la latencia de red. Todas las piezas publicadas se renderizan una sola vez; el filtro solo cambia qué se ve |
| 31/07/2026 | Sin credenciales de Cloudflare R2, los discos `r2` y `r2_privado` caen automáticamente a almacenamiento local (`AppServiceProvider::usarAlmacenamientoLocalSinCredencialesDeR2`) | Permite desarrollar y correr las pruebas de carga de miniaturas sin la infraestructura real. En cuanto se carguen `R2_ENDPOINT` y las claves, el disco vuelve a S3/R2 sin tocar código |
| 31/07/2026 | La purga de caché de Cloudflare (`PurgadorCacheCloudflare`) no falla si faltan credenciales; deja constancia en el log | Mismo motivo que el punto anterior: no bloquear el desarrollo por infraestructura pendiente |
| 31/07/2026 | La miniatura de Vimeo no se resuelve automáticamente (solo la de YouTube) | La API de oEmbed de Vimeo requiere una llamada de red por video; se documentó como límite conocido en `ProveedorVideo` en vez de resolverlo con una integración a medias |
| 31/07/2026 | La imagen de Open Graph (`public/img/og-portada.jpg`) es un placeholder generado con GD, no una foto real | Todavía no hay fotos ni videos reales de Catalina (pendiente #4). Sin esto, compartir el enlace en WhatsApp no mostraría ninguna imagen |

---

## Pendientes abiertos

| # | Pendiente | Fase | Prioridad |
|---|---|---|---|
| 1 | Definir dominio definitivo | 0 | Alta |
| 2 | Elegir proveedor de correo saliente | 9 | Media |
| 3 | Confirmar si los archivos finales van en Drive o en R2 | 9 | Media |
| 4 | Conseguir los textos y las fotos reales para el portfolio | 2 | Alta |
| 5 | Credenciales de Railway (proyecto, servicios, variables de entorno) | 0 | Alta |
| 6 | Credenciales y configuración de Cloudflare (dominio, DNS, R2, WAF, Turnstile) | 0 | Alta |
| 7 | Confirmar el % de cobertura real en CI (no se pudo medir en esta máquina por falta de Xdebug/PCOV) | 0 | Media |
| 8 | Resolver miniatura automática de Vimeo (hoy solo funciona para YouTube) | 3 | Baja |

---

## Bitácora

### 31 de julio de 2026 — Fases 0 a 3: fundaciones, autenticación, portfolio público y gestión de videos

**Qué se hizo**

- **Fase 0.** Se creó el proyecto Laravel 12 (PHP 8.2+, corrido en este entorno con PHP 8.4 de Herd) con Livewire 3.8, Tailwind CSS 4, Pest 3, Pint y Larastan (Larastan nivel 6, sin errores). `Dockerfile` multietapa (compila assets con Node y arma la imagen `serversideup/php:8.3-fpm-nginx`), `docker-compose.yml` con `app`, `worker`, `scheduler`, `db` (Postgres 16) y `redis`, y `railway.json` con los tres servicios y `/up` como healthcheck. Workflow de GitHub Actions (`.github/workflows/integracion-continua.yml`) con los cinco pasos de docs/02 §10: Pint, Larastan, Pest con cobertura mínima 70 %, y `composer audit`. La ruta `/up` la resuelve Laravel 12 por defecto.
- **Fase 1.** Migraciones de `clientes` y `users` (con `rol`, `cliente_id`, `activo`, `two_factor_secret`, `ultimo_acceso_at`). Ingreso, recuperación y restablecimiento de clave (con invalidación del resto de las sesiones al cambiar la clave), segundo factor TOTP obligatorio para el rol admin (`pragmarx/google2fa` + QR con `bacon/bacon-qr-code`), límite de cinco intentos de ingreso por minuto por IP con `RateLimiter`. Aislamiento en tres niveles: global scope `App\Scopes\PerteneceAlCliente` (vía el trait `App\Concerns\PerteneceACliente`, ya aplicado a `User`), policy `ClientePolicy`, y middlewares `AsegurarRolAdmin` / `AsegurarRolCliente` que derivan el cliente siempre de la sesión (ninguna ruta de `/portal` acepta un `cliente_id` por parámetro, verificado por prueba). `TrustProxies` configurado con `TRUSTED_PROXIES`. Armazón vacío de `/panel` y `/portal` con navegación, siguiendo el mockup. Seeder con una administradora y cuatro clientes de ejemplo (Bloom Skincare, Casa Nima, Nube Café, Duna Joyas), con las mismas marcas del prototipo. Batería de aislamiento en `tests/Feature/Aislamiento/` (rutas por rol, global scope, policy).
- **Fase 2.** Tabla y modelo `Video` (con las seis categorías del filtro como constante). Portfolio público con las seis secciones del mockup (cabecera, portada con tira de piezas, trabajos, servicios, sobre mí, contacto, pie), adaptable desde 360 px. Filtro por categoría resuelto en el cliente con Alpine, sin recarga. Ventana de reproducción con YouTube y Vimeo vía el servicio `App\Services\ProveedorVideo` (detecta proveedor, arma la URL embebible y resuelve la miniatura del proveedor cuando no hay una propia). Etiquetas Open Graph y Twitter Card, datos estructurados JSON-LD de tipo `Person`, `sitemap.xml` dinámico y `robots.txt` que bloquea `/panel`, `/portal`, `/ingresar` y `/archivos`. Seeder con doce piezas de ejemplo, con los mismos datos del prototipo.
- **Fase 3.** Listado de videos en el panel (`App\Livewire\Panel\Videos\Listado`) con buscador por título y cliente, filtro por categoría, y los cuatro contadores (cargados, publicados, ocultos, destacados). Alternar publicado y destacado desde el listado, sin abrir el formulario. Baja con confirmación (`wire:confirm`) y definitiva. Alta y edición en un formulario modal (`App\Livewire\Panel\Videos\Formulario`) con las validaciones de docs/01 §4.1. Carga de miniatura a R2 con recorte a 9:16 y conversión a WebP (`App\Services\ProcesadorMiniatura`, con Intervention Image v3); sin miniatura propia, se usa la del proveedor. Purga de caché de Cloudflare (`App\Services\PurgadorCacheCloudflare`) al publicar, despublicar, guardar o eliminar un video.
- Se corrió la batería completa de pruebas después de cada fase: 47 pruebas, 131 aserciones, todas en verde. Pint sin diferencias pendientes. Larastan nivel 6 sin errores.

**Decisiones tomadas**

Ver la tabla de "Decisiones vigentes" más arriba: son todas de esta sesión, ya que es la primera con código.

**Pendiente o roto**

- **Nada de esto está desplegado.** No hay staging ni producción: sin acceso a Railway ni a Cloudflare no se puede cumplir el criterio de aceptación de la fase 0 ("un push a `develop` despliega a staging solo"), así que ninguna fase se puede cerrar todavía aunque el código y las pruebas estén completos. Ver "Pendientes abiertos".
- No se verificó la integración continua corriendo de verdad en GitHub Actions (no hay repositorio remoto todavía). El workflow está escrito y sigue los cinco pasos de docs/02 §10, pero su primera corrida real queda pendiente.
- No se pudo abrir un navegador en este entorno para probar el sitio a mano (el sandbox bloquea levantar `php artisan serve`); la verificación de la interfaz se apoyó en la batería de Pest sobre la capa HTTP y en Livewire Testing, no en una revisión visual real. Falta una revisión a mano en cuanto haya un entorno con navegador disponible.
- Sin fotos ni videos reales (pendiente #4): la tira animada de la portada y las miniaturas de la grilla y del listado del panel son bloques de color de la paleta, no imágenes.
- La miniatura automática de Vimeo no está resuelta (pendiente #8): si no se sube una miniatura propia, un video de Vimeo queda sin miniatura hasta que se resuelva.
- Turnstile en el formulario de ingreso: no se integró todavía porque no hay `TURNSTILE_SITE_KEY` ni `TURNSTILE_SECRET_KEY` reales; el formulario funciona sin él por ahora.
- Falta confirmar el `dominio definitivo` (pendiente #1) para completar `APP_URL`, las reglas de Cloudflare y las cabeceras `Content-Security-Policy` (esto último es trabajo de la fase 10, pero conviene tenerlo presente).

**Próximo paso**

1. De tu lado: acceso a Railway y a Cloudflare (proyecto, dominio, R2), para poder cerrar de verdad las fases 0 a 3 con el despliegue a staging probado a mano.
2. Fase 4: clientes y accesos (alta con transacción, archivado, restablecimiento de clave desde la ficha, ficha con las cuatro solapas vacías, portal del cliente con su marca). Ya existen `Cliente`, el global scope y la policy base: cada tabla nueva con `cliente_id` que se agregue de acá en adelante debe sumar su prueba de aislamiento en el mismo commit, como pide docs/04.

---

### 31 de julio de 2026 — Documentación inicial

**Qué se hizo**

- Se cerró el prototipo navegable (`mockup/portfolio-mockup.html`, versión 6) con las tres capas: portfolio público, panel de administración y portal de clientes.
- Se escribieron los cuatro documentos: especificación funcional, arquitectura y datos, formato de métricas y plan de fases.
- Se definió el modelo de datos completo y el flujo de importación con conciliación en tres niveles.

**Decisiones tomadas**

- Las publicaciones son una entidad propia y aparecen solas en el calendario. Por eso se quitó "Publicación" de los tipos de evento de la agenda.
- Las métricas por pieza se guardan en una tabla aparte, con una fila por medición, para conservar la evolución en el tiempo.
- Los campos propios —título interno, estado, pilar, archivo final y creativo en Figma— nunca se sobrescriben al importar.
- Las columnas sin dato quedan vacías. Está prohibido rellenar con cero: Meta no entrega clics por pieza en publicaciones orgánicas de feed, y un cero se leería como un resultado.

**Pendiente**

- Todo el desarrollo. No hay código todavía.
- Los cuatro pendientes de la tabla de arriba.

**Próximo paso**

Fase 0: crear el repositorio con esta estructura, levantar el proyecto Laravel, configurar Docker local y dejar desplegando en Railway con el dominio en Cloudflare.

---

<!--
PLANTILLA PARA ENTRADAS NUEVAS — copiar debajo del encabezado "Bitácora"

### [fecha] — [título corto de lo hecho]

**Qué se hizo**
-

**Decisiones tomadas**
-

**Pendiente o roto**
-

**Próximo paso**

-->
