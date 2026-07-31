# Arquitectura, modelo de datos e infraestructura

**Versión:** 1.0
**Fecha:** 31 de julio de 2026
**Documento hermano:** `01-especificacion-funcional.md`

---

## 1. Decisiones de stack

| Capa | Elección | Motivo |
|---|---|---|
| Lenguaje | PHP 8.3 | Requerido por Laravel 12 |
| Framework | Laravel 12 | Autenticación, autorización, colas, migraciones y almacenamiento ya resueltos |
| Interfaz | Livewire 3 + Alpine.js | El sistema es un panel con formularios y tablas; una SPA agregaría una capa de API sin beneficio |
| Estilos | Tailwind CSS 4 | El mockup ya está resuelto con utilidades |
| Compilación | Vite | Estándar de Laravel |
| Base de datos | PostgreSQL 16 | Soporte nativo de JSONB para las métricas |
| Colas y caché | Redis | Correos y procesos en segundo plano |
| Pruebas | Pest | |
| Análisis estático | Larastan nivel 6 | |
| Formato | Laravel Pint | |

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

### 7.2 Imagen

Se usa un `Dockerfile` propio, no la detección automática. Da control sobre la versión de PHP, las extensiones y OPcache, y hace reproducible el entorno local.

Base recomendada: `serversideup/php:8.3-fpm-nginx`, que ya trae PHP-FPM y nginx configurados para Laravel.

Extensiones necesarias: `pdo_pgsql`, `redis`, `gd` o `imagick`, `intl`, `bcmath`, `zip`, `opcache`.

### 7.3 Entornos

| Entorno | Rama | Dominio | Base de datos |
|---|---|---|---|
| Local | — | `localhost` | Docker |
| Staging | `develop` | `staging.dominio` | Propia |
| Producción | `main` | `dominio` | Propia, con respaldos |

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
FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET, R2_ENDPOINT, R2_URL
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
