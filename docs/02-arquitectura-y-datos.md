# Arquitectura, modelo de datos e infraestructura

**Versión:** 1.1
**Fecha:** 1 de agosto de 2026
**Documento hermano:** `01-especificacion-funcional.md`

---

## 1. Decisiones de stack

| Capa | Elección | Motivo |
|---|---|---|
| Lenguaje | PHP 8.3 | Fijado en `composer.json` y en Railway, no por una imagen |
| Framework | Laravel 12 | Autenticación, autorización, colas, migraciones y almacenamiento ya resueltos |
| Interfaz | Livewire 3 + Alpine.js | El sistema es un panel con formularios y tablas; una SPA agregaría una capa de API sin beneficio |
| Estilos | Tailwind CSS 4 | El mockup ya está resuelto con utilidades |
| Compilación | Vite | Estándar de Laravel |
| Base de datos | PostgreSQL 16 | Soporte nativo de JSONB para las métricas |
| Colas y caché | Redis | Correos y procesos en segundo plano |
| Pruebas | Pest | |
| Análisis estático | Larastan nivel 6 | |
| Formato | Laravel Pint | |

**Sobre la versión de PHP.** No se usa una imagen propia para fijarla. Se declara en dos lugares: `config.platform.php` en `composer.json` y la configuración de Railway. Ver la sección 7.2, donde esto es una regla del proyecto y no una recomendación.

**Por qué no una API separada con front en JavaScript.** Hay un solo consumidor —el navegador—, no hay aplicación móvil prevista y el equipo es chico. Livewire entrega la interactividad necesaria sin mantener dos proyectos ni un contrato de API. Si en el futuro aparece una app móvil, se agrega una capa de API sobre la misma lógica de dominio.

---

## 2. Arquitectura de la aplicación

Tres contextos sobre un mismo despliegue, separados por rutas, middleware y layout:

```
┌──────────────────────────────────────────────────────────┐
│  Cloudflare  ·  DNS, TLS, CDN, WAF, límite de peticiones │
└───────────────────────────┬──────────────────────────────┘
                            │
┌───────────────────────────▼──────────────────────────────┐
│  Railway                                                  │
│  ┌────────────┐  ┌────────────┐  ┌────────────────────┐  │
│  │ web        │  │ worker     │  │ scheduler          │  │
│  │ Laravel    │  │ cola       │  │ tareas programadas │  │
│  └─────┬──────┘  └─────┬──────┘  └─────────┬──────────┘  │
│        └───────────────┴───────────────────┘             │
│                        │                                  │
│        ┌───────────────┴──────────┐                       │
│        │ PostgreSQL 16 │ Redis    │                       │
│        └──────────────────────────┘                       │
└───────────────────────────┬──────────────────────────────┘
                            │
              ┌─────────────▼─────────────┐
              │ Cloudflare R2             │
              │ imágenes y archivos       │
              └───────────────────────────┘
```

### 2.1 Organización del código

```
app/
├── Models/
├── Livewire/
│   ├── Publico/          Portfolio
│   ├── Panel/            Administración
│   └── Portal/           Cliente
├── Services/
│   ├── ImportadorMetricas.php
│   ├── ConciliadorPublicaciones.php
│   └── ProveedorVideo.php
├── Policies/
├── Scopes/
│   └── PerteneceAlCliente.php
├── Http/Middleware/
│   ├── AsegurarRolAdmin.php
│   └── AsegurarRolCliente.php
└── Notifications/
```

La lógica de importación y conciliación vive en **servicios**, no en componentes de interfaz ni en modelos. Es la parte con más reglas y la que más pruebas necesita; tiene que poder ejecutarse sin navegador.

### 2.2 Rutas

| Prefijo | Contexto | Acceso |
|---|---|---|
| `/` | Portfolio público | Abierto |
| `/ingresar`, `/clave` | Autenticación | Abierto |
| `/panel/*` | Administración | Rol administradora + segundo factor |
| `/portal/*` | Cliente | Rol cliente, limitado a su marca |
| `/archivos/{uuid}` | Archivos privados | Enlace firmado con vencimiento |
| `/up` | Estado del servicio | Abierto, sin datos |

---

## 3. Modelo de datos

### 3.1 Tablas

**`users`**

| Columna | Tipo | Notas |
|---|---|---|
| id | bigserial | |
| name | varchar(120) | |
| email | varchar(180) | único |
| password | varchar(255) | hash |
| rol | varchar(20) | `admin` o `cliente` |
| cliente_id | bigint nulo | obligatorio si rol es cliente |
| activo | boolean | por defecto verdadero |
| two_factor_secret | text nulo | |
| ultimo_acceso_at | timestamptz nulo | |
| timestamps | | |

**`clientes`**

id, marca (varchar 120), contacto (varchar 120), correo_contacto, telefono, color (varchar 20), archivado (boolean), timestamps.

**`videos`**

id, titulo, cliente_texto, categoria, fecha (date), proveedor (`youtube`/`vimeo`/`archivo`), enlace, miniatura_path, descripcion, publicado (boolean), destacado (boolean), orden (integer), timestamps.

**`agenda_eventos`**

id, cliente_id (FK, cascade), fecha (date), tipo (`grabacion`/`entrega`/`reunion`), titulo, nota (text), timestamps.

**`publicaciones`**

| Columna | Tipo | Notas |
|---|---|---|
| id | bigserial | |
| cliente_id | bigint | FK, cascade |
| titulo | varchar(160) | grupo C |
| estado | varchar(20) | ver especificación 7.3 |
| fecha | date | |
| plataforma | varchar(20) | `instagram` o `facebook` |
| formato | varchar(20) | reel, carrusel, imagen, historia, video |
| pilar | varchar(40) nulo | grupo C |
| copy_texto | text nulo | |
| hashtags | text nulo | |
| id_media | varchar(40) nulo | **único** cuando no es nulo |
| permalink | varchar(300) nulo | se guarda normalizado |
| archivo_final_url | text nulo | grupo C |
| creativo_figma_url | text nulo | grupo C |
| timestamps | | |

Índices: `(cliente_id, fecha)`, único parcial en `id_media`, índice en `permalink`.

**`publicacion_metricas`**

id, publicacion_id (FK, cascade), medido_el (date), alcance, vistas, interacciones, me_gusta, comentarios, compartidos, guardados, clics_enlace (nulo), seguidores_al_publicar (nulo), tasa_interaccion (numeric 5,2 nulo), timestamps.

Único en `(publicacion_id, medido_el)`. **Guardar cada medición como una fila permite ver la evolución de una pieza en el tiempo**, no solo su último estado. Los enteros son nulables a propósito: la ausencia de dato es información distinta de un cero.

**`metricas_periodo`**

id, cliente_id (FK), periodo (varchar 7, formato `AAAA-MM`), origen, actualizado_el (timestamptz), resumen (jsonb), variacion (jsonb), serie (jsonb), timestamps. Único en `(cliente_id, periodo)`.

**`importaciones`**

id, cliente_id (FK), user_id (FK), archivo_nombre, contenido (jsonb, el archivo original tal cual llegó), resultado (jsonb: creadas, actualizadas, vinculadas), created_at.

Guardar el archivo original permite reprocesar si mañana cambia la lógica de conciliación, y sirve como evidencia ante una discusión sobre números.

**`pedidos`**

id, cliente_id (FK), user_id (FK), texto (text), imagen_path (nulo), estado (`nuevo`/`en_curso`/`entregado`), respuesta (text nulo), respondido_at (nulo), timestamps.

### 3.2 Relaciones

```
clientes 1─n users
clientes 1─n agenda_eventos
clientes 1─n publicaciones ──1─n publicacion_metricas
clientes 1─n metricas_periodo
clientes 1─n pedidos
clientes 1─n importaciones
videos                      (sin relación: cliente es texto libre)
```

### 3.3 Reglas de consulta y ordenamiento

Tres reglas que no son preferencias de estilo: son defensas contra fallas que ya se produjeron en una implementación anterior de este mismo sistema y que el entorno de desarrollo tapaba.

#### Regla 1 — Búsquedas de texto siempre con `whereLike` insensible

```php
// Correcto
$query->whereLike('marca', "%{$termino}%", caseSensitive: false);

// Prohibido
$query->where('marca', 'like', "%{$termino}%");
```

**Motivo.** PostgreSQL distingue mayúsculas en `LIKE`; SQLite no. Un buscador escrito con `LIKE` crudo funciona perfecto en desarrollo sobre SQLite y en producción no encuentra nada, salvo que el usuario escriba las mayúsculas exactas. El error no aparece en las pruebas si estas corren contra un motor distinto al de producción, que es exactamente por qué la sección 10 exige que corran contra PostgreSQL.

`whereLike(..., caseSensitive: false)` de Laravel 12 traduce a `ILIKE` en PostgreSQL y mantiene el comportamiento esperado en cualquier motor. Aplica a todo buscador del sistema: videos, clientes, publicaciones y pedidos.

#### Regla 2 — Collation explícita en las columnas de texto que se ordenan

Las columnas `clientes.marca`, `clientes.contacto`, `publicaciones.titulo` y `videos.titulo` declaran su collation en la migración:

```php
$table->string('marca', 120)->collation('es-AR-x-icu');
```

**Motivo.** Sin declararla, el orden alfabético hereda la collation del sistema operativo, que difiere entre la máquina de desarrollo y Railway. El resultado es un listado que en local ordena "Ñandú" entre "Nube" y "Ocaso", y en producción lo manda después de la Z. Es un error que nadie reporta como falla pero que hace ver desprolijo el panel.

**No usar collations no deterministas.** En PostgreSQL 16 rompen `LIKE` e `ILIKE` sobre esas columnas, lo que choca de frente con la regla 1.

#### Regla 3 — `composer.json` fija la versión de PHP de producción

```json
{
  "config": {
    "platform": {
      "php": "8.3.0"
    }
  }
}
```

**Motivo.** Sin ese pin, Composer resuelve las dependencias contra la versión de PHP de la máquina donde se ejecuta. Si esa versión es más nueva que la de producción, el `composer.lock` resultante puede exigir paquetes que no se instalan en el servidor, y la falla aparece recién en el despliegue. El pin desacopla la resolución de dependencias del PHP local, que es justamente lo que se necesita ahora que no hay una imagen que uniforme el entorno.

---

## 4. Servicio de importación

### 4.1 Secuencia

```
1. Recibir archivo o texto
2. Decodificar JSON            → error legible si falla
3. Validar estructura          → lista de campos faltantes
4. Verificar identidad de cliente → detener si no coincide
5. Abrir transacción
   5.1 Registrar la importación
   5.2 Guardar o actualizar métricas del período
   5.3 Por cada publicación: conciliar
   5.4 Escribir la medición
6. Confirmar transacción
7. Encolar la notificación al cliente
8. Devolver informe
```

### 4.2 Normalización del permalink

Antes de comparar se quitan el esquema, el `www`, la barra final y todos los parámetros de consulta. `https://www.instagram.com/p/ABC/?igshid=xyz` y `https://instagram.com/p/ABC` son la misma publicación. **Sin este paso, cada importación duplica todo.**

### 4.3 Idempotencia

La combinación de `id_media` único y `(publicacion_id, medido_el)` único garantiza que reimportar el mismo archivo actualice en lugar de duplicar. Es un requisito verificado por prueba automatizada, no una expectativa.

---

## 5. Almacenamiento de archivos

| Contenido | Dónde | Acceso |
|---|---|---|
| Miniaturas del portfolio | R2, bucket público con dominio propio | Público, cacheado por Cloudflare |
| Videos del portfolio | YouTube o Vimeo sin listar | Embebido |
| Imágenes de pedidos | R2, bucket privado | Enlace firmado, 15 minutos |
| Archivos finales | Drive del cliente o R2 privado | Enlace firmado |
| Respaldos | R2, bucket privado con retención | Solo servicio |

**Por qué R2 y no S3.** No cobra tráfico de salida, es compatible con la API de S3 —el driver de Laravel funciona sin cambios— y ya está en la cuenta de Cloudflare que se usa para DNS.

**Por qué no alojar los videos del portfolio.** Transcodificar y servir video es el costo más alto y el problema técnico más grande de un sitio así. YouTube o Vimeo sin listar lo resuelven gratis. Si más adelante molesta la marca del reproductor, se migra a Cloudflare Stream sin tocar el modelo de datos: solo cambia el campo `proveedor`.

---

## 6. Seguridad

### 6.1 Aislamiento entre clientes — tres niveles

Es el requisito crítico. Se implementa por capas para que ninguna falla aislada exponga datos.

**Nivel 1 — Consulta.** Un *global scope* de Eloquent aplicado a todos los modelos con `cliente_id`. Cuando el usuario autenticado tiene rol cliente, toda consulta se filtra por su `cliente_id` de forma automática, sin depender de que el desarrollador lo recuerde.

**Nivel 2 — Autorización.** Una policy por modelo. Toda acción sobre un recurso verifica pertenencia antes de ejecutarse, incluso si llegó por identificador directo en la URL.

**Nivel 3 — Interfaz.** Los componentes del portal reciben el cliente desde la sesión, nunca desde un parámetro de la petición. No existe una ruta del portal que acepte un `cliente_id`.

**Verificación.** Una batería de pruebas recorre cada ruta y cada acción del portal autenticada como cliente A intentando alcanzar recursos del cliente B. Debe responder 403 o 404 en todos los casos. **Esta batería se ejecuta en cada integración; si falla, no se despliega.**

### 6.2 Autenticación

Hash Argon2id. Límite de cinco intentos por minuto por IP y por correo. Segundo factor TOTP obligatorio para administradora. Sesiones en base de datos, cookies `HttpOnly`, `Secure` y `SameSite=Lax`. Cierre por inactividad a las dos horas. Invalidación de todas las sesiones al cambiar la clave.

### 6.3 Aplicación

Escapado automático de Blade —prohibido `{!! !!}` con contenido de usuario—. Consultas siempre por Eloquent o parámetros vinculados. Validación de tipo real de imagen, no solo de extensión. Cabeceras `Content-Security-Policy`, `X-Content-Type-Options`, `Referrer-Policy` y `Permissions-Policy`. Dependencias auditadas con `composer audit` en cada integración.

### 6.4 Datos personales

El sistema guarda datos de contacto de clientes y sus métricas de redes. No guarda datos de tarjetas ni información sensible. Los respaldos se cifran en reposo. Al archivar un cliente, sus datos se conservan; existe un comando de eliminación definitiva para ejecutar ante un pedido formal de baja.

---

## 7. Infraestructura en Railway

### 7.1 Servicios

| Servicio | Comando | Escala |
|---|---|---|
| `web` | servidor HTTP de la aplicación | 1 instancia, ampliable |
| `worker` | `php artisan queue:work --tries=3 --timeout=90` | 1 instancia |
| `scheduler` | `php artisan schedule:work` | 1 instancia |
| `postgres` | Complemento gestionado | Respaldos diarios |
| `redis` | Complemento gestionado | Colas y caché |

**Los tres procesos van separados a propósito.** Si el worker se cae procesando un correo, el sitio sigue en pie. Si se despliega una versión nueva, la cola se drena sin cortar peticiones web.

### 7.2 Construcción

**Se usa la detección automática de Railway (Nixpacks). No hay `Dockerfile` propio ni `docker-compose.yml` en este proyecto.**

Esta decisión reemplaza a la anterior, que sí definía una imagen propia. El motivo del cambio: mantener una imagen propia solo tiene sentido si se la reproduce localmente, y eso obliga a instalar Docker y WSL2 en la máquina de desarrollo. En la práctica ese camino trajo bloqueos por permisos de Windows y un costo de mantenimiento que este proyecto no justifica. Railway con Nixpacks ya funcionó sin fricción en otros proyectos del mismo autor, con el entorno local resuelto con Herd.

**La versión de PHP se fija en dos lugares, no en una imagen:**

1. `config.platform.php` en `composer.json` — controla contra qué versión resuelve Composer las dependencias. Ver regla 3 de la sección 3.3.
2. La configuración de PHP en Railway — controla qué binario corre en el servidor.

Ambos valores deben coincidir. Si divergen, el `composer.lock` se genera contra una versión y se instala contra otra.

**Extensiones necesarias:** `pdo_pgsql`, `redis`, `gd`, `intl`, `bcmath`, `zip`, `opcache`. Nixpacks resuelve la mayoría a partir de las dependencias declaradas en `composer.json`; las que falten se agregan por configuración de Railway. **Verificarlas en el primer despliegue**, no darlas por sentadas: una extensión ausente se manifiesta como un error de ejecución en la primera pantalla que la use, no en el build.

**Compensación aceptada.** Sin imagen propia, el entorno local y el de producción no son idénticos. La contrapartida está en la sección 10: las pruebas corren contra PostgreSQL en integración continua, que es donde importa que el entorno se parezca a producción.

### 7.3 Entornos

| Entorno | Rama | Dominio | Base de datos | Ejecución |
|---|---|---|---|---|
| Local | — | `.test` de Herd | PostgreSQL 16 nativo | Herd |
| Staging | `develop` | `staging.dominio` | Complemento de Railway | Nixpacks |
| Producción | `main` | `dominio` | Complemento de Railway, con respaldos | Nixpacks |

**Entorno local.** Laravel Herd para PHP y el servidor web, PostgreSQL 16 instalado de forma nativa. Sin Docker y sin WSL2. Redis no se instala en local: corre solo en Railway (sección 7.5), porque a diferencia de SQLite contra PostgreSQL, no hay una clase de bug que quede oculta por resolver colas y caché en línea en desarrollo.

**PostgreSQL también en local, no SQLite.** Es lo que hace visibles en desarrollo los problemas de las reglas 1 y 2 de la sección 3.3. Usar SQLite localmente los oculta hasta el despliegue.

**Staging nunca comparte base de datos con producción.** Se puebla con seeders o con una copia anonimizada.

### 7.4 Despliegue

Railway despliega solo al recibir un push en la rama correspondiente. En cada despliegue:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Comprobación de estado en `/up`. Si falla, Railway no promueve la versión nueva.

**Regla sobre migraciones:** toda migración debe poder aplicarse sobre la base en producción sin bloquearla. Nada de renombrar columnas en uso; primero se agrega la nueva, se migran los datos, se despliega el código y recién después se elimina la vieja.

### 7.5 Variables de entorno

```
APP_NAME, APP_ENV, APP_KEY, APP_DEBUG=false, APP_URL
DB_CONNECTION=pgsql, DATABASE_URL
REDIS_URL
QUEUE_CONNECTION=redis, CACHE_STORE=redis, SESSION_DRIVER=database
R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_ENDPOINT
R2_BUCKET_PUBLICO, R2_URL_PUBLICO
R2_BUCKET_PRIVADO
MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS
TRUSTED_PROXIES=*
```

`TRUSTED_PROXIES` es necesario para que Laravel lea la IP real detrás de Cloudflare. Sin esto, **el límite de intentos de ingreso bloquea a todos los usuarios juntos**, porque ve una sola IP.

---

## 8. Configuración de Cloudflare

### 8.1 DNS y TLS

Registro CNAME hacia el dominio de Railway, con proxy activado. Modo TLS **Full (strict)**. Always Use HTTPS activo. HSTS con 6 meses, incluyendo subdominios, después de verificar que todo funciona por HTTPS.

### 8.2 Caché

| Ruta | Regla |
|---|---|
| `/build/*`, `/img/*`, `/fonts/*` | Cachear, un año |
| `/` y páginas públicas | Cachear, 5 minutos, con purga al publicar un video |
| `/panel/*`, `/portal/*`, `/ingresar` | **Nunca cachear** |
| `/archivos/*` | Nunca cachear |

Cachear una respuesta del portal sería servirle a un cliente los datos de otro. Es la falla más grave posible en este sistema y la regla de exclusión es obligatoria.

### 8.3 Seguridad perimetral

- Reglas WAF gestionadas, activas.
- Límite de peticiones en `/ingresar`: 10 por minuto por IP.
- Límite en `/clave/recuperar`: 5 por hora por IP.
- Bot Fight Mode activo en el portfolio; desactivado en `/panel` y `/portal` para no interferir con el uso legítimo.
- Turnstile en el formulario de ingreso.

### 8.4 R2

Dos buckets: `catalina-publico`, con dominio propio y caché de un año; y `catalina-privado`, sin acceso público, servido solo por enlaces firmados desde la aplicación.

---

## 9. Observabilidad y respaldos

**Registros.** Canal diario con retención de 14 días, en formato JSON, y captura de errores en Sentry con avisos por correo.

**Métricas mínimas a vigilar:** tiempo de respuesta del percentil 95, tasa de error 5xx, largo de la cola y trabajos fallidos, uso de conexiones de PostgreSQL.

**Respaldos.** Diarios automáticos de Railway con siete días de retención, más un volcado semanal a R2 con retención de tres meses. **Una restauración de prueba en staging cada trimestre**; un respaldo que nunca se restauró no es un respaldo.

**Auditoría.** La tabla `importaciones` guarda todo archivo procesado. Los cambios de estado de pedidos y las altas y bajas de clientes se registran en el log de la aplicación con usuario y fecha.

---

## 10. Integración continua

En cada push, GitHub Actions ejecuta:

1. `composer install` y `npm ci`
2. `./vendor/bin/pint --test`
3. `./vendor/bin/phpstan analyse`
4. `php artisan test`
5. `composer audit`

Una rama no se fusiona a `develop` si algún paso falla. Cobertura mínima exigida: 70 % general, **100 % en el servicio de importación y en las pruebas de aislamiento entre clientes**.

### 10.1 Las pruebas corren contra PostgreSQL

El workflow levanta un servicio `postgres:16` y las pruebas se ejecutan contra él. **Nunca contra SQLite.**

Este es el punto donde se detectan las fallas de las reglas 1 y 2 de la sección 3.3. Correr las pruebas contra un motor distinto al de producción convierte a la batería de pruebas en un certificado de que el código funciona en un entorno que no existe.

### 10.2 Dos trampas de configuración que hay que revisar

Ambas producen el mismo síntoma —las pruebas corren contra el motor equivocado sin avisar— y ambas ya ocurrieron.

**Trampa 1 — `phpunit.xml`.** El archivo **no debe** fijar `DB_CONNECTION=sqlite`. Es el valor que trae Laravel por defecto en varios esqueletos y pasa desapercibido porque las pruebas igual pasan.

```xml
<!-- Quitar o comentar estas líneas -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

**Trampa 2 — variables de entorno a nivel job en GitHub Actions.** Una variable definida en el bloque `env` del job **pisa lo que diga `phpunit.xml`**. Corregir uno de los dos lugares y no el otro deja el problema intacto, con la agravante de que quien lo corrigió cree que ya está resuelto.

**Verificar los dos archivos, siempre.** Y comprobarlo empíricamente: una prueba que consulte el driver activo y falle si no es `pgsql` cierra la discusión de una vez.

```php
it('corre contra PostgreSQL', function () {
    expect(DB::connection()->getDriverName())->toBe('pgsql');
});
```
