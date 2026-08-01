# Portfolio y portal de clientes — Catalina Avendaño

Repositorio del sistema. Este archivo explica cómo está organizado el proyecto y **cómo se trabaja en él**. Léelo antes de tocar código.

---

## 1. Documentación

| Archivo | Contenido |
|---|---|
| `docs/01-especificacion-funcional.md` | Qué hace el sistema, para quién y con qué reglas |
| `docs/02-arquitectura-y-datos.md` | Stack, infraestructura, modelo de datos, seguridad, despliegue |
| `docs/03-formato-metricas.md` | Contrato del archivo JSON de métricas |
| `docs/04-plan-de-fases.md` | Plan de ejecución de la fase 0 a producción |
| `docs/05-prompts-de-ejecucion.md` | Prompts para construir el sistema con Claude Code |
| `docs/06-manual-de-marca.md` | Identidad visual y de redacción |
| `AVANCE.md` | **Estado real del proyecto. Se actualiza siempre.** |
| `mockup/portfolio-mockup.html` | Prototipo navegable de referencia visual y funcional |

El mockup es la referencia de diseño y de comportamiento. Ante una duda de interacción que la especificación no cubra, se resuelve como está en el mockup.

---

## 2. Regla de AVANCE.md

**`AVANCE.md` se actualiza al cerrar cada sesión de trabajo y al terminar cada fase. Sin excepción.**

Es la memoria del proyecto. Cualquier persona —o cualquier asistente de IA que retome el trabajo— tiene que poder leer solo ese archivo y saber en qué punto está todo, qué se decidió y qué sigue. Un commit que cambia funcionalidad y no toca `AVANCE.md` está incompleto.

Cada entrada registra:

1. **Fecha y fase.**
2. **Qué se hizo**, en pasado y concreto: "se implementó el alta de clientes con validación de correo único".
3. **Decisiones tomadas**, con el motivo. Sobre todo las que se apartan de la especificación.
4. **Qué quedó pendiente o roto**, sin maquillar.
5. **Próximo paso**, concreto y accionable.

Las entradas se agregan **arriba**, de más nueva a más vieja. No se borran ni se reescriben las anteriores: si algo cambió, se escribe una entrada nueva que lo diga.

Cuando una decisión modifica la especificación, se actualiza el documento correspondiente **y** se anota el cambio en `AVANCE.md`, con el archivo afectado.

---

## 3. Estructura del repositorio

```
catalina-portfolio/
├── AVANCE.md
├── README.md
├── docs/
│   ├── 01-especificacion-funcional.md
│   ├── 02-arquitectura-y-datos.md
│   ├── 03-formato-metricas.md
│   ├── 04-plan-de-fases.md
│   ├── 05-prompts-de-ejecucion.md
│   └── 06-manual-de-marca.md
├── mockup/
│   └── portfolio-mockup.html
├── app/
├── database/
├── resources/
├── routes/
├── tests/
├── composer.json
├── phpunit.xml
└── railway.json
```

**No hay `Dockerfile` ni `docker-compose.yml`.** Railway despliega con detección automática (Nixpacks) y el entorno local corre sobre Herd. Ver `docs/02-arquitectura-y-datos.md`, sección 7.2.

---

## 4. Flujo de trabajo

**Ramas**

- `main` — producción. Solo recibe merges desde `develop` y despliega solo.
- `develop` — staging. Despliega solo al entorno de pruebas.
- `fase/N-nombre` — una rama por fase. Se abre desde `develop` y vuelve ahí.

**Commits**: mensajes en español, en imperativo y con prefijo de tipo.

```
feat: alta de clientes con acceso al portal
fix: la conciliación por permalink ignoraba parámetros de seguimiento
docs: actualiza AVANCE tras cerrar la fase 4
test: aislamiento de datos entre clientes
```

**Una fase se da por cerrada** cuando cumple todos sus criterios de aceptación, las pruebas pasan en CI, está desplegada en staging y `AVANCE.md` está actualizado.

---

## 5. Entorno local

No se usa Docker. Se necesita:

- **Laravel Herd** — provee PHP 8.3, Composer y el servidor web con dominios `.test`
- **PostgreSQL 16** instalado de forma nativa
- **Node 20 o superior**

Redis corre solo en Railway (`QUEUE_CONNECTION=sync` y `CACHE_STORE=file` en local): a diferencia de SQLite contra PostgreSQL, no hay una clase de bug que quede oculta por resolver colas y caché en línea durante el desarrollo.

### Puesta en marcha

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Creá la base de datos y cargá los datos de ejemplo:

```bash
createdb catalina_portfolio
createdb catalina_portfolio_testing
php artisan migrate --seed
```

Compilá los recursos y abrí el sitio:

```bash
npm run dev
```

Herd sirve el proyecto en `http://catalina-portfolio.test` si la carpeta está dentro de un directorio estacionado.

Los seeders cargan una administradora, cuatro clientes de ejemplo con sus accesos, publicaciones con métricas y pedidos en distintos estados. Alcanza para recorrer el sistema completo sin datos reales.

### Base de datos: PostgreSQL también en local

**No se usa SQLite en desarrollo, ni siquiera "para ir rápido".**

PostgreSQL distingue mayúsculas en `LIKE` y SQLite no. Trabajar sobre SQLite oculta hasta el despliegue una clase entera de errores en buscadores y en ordenamiento alfabético. La segunda base, `catalina_portfolio_testing`, es la que usan las pruebas.

### Antes de cada push

```bash
./vendor/bin/pint          # formato
./vendor/bin/phpstan       # análisis estático
php artisan test           # pruebas
```

---

## 6. Tres reglas técnicas que hay que conocer antes de escribir código

Están desarrolladas en `docs/02-arquitectura-y-datos.md`, sección 3.3. Resumidas:

1. **Búsquedas con `whereLike(..., caseSensitive: false)`**, nunca con `where('columna', 'like', ...)`.
2. **Collation `es-AR-x-icu` declarada** en las columnas de texto que se ordenan.
3. **`config.platform.php` fijado en `composer.json`** con la versión de PHP de producción.

Y dos verificaciones de la configuración de pruebas, en la sección 10.2 del mismo documento: `phpunit.xml` no debe fijar `DB_CONNECTION=sqlite`, y el workflow de GitHub Actions no debe definir variables de entorno a nivel job que lo pisen. Son dos archivos distintos y hay que revisar los dos.

---

## 7. Contactos y datos del proyecto

- **Titular:** Catalina Avendaño — Viedma, Río Negro, Argentina
- **Correo:** catalinaavendanio@gmail.com
- **Infraestructura:** Railway (aplicación y base de datos, despliegue por Nixpacks), Cloudflare (DNS, CDN, WAF y almacenamiento R2)
