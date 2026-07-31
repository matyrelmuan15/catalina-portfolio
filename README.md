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
│   └── 04-plan-de-fases.md
├── mockup/
│   └── portfolio-mockup.html
├── app/
├── database/
├── resources/
├── routes/
├── tests/
├── docker/
├── Dockerfile
├── docker-compose.yml
└── railway.json
```

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

```bash
cp .env.example .env
docker compose up -d
composer install && npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

Los seeders cargan una administradora, cuatro clientes de ejemplo con sus accesos, publicaciones con métricas y pedidos en distintos estados. Alcanza para recorrer el sistema completo sin datos reales.

**Antes de cada push:**

```bash
./vendor/bin/pint          # formato
./vendor/bin/phpstan       # análisis estático
php artisan test           # pruebas
```

---

## 6. Contactos y datos del proyecto

- **Titular:** Catalina Avendaño — Viedma, Río Negro, Argentina
- **Correo:** catalinaavendanio@gmail.com
- **Infraestructura:** Railway (aplicación y base de datos), Cloudflare (DNS, CDN, WAF y almacenamiento de archivos)
