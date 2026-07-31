# AVANCE

Estado real del proyecto. **Se actualiza al cerrar cada sesión de trabajo y al terminar cada fase.**

Quien retome el trabajo —persona o asistente de IA— tiene que poder leer solo este archivo y saber en qué punto está todo. Las entradas nuevas van **arriba**. Las anteriores no se editan ni se borran: si algo cambió, se escribe una entrada nueva que lo diga.

---

## Estado general

| | |
|---|---|
| **Fase actual** | 0 — Fundaciones |
| **Última actualización** | 31 de julio de 2026 |
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

---

## Bitácora

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
