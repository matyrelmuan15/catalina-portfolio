# AVANCE

Estado real del proyecto. **Se actualiza al cerrar cada sesión de trabajo y al terminar cada fase.**

Quien retome el trabajo —persona o asistente de IA— tiene que poder leer solo este archivo y saber en qué punto está todo. Las entradas nuevas van **arriba**. Las anteriores no se editan ni se borran: si algo cambió, se escribe una entrada nueva que lo diga.

---

## Estado general

| | |
|---|---|
| **Fase actual** | 0 a 3 implementadas y comiteadas, **ninguna cerrada**: falta el despliegue en staging probado a mano, que es criterio de aceptación de todas |
| **Última actualización** | 1 de agosto de 2026 |
| **Entorno de staging** | Sin desplegar |
| **Entorno de producción** | Sin desplegar |
| **Bloqueos activos** | Aprovisionar Railway y Cloudflare reales (requiere credenciales que no están disponibles en este entorno) |

### Progreso por fase

| Fase | Estado |
|---|---|
| 0 — Fundaciones | En revisión — código completo y comiteado; falta el aprovisionamiento real en Railway/Cloudflare y que CI corra una vez |
| 1 — Autenticación y roles | En revisión — código completo y comiteado; falta el recorrido manual en staging |
| 2 — Portfolio público | En revisión — Lighthouse local da rendimiento 91 (cumple) y accesibilidad 93 (el criterio pide ≥95); falta la verificación en navegadores reales |
| 3 — Gestión de videos | En revisión — código completo y comiteado; falta el recorrido manual en staging |
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
| 01/08/2026 | Laravel se fija en `^12.0` explícitamente en `composer.json` | El instalador de Composer resuelve Laravel 13 por defecto en este momento; la documentación pide Laravel 12 |
| 01/08/2026 | `larastan/larastan` en `^3.0`, no `^2.x` | La línea 2.x no soporta Laravel 12 en las versiones publicadas hoy |
| 01/08/2026 | Malva pasa de `#9a7f8c` a `#866a78`. **Pendiente de aprobación visual de Catalina**: técnicamente ya cumple la regla, pero es un cambio de identidad y no se considera definitivo hasta que ella lo vea | El valor del mockup da 3,39:1 sobre porcelana, por debajo del mínimo de 4,5:1 que fija la sección 13 de `docs/01`. Entre un valor extraído y una regla explícita, manda la regla. Se conservó matiz y saturación y se bajó solo la luminosidad |
| 01/08/2026 | El proyecto sigue fijado en PHP 8.3; no se sube a 8.4 | No se pudo verificar con certeza que Railway/Nixpacks resuelva 8.4 sin fricción: el proyecto nunca se desplegó ahí y Railway está migrando de Nixpacks a Railpack. Las dependencias sí lo admiten. Ver la entrada de bitácora del 01/08/2026 |
| 01/08/2026 | El scope `PerteneceAlCliente` **sí se aplica** al modelo `User`, con una guarda `Auth::hasUser()` al inicio de `apply()` | Revierte la exclusión registrada antes el mismo día. La recursión existía y era real, pero se resolvía con la guarda: mientras el guard resuelve la sesión todavía no tiene usuario asignado, así que el scope se salta exactamente en ese instante y vuelve a aplicar en el resto de las consultas. Ver la entrada de bitácora del 01/08/2026 |
| 01/08/2026 | Autenticación implementada a mano (Auth::attempt + Password broker + `pragmarx/google2fa`), sin Laravel Fortify | Fortify exige columnas adicionales para 2FA (`two_factor_recovery_codes`, `two_factor_confirmed_at`) que no están en el esquema de `users` que define `docs/02` |
| 01/08/2026 | El filtro de categorías del portfolio público se resuelve con Alpine.js puro, sin ida y vuelta a Livewire | Cumple el criterio de la Fase 2 ("el filtro responde en menos de 100 ms"); la página pública tampoco carga el bundle de Livewire, solo Tailwind + un entrypoint propio de Alpine (`resources/js/publico.js`), lo que ayuda además al puntaje de Lighthouse. Auth, panel y portal sí usan Livewire, como pide `docs/02` |
| 01/08/2026 | **Railway despliega con detección automática (Nixpacks). Sin `Dockerfile` ni `docker-compose.yml`** | Revierte la decisión del 31/07. Mantener imagen propia obligaba a Docker y WSL2 en la máquina de desarrollo, con bloqueos por permisos de Windows |
| 01/08/2026 | Entorno local sobre Herd, con PostgreSQL 16 nativo y Redis | Sin Docker ni WSL2 |
| 01/08/2026 | La versión de PHP se fija en `composer.json` y en Railway, no por imagen | Sin imagen propia hace falta declararla en los dos extremos |
| 01/08/2026 | PostgreSQL también en desarrollo, nunca SQLite | SQLite oculta errores de `LIKE` y de ordenamiento hasta el despliegue |
| 31/07/2026 | Laravel 12 con Livewire 3, sin API separada | Un solo consumidor y equipo chico |
| 31/07/2026 | Railway para aplicación y base; Cloudflare para DNS, WAF y R2 | Infraestructura ya elegida por el titular |
| 31/07/2026 | Videos del portfolio en YouTube o Vimeo sin listar | Evita el costo de transcodificar y servir video |
| 31/07/2026 | La versión 1 no se conecta a Meta Graph API; trabaja por importación de archivo | Menos permisos, menos tokens que renovar |
| 31/07/2026 | Los clientes se archivan, no se eliminan | Conserva historial y es reversible |
| 31/07/2026 | Sin estado de aprobación en pedidos | Se evalúa con datos de uso real |
| 31/07/2026 | El cliente no propone cambios de fecha desde el sistema | Exigiría negociación de estados |

---

## Pendientes abiertos

| # | Pendiente | Fase | Prioridad |
|---|---|---|---|
| 1 | Definir dominio definitivo | 0 | Alta |
| 2 | Elegir proveedor de correo saliente | 9 | Media |
| 3 | Confirmar si los archivos finales van en Drive o en R2 | 9 | Media |
| 4 | Conseguir los textos y las fotos reales para el portfolio | 2 | Alta |
| 5 | Claves reales de Turnstile | 1 | Media |
| 6 | Miniatura automática de Vimeo (solo está resuelto YouTube) | 3 | Baja |
| 7 | Aprovisionar el proyecto real en Railway (los tres servicios) y el dominio en Cloudflare; no hay credenciales de esas cuentas en el entorno donde se desarrolló este lote | 0 | Alta |
| 8 | Instalar Redis en el entorno de desarrollo (o Herd Pro, que trae "services"); hoy `.env` local corre con `QUEUE_CONNECTION=sync` y `CACHE_STORE=file` como paliativo | 0 | Media |
| 9 | Códigos de respaldo para el segundo factor de la administradora (hoy, si pierde el dispositivo, no hay salida salvo restablecer el secreto a mano en la base) | 1 | Media |
| 10 | UI para que un cliente active su propio segundo factor (el campo y el servicio ya existen; RF-93 lo pide opcional, pero no hay pantalla para eso todavía) | 1 | Baja |
| 11 | Verificar el comportamiento real en Chrome, Safari, Firefox y Edge. Lighthouse ya corrió local en headless (ver entrada del 01/08/2026): queda el recorrido manual en navegadores reales | 2 | Alta |
| ~~12~~ | ~~Correr el workflow de integración continua al menos una vez~~ **Resuelto el 01/08/2026**: PR #1 hacia `develop`, los trece pasos en verde | 0 | — |
| 13 | Subir la accesibilidad del portfolio de 93 a ≥95. **Queda un solo foco**: el rótulo del hero, fucsia `#e4006e` sobre porcelana, 4,33:1. Los otros tres ya se resolvieron. Este exige tocar fucsia o el tamaño de fuente, así que lo decide Catalina | 2 | Media |
| 14 | Aprobación visual del nuevo malva `#866a78` por parte de Catalina | 2 | Media |
| 15 | Alinear el PHP local con el del proyecto: Herd resuelve `php` a 8.4.23 y el proyecto está fijado en 8.3. Comando en la entrada de bitácora del 01/08/2026 | 0 | Media |
| ~~16~~ | ~~`composer.json` declaraba `"php": "^8.3"`, que permitía que producción levantara con 8.4~~ **Resuelto el 01/08/2026**: la restricción pasó a `"php": "8.3.*"` | 0 | — |
| 17 | **Reemplazar el comando de arranque de `railway.json` por uno apto para producción antes del primer despliegue real.** Hoy usa `php artisan serve`, que es el servidor de desarrollo embebido de PHP: un solo proceso, sin supervisión ni concurrencia real | 0 | Alta |
| 18 | Discrepancias entre `docs/06-manual-de-marca.md` y lo construido, relevadas el 01/08/2026 y no corregidas: el punto de estado del listado de videos es un tercer elemento circular fuera de las dos excepciones que admite el manual; un hexadecimal suelto en `configurar-segundo-factor.blade.php`; `welcome.blade.php` sigue siendo la portada por defecto de Laravel; y no hay helper de formato numérico argentino para la Fase 8 | 2 | Baja |

---

## Bitácora

### 1 de agosto de 2026 — El manual de marca entra al repositorio; contraste, PHP y Railway

**Qué se hizo**

- Se versionó `docs/06-manual-de-marca.md`, que existía en la documentación del proyecto pero nunca se había comiteado. Queda corregida la observación de la entrada anterior, que lo daba por inexistente: no existía **en el repositorio**, que es distinto.
- Se sincronizó el manual con el valor nuevo de malva, en los dos lugares donde aparecía: la tabla de paleta (sección 2) y el bloque de tokens (sección 8), los dos ahora en `#866A78`. Se agregó al pie de la tabla una nota que remite a la entrada de bitácora donde está el cálculo y el estado de aprobación pendiente. **El manual y `resources/css/app.css` coinciden.**
- Se resolvieron los dos focos de contraste que no tocaban la paleta: el rótulo de `#contacto` pasó de `rgba(255,255,255,.75)` a blanco pleno, y `.via .r` perdió su `opacity:.7`. Blanco sobre fucsia da **4,64:1**, por encima del mínimo. Los dos desaparecieron de la auditoría de Lighthouse. **Queda un solo foco**: el rótulo del hero, fucsia sobre porcelana (4,33:1), que no se tocó porque espera aprobación junto con malva.
- La accesibilidad sigue marcando **93**: la auditoría `color-contrast` es binaria y un solo nodo en falta la mantiene en rojo. La lista pasó de seis elementos a uno.
- `composer.json` pasó de `"php": "^8.3"` a `"php": "8.3.*"`, que cierra la puerta a que producción levante con 8.4 mientras CI prueba sobre 8.3 (pendiente 16, ahora resuelto). Composer resolvió sin conflictos: "Nothing to modify in lock file". El `composer.lock` solo cambió el `content-hash` y esa línea; ningún paquete se movió de versión.

**Repaso del manual contra lo construido (fases 0 a 3)**

Cumple: el enlace de Google Fonts coincide exactamente con el de la sección 8; no hay pesos negrita en ningún lado; no hay una sola `box-shadow`; el espaciado de secciones es 110/32 px y el ancho máximo 1180 px, como pide la sección 4; la tira de la portada corre en 34 s; los íconos son los cuatro caracteres tipográficos que fija la sección 5; hay bloque `prefers-reduced-motion`; y la voz respeta voseo, sin exclamaciones ni emoticones.

Discrepancias relevadas, **ninguna corregida** (pendiente 18):

1. **Tercer elemento circular.** `.punto`, el indicador de publicado del listado de videos, usa `border-radius: 50%`. La sección 4 admite exactamente dos excepciones: el círculo del día actual en el calendario y el botón de reproducción. El punto existe en el mockup, así que lo que falta es que el manual lo contemple, no que el código se haya desviado.
2. **Hexadecimal suelto en una plantilla.** `configurar-segundo-factor.blade.php` tiene `style="background:#fff;…"` en línea. La sección 8 dice que no se escriben hexadecimales sueltos en las plantillas.
3. **`welcome.blade.php` sigue en el repositorio.** Es la portada por defecto de Laravel, con su propio bundle de Tailwind embebido y una estética que no tiene nada que ver con la marca. No está enrutada —`/` va a `PortfolioController`—, pero quedó del andamiaje inicial.
4. **No hay formato numérico argentino.** La sección 7 pide 184.200 y 12,4. Los contadores del panel imprimen enteros crudos. Hoy no se nota porque son números de una o dos cifras, pero no existe el mecanismo, y la Fase 8 es de métricas.
5. **Sin verificar a 360 px.** La lista de la sección 9 lo exige; el corte más chico del CSS es 560 px. No se puede confirmar desde el CSS solo, hace falta probarlo.
6. **Rótulos en plural.** Los contadores dicen "Cargados", "Publicados". La sección 7 pide rótulos en singular y sin artículo. Discutible: esos rótulos nombran una cantidad, no un campo.

**Decisiones tomadas**

- No se tocó `railway.json`, por instrucción expresa. Solo quedó anotado como pendiente 17.

**Pendiente o roto**

- Las seis discrepancias del manual, sin corregir y a la espera de decisión.
- El rótulo del hero y la aprobación visual de malva.

**Próximo paso**

Mergear el PR #1 a `develop`.

---

### 1 de agosto de 2026 — Contraste de malva, CI en verde y decisión sobre PHP 8.4

**1. Malva no cumplía el contraste mínimo**

El `#9a7f8c` venía del mockup y nunca se contrastó contra la regla de la sección 13 de `docs/01`, que exige 4,5:1 en texto. Da **3,39:1 sobre porcelana** y **3,63:1 sobre blanco**: no cumple. Entre un valor extraído de una maqueta y una regla explícita del propio pliego, manda la regla.

Se conservaron matiz y saturación (HSL 331°, 12 %) y se bajó solo la luminosidad, de 55,1 % a 47,1 %, hasta el tono más claro de la misma familia que cumple en los dos fondos:

| | Valor | Sobre porcelana `#fbf6f7` | Sobre blanco `#ffffff` |
|---|---|---|---|
| Anterior | `#9a7f8c` | 3,39:1 ❌ | 3,63:1 ❌ |
| **Nuevo** | **`#866a78`** | **4,52:1** ✅ | **4,83:1** ✅ |

**Queda pendiente de aprobación visual de Catalina.** Cumple la regla, pero es un cambio de identidad y no se considera definitivo hasta que ella lo vea (pendiente 14).

El manual de marca no existe en el repositorio: se buscó `docs/06-manual-de-marca.md` en el árbol y en toda la historia de git, y nunca existió; solo hay `docs/01` a `docs/04`. La regla de contraste vive en la sección 13 de `docs/01`. El cambio quedó documentado acá y en un comentario en `resources/css/app.css`, donde está la única declaración de la variable en el proyecto. **No se tocó `mockup/portfolio-mockup.html`**, que también declara `--malva`: es el artefacto de referencia contra el que se compara, y modificarlo mientras el cambio espera aprobación sería borrar el punto de comparación.

Verificado con Lighthouse: `.enlace-sesion` y el párrafo que fallaban por malva desaparecieron de la lista. **La auditoría `color-contrast` sigue en rojo por otros tres focos, todos de fucsia**, que no se tocaron por instrucción expresa:

- `.rotulo` del hero: fucsia `#e4006e` sobre porcelana → 4,33:1
- `#contacto .rotulo`: `rgba(255,255,255,.75)` sobre fucsia → 2,97:1
- `.via .r` (×4): blanco con `opacity:.7` sobre fucsia → 2,73:1

Corrección a la entrada anterior de esta bitácora: ahí se listaron `.enlace-sesion`, `.rotulo` y un `<p>` como si fueran todos los focos; eran los tres primeros de una lista de seis. La accesibilidad sigue en **93**.

El rendimiento móvil quedó en **99**, estable en tres corridas seguidas (LCP ~1,65 s). El **91** de la medición anterior fue la primera corrida en frío, con vistas Blade sin compilar; no era representativo.

**2. Integración continua: en verde**

Se abrió el PR #1 de `fase/0-3-fundaciones` hacia `develop`, sin tocar los disparadores del workflow. El `pull_request` lo activó y **los trece pasos terminaron en éxito** en 1 m 14 s:

| Paso | Resultado |
|---|---|
| Composer install | ✅ |
| npm ci | ✅ |
| Pint | ✅ |
| Larastan | ✅ |
| Pest | ✅ 99 pruebas, 198 aserciones |
| composer audit | ✅ sin advisories |

La incógnita que quedaba abierta —la cobertura mínima del 70 %, que no se podía medir en local por falta de PCOV— **quedó resuelta: 95,7 %**.

**3. PHP 8.4: se investigó y se decidió NO subir**

- **Dependencias: dan bien.** Todas declaran rangos que incluyen 8.4 por semver: `laravel/framework` 12.64.0 `^8.2`, `intervention/image` 3.11.8 `^8.1`, `bacon/bacon-qr-code` 3.1.1 `^8.1`, `pragmarx/google2fa` 8.0.3 `^7.1|^8.0`, `larastan/larastan` 3.10.0 `^8.2`, `livewire` 3.8.3 `^8.1`, `pest` 3.8.7 `^8.2.0`. Salvedad: la documentación de Laravel 12 dice "PHP >= 8.2", que es un piso, no una declaración explícita de que 8.4 esté soportado.
- **Railway/Nixpacks: no se pudo verificar con certeza.** Nixpacks agregó soporte de 8.4 en la versión 1.33.0, y el error "No version available for php 8.4.0" que aparecía en Railway quedó resuelto en enero de 2026 (era un problema de formato de la restricción: `^8.4` en vez de `^8.4.0`). Pero: el proyecto **nunca se desplegó en Railway**, así que no hay contra qué probarlo; y la documentación de Railway hoy dice que usan **Railpack**, no Nixpacks, mientras `railway.json` fija `"builder": "NIXPACKS"` explícitamente. No hay forma de confirmar desde acá qué versión de Nixpacks corre Railway ni cómo resuelve el caso.

Como una de las dos verificaciones no da con certeza, **el proyecto queda en 8.3 y no se tocó ni `composer.json` ni los documentos**.

Dos hallazgos del camino, los dos anotados como pendientes:

- **`config.platform.php` no controla la versión de producción.** Es una opción de resolución de dependencias de Composer. Nixpacks elige el PHP a partir de `require.php`, que hoy es `"^8.3"` — y eso **permite 8.4**. O sea que producción podría levantar con 8.4 mientras CI prueba sobre 8.3, sin que nada lo avise (pendiente 16).
- **`railway.json` arranca con `php artisan serve`**, que es el servidor de desarrollo de PHP: un solo proceso, sin supervisión ni concurrencia real. Conviene revisarlo antes del primer despliegue (pendiente 17).

**Para alinear el entorno local con el proyecto**

En esta máquina Herd tiene instalado solo php84 y resuelve `php` a 8.4.23. Para que coincida con el 8.3 del proyecto y de CI:

1. Abrir Herd → sección **PHP / Versions** → botón **Install** junto a **8.3** (tarda dos o tres minutos). La instalación de versiones se hace desde la interfaz.
2. Después, en cualquier terminal: `herd use 8.3`
3. Verificar: `php -v` tiene que responder `PHP 8.3.x`.

Si preferís no mover el PHP global —hay otros dos sitios en Herd, `cuentas-corrientes` y `saas-hotel`, que hoy corren con 8.4—, la alternativa es aislar solo este proyecto, desde su carpeta:

```
herd link catalina-portfolio
herd isolate 8.3
```

Con esa variante, los comandos de este proyecto se corren como `herd php artisan test` y `herd composer install`, que usan la versión aislada; `php` a secas sigue siendo el global.

**Pendiente o roto**

- Railway y Cloudflare: sin tocar, sin credenciales.
- Accesibilidad en 93; los tres focos que quedan son de fucsia y los decide Catalina (pendiente 13).
- El nuevo malva espera aprobación visual (pendiente 14).
- Navegadores reales: sigue sin verificarse (pendiente 11).

**Próximo paso**

Mergear el PR #1 a `develop` y aprovisionar Railway y Cloudflare.

---

### 1 de agosto de 2026 — Primer commit del código: rama `fase/0-3-fundaciones`

**Qué se hizo**

- Se comiteó por primera vez el código de las fases 0 a 3, que hasta ahora vivía entero sin versionar (121 archivos nuevos). Rama `fase/0-3-fundaciones`, abierta desde `develop` según el flujo del README, y empujada a `origin`. Seis commits, agrupados por unidad lógica:

  | Commit | Contenido |
  |---|---|
  | `chore: agrega la base del proyecto Laravel 12 con PostgreSQL y CI` | Fase 0 |
  | `feat: agrega autenticacion, roles y segundo factor` | Fase 1 |
  | `feat: agrega el portfolio publico` | Fase 2 |
  | `feat: agrega la gestion de videos en el panel` | Fase 3 |
  | `docs: quita Redis del entorno local de desarrollo` | Cambio de entorno |
  | `docs: actualiza AVANCE con el estado del lote` | Este archivo |

- Los arreglos del scope de `User` y de las vistas planas no van como commits aparte: el código entró con su contenido final ya corregido, y el detalle de los dos problemas está en las dos entradas siguientes de esta bitácora.
- Se verificó que `.env` no entra al repositorio y que `.env.example` no lleva secretos reales: las únicas claves con valor son las de prueba públicas de Turnstile.

**Integración continua: NO corrió**

`gh run list --branch fase/0-3-fundaciones` no devuelve ninguna ejecución, y la causa es de configuración, no un fallo: `.github/workflows/ci.yml` se dispara solo con `push` a `main`/`develop` o con `pull_request` hacia esas ramas. Una rama `fase/**` no activa nada.

**Ninguno de los cinco pasos (composer install/npm ci, Pint, PHPStan, pruebas, composer audit) se ejecutó en GitHub Actions.** Se resuelve abriendo el PR hacia `develop` o agregando `fase/**` a los disparadores (pendiente 12). El equivalente local sí corre y pasa, los cinco pasos: `composer install` y `npm ci` ya resueltos (el proyecto levanta y `npm run build` compila), Pint `passed`, PHPStan `No errors` (con `--memory-limit=1G`; con el límite por defecto de 128 M el proceso se cae), 99 pruebas con 198 aserciones en verde, y `composer audit` sin advisories. Correrlos a mano no equivale a que CI los corra: local va sobre PHP 8.4 y Windows, el workflow sobre PHP 8.3 y Ubuntu, y el paso de pruebas en CI además exige `--min=70` de cobertura, que localmente no se midió.

Dato aparte: la única ejecución histórica del workflow, sobre `develop` y anterior a este lote, terminó en `failure`.

**Lighthouse: sí se pudo correr**

`php artisan serve` sigue fallando (`Failed to listen on 127.0.0.1:8123 (reason: ?)`), pero no es un problema de sockets: Node bindea ese mismo puerto sin drama, y el servidor embebido de PHP invocado directo (`php.exe -S 127.0.0.1:8123 -t public`, salteando el shim `php.bat` de Herd) levanta bien. Con eso, y con los recursos compilados por `npm run build`, Lighthouse 12.8.2 corrió en Chrome headless contra el portfolio real (11 videos publicados de la base local).

| Preset | Rendimiento | Accesibilidad |
|---|---|---|
| **Móvil** (el del criterio de aceptación) | **91** ✅ (pide ≥90) | **93** ❌ (pide ≥95) |
| Escritorio | 99 | 93 |

Métricas de móvil: FCP 2,8 s · LCP 2,8 s · TBT 0 ms · CLS 0,003 · Speed Index 3,1 s.

La accesibilidad falla por una sola auditoría, `color-contrast`, en `.enlace-sesion`, `.rotulo` y un `<p>`. No se tocó: subir el contraste es cambiar la paleta del mockup y esa decisión es de Catalina (pendiente 13).

**Decisiones tomadas**

- Ninguna fase se marca como "Cerrada". Según los criterios de aceptación de `docs/04`, todas dependen del despliegue en staging probado a mano, que no se puede hacer desde acá. Las cuatro pasan a "En revisión".

**Pendiente o roto**

Todo esto queda fuera del alcance de esta sesión y necesita a Catalina o credenciales que no están acá:

- **Railway y Cloudflare**: no se tocaron, por instrucción explícita y por falta de credenciales (pendiente 7).
- **CI**: no corrió nunca (pendiente 12). Hasta que corra, los cinco pasos están verificados solo localmente y sobre PHP 8.4, no sobre el 8.3 que usa el workflow.
- **Navegadores reales**: sigue sin verificarse en Chrome, Safari, Firefox y Edge (pendiente 11). Lighthouse headless no lo reemplaza.
- **Accesibilidad 93 < 95** (pendiente 13).
- **Cobertura de pruebas**: el paso de CI exige `--min=70` y **no se puede medir acá**: el PHP de Herd no trae ni PCOV ni Xdebug, y `php artisan test --coverage` corta con "Code coverage driver not available". En CI lo resuelve `setup-php` con `coverage: pcov`. Si la cobertura queda por debajo de 70, ese paso va a fallar aunque las 99 pruebas pasen; es la incógnita más concreta que queda sobre el workflow.

**Próximo paso**

Decidir cómo se dispara CI sobre esta rama (PR hacia `develop`, o `fase/**` en los disparadores) y correrlo. Después, aprovisionar Railway y Cloudflare.

---

### 1 de agosto de 2026 — El scope `PerteneceAlCliente` pasa a aplicarse también a `User`

**Qué se hizo**

- Se revirtió la exclusión del modelo `User` del global scope `PerteneceAlCliente`, anotada más abajo en la entrada de la Fase 1. El scope ahora se aplica en el `booted()` de `User` como en cualquier otro modelo con `cliente_id`.
- La recursión que motivó la exclusión era real, pero la causa es más acotada de lo que decía esa nota: se dispara **solo** mientras el guard de sesión resuelve al usuario por primera vez (`EloquentUserProvider::retrieveById`, que corre `User::newQuery()`). En ese instante `SessionGuard::$user` todavía es `null` —la asignación ocurre recién cuando `retrieveById` retorna—, así que una guarda `if (! Auth::hasUser()) return;` al inicio de `apply()` corta la recursión justo ahí y deja el scope activo en todas las demás consultas.
- Cobertura nueva en `tests/Feature/Aislamiento/ScopeUsuarioTest.php`: resolución del usuario desde la sesión sin recursión (cliente y administradora), un cliente que no ve las cuentas de otra marca ni las de la administradora, la administradora que sigue viendo todo, y el ingreso que puede buscar por correo cualquier cuenta porque todavía no hay sesión.
- Se verificó que la guarda es lo que sostiene el arreglo: quitándola, la prueba de resolución desde la sesión agota la memoria de PHP (recursión infinita), y volviéndola a poner pasa.

**Por qué la batería anterior no detectaba nada**

Las 87 pruebas que había pasaban con el scope aplicado a `User` **incluso antes** de agregar la guarda. No probaban nada: toda la batería autentica con `actingAs`, que asigna el usuario directamente al guard y nunca ejecuta `retrieveById`, que es la única consulta donde ocurría la recursión. Las pruebas nuevas siembran el id en la sesión (`withSession([Auth::getName() => $id])`) y fuerzan al guard a resolverlo con una consulta real.

**Decisiones tomadas**

- Ver la tabla "Decisiones vigentes": la fila que registraba la exclusión quedó corregida.
- La guarda es `Auth::hasUser()` a secas, no `$model instanceof User`. Alcance conocido y aceptado: si en algún momento se consulta un modelo con `cliente_id` en una petición autenticada **antes** de que algo haya tocado `Auth::user()`, el scope no filtra. Hoy no puede pasar en ninguna ruta del proyecto: `/panel` y `/portal` están detrás de `rol.admin` / `rol.cliente`, que resuelven al usuario antes de llegar a la consulta. Vale tenerlo presente al agregar rutas autenticadas que no pasen por esos middlewares.

**Pendiente o roto**

- Nada de este cambio. Ver la entrada siguiente por el fallo de las vistas planas, corregido en el mismo lote.

**Próximo paso**

Fase 4: clientes y accesos.

---

### 1 de agosto de 2026 — Las siete páginas `Route::view` del panel y del portal devolvían 500

**Qué se hizo**

- Se corrigió la invocación del layout en `resources/views/panel/proximamente.blade.php` y `resources/views/portal/proximamente.blade.php`: `<x-layouts.panel>` → `<x-layouts::panel>` (y lo mismo para `portal`).
- `AppServiceProvider` registra los layouts con `Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts')`. Al registrar un path anónimo **con prefijo**, Blade lo expone como namespace y se invoca con dos puntos (`<x-layouts::panel>`); con un punto busca `resources/views/components/layouts/panel.blade.php`, que no existe. Las siete rutas afectadas (`/panel/clientes`, `/panel/pedidos`, `/panel/cuenta`, `/portal/calendario`, `/portal/publicaciones`, `/portal/pedidos`, `/portal/metricas`) tiraban `InvalidArgumentException` y respondían 500.
- Se agregó `tests/Feature/Fundaciones/VistasPlanasTest.php`, que renderiza las siete y espera 200.

**Cómo se pasó por alto hasta ahora**

Se descubrió de casualidad, escribiendo la prueba de resolución de sesión de la entrada de arriba. `/panel/videos` es un componente Livewire de página completa y usa `#[Layout(...)]`, que resuelve la vista por nombre y no por componente: por eso el panel parecía andar. Las páginas `Route::view` son las únicas que renderizan el layout como componente Blade, y la batería de aislamiento solo verificaba redirecciones y 403 sobre ellas —nunca un 200—, así que el 500 nunca se manifestó.

**Decisiones tomadas**

- Ninguna. Es una corrección de un defecto.

**Pendiente o roto**

- Queda como criterio para las fases que vienen: toda ruta que renderice una página necesita al menos una prueba que espere 200. Verificar el control de acceso no alcanza para saber que la página existe.

**Próximo paso**

Fase 4: clientes y accesos.

---

### 1 de agosto de 2026 — Fase 3 cerrada: gestión de videos en el panel

**Qué se hizo**

- Listado de videos en `/panel/videos` (`App\Livewire\Panel\Videos\Listado`) con buscador por título y cliente (`whereLike(..., caseSensitive: false)`, regla 1 de `docs/02`), filtro por categoría y los cuatro contadores (cargados, publicados, ocultos, destacados).
- Alta, edición y baja con confirmación; publicar y destacar se alternan desde el listado sin abrir el formulario.
- Servicio `ProcesadorMiniatura`: recorte a 9:16 (`cover()` de Intervention Image v3) y conversión a WebP, bajando calidad y después resolución hasta quedar por debajo de 200 KB. Se sube al disco `r2_publico`.
- Servicio `ProveedorVideo`: detecta YouTube, Vimeo o archivo a partir del enlace, arma la URL de embebido y, para YouTube, la miniatura automática cuando no se sube una a mano (Vimeo queda pendiente, ítem 6 de la tabla de arriba).
- Servicio `PurgaCacheCloudflare`: purga la portada del portfolio al publicar, despublicar o eliminar un video publicado. Sin `CLOUDFLARE_ZONE_ID`/`CLOUDFLARE_API_TOKEN` configuradas, omite la purga y deja aviso en el log en vez de fallar.
- Batería de pruebas del panel (`tests/Feature/Panel/VideosTest.php`): contadores, buscador insensible a mayúsculas contra PostgreSQL real, filtro, alta, validación, edición, subida y procesamiento de miniatura (con `Storage::fake('r2_publico')`), alternar publicado/destacado, purga de caché (`Http::fake`) y baja con confirmación. Prueba aparte (`tests/Unit/Services/ProcesadorMiniaturaTest.php`) que genera una imagen de ~5 MB con ruido real y confirma que el resultado queda por debajo de 200 KB y mantiene la relación 9:16.

**Decisiones tomadas**

- Ninguna nueva más allá de las ya registradas en la entrada de la Fase 1 (scope, Fortify) y la de la Fase 2 (Alpine para lo que no necesita ida y vuelta al servidor); el resto de la lógica se ajusta al plan de fases sin apartarse de la especificación.

**Pendiente o roto**

- Miniatura automática de Vimeo sigue sin resolver (pendiente #6, ya anotado).
- La purga de Cloudflare no se probó contra una cuenta real: no hay credenciales en este entorno. Queda cubierta por la prueba con `Http::fake()`, que verifica que la llamada se arma y se dispara correctamente.
- No se verificó a mano que "cargar un video y verlo en el sitio toma menos de un minuto": no hay navegador disponible en este entorno para el recorrido manual (mismo motivo que el pendiente 11).

**Próximo paso**

Fase 4: clientes y accesos.

---

### 1 de agosto de 2026 — Fase 2 cerrada: portfolio público

**Qué se hizo**

- Tabla y modelo `Video` (con la collation `es-AR-x-icu` en `titulo`) y seeder con doce piezas de ejemplo, cubriendo las seis categorías y estados variados (publicado, oculto, destacado).
- Portfolio público de una sola página (`PortfolioController` + `resources/views/publico/portfolio.blade.php`), fiel a la estructura de `mockup/portfolio-mockup.html`: cabecera, portada con tira animada, trabajos con filtro por categoría, servicios, sobre mí, contacto y pie.
- Filtro por categoría y visor de video en modal, los dos resueltos con Alpine.js del lado del cliente (ver decisión abajo): sin recarga y sin ida y vuelta al servidor.
- Reproducción embebida de YouTube (`youtube-nocookie.com`) y Vimeo, con el video sin cargar hasta que se abre la pieza (RF de rendimiento de la sección 3.4).
- Etiquetas Open Graph, datos estructurados JSON-LD de tipo Person, `sitemap.xml` dinámico (`SitemapController`) y `robots.txt` que bloquea `/panel`, `/portal`, `/ingresar` y `/clave`.
- Cabecera `Cache-Control: max-age=300, public` en las rutas públicas (middleware `CachearRespuestaPublica`), como paso previo a configurar la regla de caché de Cloudflare (sección 8.2 de `docs/02`).

**Decisiones tomadas**

- Ver la entrada correspondiente en la tabla "Decisiones vigentes": el filtro se resolvió con Alpine.js puro en vez de un roundtrip a Livewire, para cumplir el criterio de "responde en menos de 100 ms" sin arriesgarlo a la latencia de red; la página pública no carga el bundle de Livewire.
- Al abrir una pieza se usa un modal, no una URL propia por video —fiel al mockup (`abrirVisor`)—; compartir el enlace comparte la portada, con sus propias etiquetas Open Graph.
- Los textos y la foto de "Sobre mí" son los del propio mockup (contenido de referencia): coincide con el pendiente #4 ya anotado (conseguir fotos y textos reales).

**Pendiente o roto**

- No se corrió Lighthouse ni se probó en Chrome/Safari/Firefox/Edge reales: no hay navegador disponible en este entorno de desarrollo. Falta ese recorrido manual antes de dar la Fase 2 por verificada end-to-end (criterios de aceptación de `docs/04`).
- Pendiente #4 sigue abierto.

**Próximo paso**

Fase 3: gestión de videos en el panel.

---

### 1 de agosto de 2026 — Fase 1 cerrada: autenticación y roles

**Qué se hizo**

- Tablas `clientes` y `users` (con collation `es-AR-x-icu` en `marca` y `contacto`, y un `CHECK` en `users` que obliga a que todo cliente tenga `cliente_id` y ninguna administradora lo tenga), factories y seeder (`ClienteSeeder`: una administradora y cuatro clientes de ejemplo, con las claves de prueba documentadas al final de este informe).
- Ingreso, recuperación y restablecimiento de clave, con invalidación de todas las sesiones anteriores al cambiarla (sección 6.2 de `docs/02`).
- Segundo factor TOTP obligatorio para la administradora: enrolamiento forzado en el primer ingreso, código QR generado localmente con `bacon/bacon-qr-code` (sin depender de un servicio externo que vea el secreto), verificación con `pragmarx/google2fa`.
- Roles `admin`/`cliente` con los middlewares `AsegurarRolAdmin` y `AsegurarRolCliente`; límite de cinco intentos por minuto por IP y por correo (RF-91); Turnstile en el formulario de ingreso (claves de prueba oficiales de Cloudflare en `.env.example`, pendiente #5 las reales).
- Nivel 1 del aislamiento: global scope `PerteneceAlCliente`. Nivel 2: `UserPolicy`, primera policy del proyecto. Nivel 3: ninguna ruta del portal acepta un identificador de cliente por parámetro; el cliente sale siempre de la sesión.
- Estructuras vacías de `/panel` y `/portal` con navegación (`layouts/panel.blade.php`, `layouts/portal.blade.php`), fieles al armazón del mockup.
- Batería de aislamiento (`tests/Feature/Aislamiento/RolesMiddlewareTest.php`): recorre cada ruta de `/panel` y de `/portal` sin sesión y autenticada con el rol contrario, además del caso de una administradora con el segundo factor pendiente intentando entrar directo. Se suma `PerteneceAlClienteTest` (unitaria) y `UserPolicyTest`.

**Decisiones tomadas**

- El scope `PerteneceAlCliente` no se aplica al modelo `User` pese a tener `cliente_id`: produce recursión infinita en la resolución del guard de sesión (`User::newQuery()`, al resolver `Auth::user()`, volvería a invocar `Auth::user()` dentro del propio scope). El aislamiento de cuentas queda cubierto por los middlewares de rol y por `UserPolicy`; el scope se deja listo para aplicarse al primer modelo de negocio real con `cliente_id` (`agenda_eventos`, Fase 5). Documentado también en el propio archivo del scope.
- Los componentes de autenticación se ubicaron en `app/Livewire/Auth/`, una carpeta que no está en el organigrama de la sección 2.1 de `docs/02` (que solo lista Publico/Panel/Portal): la autenticación es transversal a los tres contextos y no encajaba en ninguno.
- Autenticación resuelta a mano (`Auth::attempt`, `Password` broker, `pragmarx/google2fa`) en vez de con Laravel Fortify: Fortify exige columnas adicionales para el segundo factor (`two_factor_recovery_codes`, `two_factor_confirmed_at`) que no están en el esquema de `users` que fija `docs/02`, sección 3.1.
- Se registró `Authenticate::redirectUsing()` en `AppServiceProvider`, además de `RedirectIfAuthenticated::redirectUsing()`: sin el primero, cualquier petición sin sesión a una ruta con el middleware `auth` (por ejemplo `/salir`) rompía, porque Laravel cae al *fallback* `route('login')`, que no existe en este proyecto (la ruta de ingreso se llama `ingresar`). Se detectó escribiendo la prueba de `/salir` sin sesión, antes de que llegara a producción.

**Pendiente o roto**

- No hay códigos de respaldo para el segundo factor: si la administradora pierde el dispositivo, hoy no hay salida salvo restablecer el secreto a mano en la base (pendiente 9).
- El segundo factor es opcional para clientes según RF-93, pero no hay pantalla para que un cliente lo active en este lote (pendiente 10). El campo y el servicio ya soportan el caso.
- Redis no está instalado en el entorno donde se desarrolló este lote (hace falta Herd Pro para los "services" de Herd, o instalarlo aparte). El `.env` local quedó con `QUEUE_CONNECTION=sync` y `CACHE_STORE=file` como paliativo; `.env.example` sigue documentando Redis, como pide `docs/02` (pendiente 8).

**Próximo paso**

Fase 2: portfolio público.

---

### 1 de agosto de 2026 — Fase 0 cerrada: fundaciones del proyecto

**Qué se hizo**

- Proyecto Laravel instalado y fijado en la rama `^12.0` (el instalador de Composer resuelve Laravel 13 por defecto en este momento, que no es lo que pide `docs/02`), con PHP 8.3 fijado en `config.platform.php`, Livewire 3, Tailwind 4, Pest 3, Pint y Larastan 3 (la línea 2.x de Larastan no soporta Laravel 12 todavía).
- Conexión a PostgreSQL 16 nativo como driver por defecto de la aplicación; bases `catalina_portfolio` y `catalina_portfolio_testing` creadas en la instancia local.
- Discos `r2_publico` y `r2_privado` en `config/filesystems.php`, sobre el driver S3 (compatible con la API de R2), con las variables de entorno ya definidas en la bitácora anterior.
- `phpunit.xml` corre contra PostgreSQL —nunca contra SQLite— y `.github/workflows/ci.yml` no define ninguna variable `DB_*` a nivel job que lo pise: las dos trampas de la sección 10.2 de `docs/02` quedaron cubiertas, con una nota en el propio workflow para quien lo toque después.
- Prueba que confirma que el driver activo es `pgsql` y otra que confirma la collation `es-AR-x-icu` en las columnas que la necesitan (`tests/Feature/Fundaciones/BaseDeDatosTest.php`).
- `railway.json` con build por Nixpacks y el comando de arranque del servicio `web` (migraciones + los cuatro `*:cache` + servidor). Los servicios `worker` y `scheduler` quedan documentados acá porque Railway no permite declarar varios comandos de arranque distintos en un solo `railway.json`: hay que crearlos como servicios aparte en el dashboard, apuntando al mismo repositorio, con `php artisan queue:work --tries=3 --timeout=90` y `php artisan schedule:work` respectivamente.
- `TRUSTED_PROXIES` resuelto en `bootstrap/app.php`; ruta `/up` (la trae Laravel 12 por defecto, se usa tal cual).

**Decisiones tomadas**

- Ver la tabla "Decisiones vigentes": Laravel fijado a `^12.0`, Larastan a `^3.0`, y tres paquetes agregados al stack por necesidad de implementación (no de arquitectura): `intervention/image` (recorte y conversión de miniaturas, Fase 3), `bacon/bacon-qr-code` y `pragmarx/google2fa` (segundo factor, Fase 1).

**Pendiente o roto**

- No se creó el proyecto real en Railway (los tres servicios, las bases gestionadas) ni el dominio en Cloudflare: no hay credenciales de esas cuentas en este entorno (pendiente 7). El repositorio queda listo para desplegarse; falta la parte de aprovisionamiento, que solo se puede hacer desde las consolas de Railway y Cloudflare.
- No se pudo verificar el build real de Railway ni que las extensiones de PHP declaradas estén presentes ahí (mismo motivo).
- El workflow de integración continua no corrió todavía dentro de GitHub Actions: no hay push a un repositorio remoto desde este entorno. Se validó cada paso a mano, en el mismo orden del workflow (composer install, Pint, Larastan, Pest contra PostgreSQL real, composer audit), y todos pasan (pendiente 12).
- Redis no está instalado en esta máquina de desarrollo; ver el detalle en la entrada de la Fase 1 (pendiente 8).

**Próximo paso**

Fase 1: autenticación y roles.

---

### 1 de agosto de 2026 — Corrección de inconsistencia en variables de R2

**Qué se hizo**

- Se corrigió `docs/02-arquitectura-y-datos.md` (sección 7.5) y `docs/04-plan-de-fases.md` (alcance y criterios de aceptación de la Fase 0), que seguían describiendo un solo bucket y disco de R2 (`R2_BUCKET`, disco `r2`) pese a que las secciones 5 y 8.4 de `docs/02` ya definían dos buckets con reglas de acceso distintas.

**Decisiones tomadas**
- Las variables de entorno de R2 pasan a ser `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_ENDPOINT` (compartidas entre los dos discos, porque el token de Cloudflare está scoped a ambos buckets), más `R2_BUCKET_PUBLICO` y `R2_URL_PUBLICO` para el disco público, y `R2_BUCKET_PRIVADO` para el privado. No hay `R2_URL_PRIVADO`: el bucket privado no tiene acceso público, por diseño.
- El alcance y los criterios de aceptación de la Fase 0 pasan a hablar de dos discos, `r2_publico` y `r2_privado`, en vez de un disco genérico `r2`.

**Pendiente o roto**
-

**Próximo paso**

Ninguno específico de este ajuste; continúa el trabajo de la Fase 0.

---

### 1 de agosto de 2026 — Cambio de infraestructura y reinicio del desarrollo

**Qué se hizo**

- Se revirtió la decisión de usar un `Dockerfile` propio. **Railway pasa a desplegar con detección automática (Nixpacks)** y el proyecto ya no lleva `Dockerfile` ni `docker-compose.yml`.
- Se redefinió el entorno local: Herd para PHP, PostgreSQL 16 nativo y Redis. Sin Docker y sin WSL2.
- Se actualizaron `README.md`, `docs/02-arquitectura-y-datos.md`, `docs/04-plan-de-fases.md` y `docs/05-prompts-de-ejecucion.md`. Los documentos 01, 03 y 06 no cambiaron.
- **El proyecto se reconstruye desde cero con esta documentación.** El código de la implementación anterior se descarta.

**Por qué se revirtió la decisión del Dockerfile**

Una imagen propia solo aporta si se la reproduce localmente; si no, es una definición que nadie verifica hasta el despliegue. Reproducirla en esta máquina exigía Docker y WSL2, y ese camino trajo bloqueos por permisos de Windows que consumieron tiempo sin devolver nada al producto. Railway con Nixpacks ya funcionó sin fricción en otros proyectos del mismo autor.

Se acepta la compensación: el entorno local y el de producción ya no son idénticos. La contrapartida está en integración continua, donde las pruebas corren contra PostgreSQL, que es donde importa parecerse a producción.

**Hallazgos de la implementación anterior, ya incorporados a `docs/02`**

Tres problemas reales que se produjeron y que ahora son reglas del proyecto, en la sección 3.3:

1. **Búsquedas de texto.** Se usa `whereLike(..., caseSensitive: false)`, nunca `where('columna', 'like', ...)`. PostgreSQL distingue mayúsculas en `LIKE` y SQLite no: el buscador funcionaba en desarrollo y no encontraba nada en producción.
2. **Ordenamiento alfabético.** Las columnas de texto que se ordenan llevan collation `es-AR-x-icu` declarada en la migración. Sin eso el orden depende del sistema operativo y las palabras con tilde o eñe quedaban después de la Z. No se usan collations no deterministas: rompen `LIKE` e `ILIKE` en PostgreSQL 16.
3. **Resolución de dependencias.** `composer.json` fija `config.platform.php` con la versión de PHP de producción. Sin ese pin, Composer resuelve contra el PHP local y genera un `composer.lock` que no se instala en el servidor.

Y dos trampas de configuración de pruebas, ahora advertidas en la sección 10.2:

4. `phpunit.xml` no debe fijar `DB_CONNECTION=sqlite`. Las pruebas corren contra PostgreSQL, igual que producción.
5. Una variable de entorno definida a nivel job en el workflow de GitHub Actions **pisa** lo que diga `phpunit.xml`. Hay que verificar los dos lugares; corregir uno solo deja el problema intacto y da la sensación de haberlo resuelto.

**Pendiente**

- Todo el desarrollo. No hay código.
- Los seis pendientes de la tabla de arriba.

**Próximo paso**

Fase 0: crear el repositorio con esta estructura, levantar el proyecto Laravel sobre Herd con PostgreSQL nativo, fijar `config.platform.php`, configurar GitHub Actions con servicio `postgres:16` y dejar desplegando en Railway por Nixpacks, con el dominio en Cloudflare.

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
