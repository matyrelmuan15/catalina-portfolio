# AVANCE

Estado real del proyecto. **Se actualiza al cerrar cada sesión de trabajo y al terminar cada fase.**

Quien retome el trabajo —persona o asistente de IA— tiene que poder leer solo este archivo y saber en qué punto está todo. Las entradas nuevas van **arriba**. Las anteriores no se editan ni se borran: si algo cambió, se escribe una entrada nueva que lo diga.

---

## Estado general

| | |
|---|---|
| **Fase actual** | 0 — Fundaciones |
| **Última actualización** | 1 de agosto de 2026 |
| **Entorno de staging** | Sin desplegar |
| **Entorno de producción** | Sin desplegar |
| **Bloqueos activos** | Ninguno |

### Progreso por fase

| Fase | Estado |
|---|---|
| 0 — Fundaciones | Sin empezar |
| 1 — Autenticación y roles | Sin empezar |
| 2 — Portfolio público | Sin empezar |
| 3 — Gestión de videos | Sin empezar |
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

---

## Bitácora

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
