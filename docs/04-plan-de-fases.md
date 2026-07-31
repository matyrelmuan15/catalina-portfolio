# Plan de fases

**Versión:** 1.0
**Fecha:** 31 de julio de 2026

Once fases, de la 0 a la 10. Cada una entrega algo verificable. **Ninguna fase se cierra sin actualizar `AVANCE.md`.**

Estimaciones para una persona con dedicación parcial. Ajustar según el equipo real.

---

## Cómo se cierra una fase

1. Todos los criterios de aceptación cumplidos.
2. Pruebas automatizadas de la fase escritas y en verde.
3. Integración continua en verde.
4. Desplegada en staging y probada a mano.
5. `AVANCE.md` actualizado con lo hecho, lo decidido y lo pendiente.
6. Rama fusionada a `develop`.

---

## Fase 0 — Fundaciones

**Objetivo.** Que exista un proyecto desplegable, con la infraestructura de pie y sin funcionalidad.

**Alcance**

- Repositorio con la estructura de `README.md` y los cuatro documentos.
- `AVANCE.md` creado, con la primera entrada.
- Laravel 12 con PHP 8.3, Livewire 3, Tailwind 4, Pest, Pint y Larastan.
- `Dockerfile` y `docker-compose.yml` con aplicación, PostgreSQL y Redis.
- Proyecto en Railway con los cinco servicios y los dos entornos.
- Dominio en Cloudflare, proxy activo, TLS estricto, `TRUSTED_PROXIES` configurado.
- Buckets de R2 creados y disco `r2` configurado en Laravel.
- GitHub Actions con los cinco pasos de integración.
- Ruta `/up` respondiendo y usada como comprobación de estado.

**Criterios de aceptación**

- Un push a `develop` despliega a staging solo y el sitio responde por HTTPS con certificado válido.
- El worker procesa un trabajo de prueba.
- Un archivo subido a R2 se lee desde la aplicación.
- La integración continua corre entera en verde.

**Riesgo.** La configuración de proxy de Cloudflare. Si `TRUSTED_PROXIES` queda mal, el límite de intentos de ingreso bloqueará a todos los usuarios a la vez y se descubre tarde. Verificar en esta fase que la aplicación registra la IP real del visitante.

**Estimación.** 3 a 4 días.

---

## Fase 1 — Autenticación y roles

**Objetivo.** Que existan los tres tipos de acceso con sus fronteras.

**Alcance**

- Tablas `users` y `clientes` con migraciones y factories.
- Ingreso, salida, recuperación de clave y cambio de clave.
- Roles `admin` y `cliente`; middlewares de cada contexto.
- Segundo factor TOTP para administradora.
- Global scope `PerteneceAlCliente` y policies base.
- Límite de intentos y Turnstile en el formulario.
- Estructuras vacías de `/panel` y `/portal` con navegación.
- Seeders: una administradora y cuatro clientes.

**Criterios de aceptación**

- Un cliente que entra a `/panel` recibe 403.
- Un visitante sin sesión que entra a `/portal` va al ingreso.
- Seis intentos fallidos en un minuto quedan bloqueados.
- La administradora no puede entrar sin el segundo factor.
- **La batería de pruebas de aislamiento existe y pasa**, aunque todavía haya pocos modelos que verificar.

**Riesgo.** Es la fase que sostiene todo lo demás. No avanzar con el aislamiento a medias: cada modelo con `cliente_id` que se agregue después debe sumar su prueba en el mismo commit.

**Estimación.** 4 a 5 días.

---

## Fase 2 — Portfolio público

**Objetivo.** Sitio en línea, compartible, que ya sirve para conseguir clientes.

**Alcance**

- Tabla `videos` y modelo.
- Las seis secciones del mockup, adaptables desde 360 píxeles.
- Filtro por categoría sin recarga.
- Ventana de reproducción con YouTube y Vimeo.
- Etiquetas Open Graph, `sitemap.xml`, `robots.txt`, datos estructurados de tipo Person.
- Reglas de caché de Cloudflare para las rutas públicas.
- Seeder con doce piezas de ejemplo.

**Criterios de aceptación**

- Lighthouse: rendimiento 90 o más, accesibilidad 95 o más en móvil.
- Compartir el enlace en WhatsApp muestra imagen, título y descripción.
- El filtro responde en menos de 100 milisegundos.
- Se ve correctamente en Chrome, Safari, Firefox y Edge, en escritorio y en teléfono.

**Entregable.** El portfolio ya se puede publicar y usar, aunque el panel todavía no exista.

**Estimación.** 5 a 6 días.

---

## Fase 3 — Gestión de videos

**Objetivo.** Que Catalina cargue su trabajo sin ayuda técnica.

**Alcance**

- Listado con buscador, filtro y los cuatro contadores.
- Alta, edición y baja con confirmación.
- Alternar publicado y destacado desde el listado.
- Carga de miniatura a R2 con recorte a 9:16 y conversión a WebP.
- Lectura automática de la miniatura del proveedor cuando no se sube una.
- Purga de caché de Cloudflare al publicar o despublicar.

**Criterios de aceptación**

- Cargar un video y verlo en el sitio en menos de un minuto, sin pasos técnicos.
- Una imagen de 5 MB se procesa y queda por debajo de 200 KB.
- Despublicar lo saca del sitio de inmediato, con la caché purgada.

**Estimación.** 4 días.

---

## Fase 4 — Clientes y accesos

**Objetivo.** Dar de alta marcas con acceso propio.

**Alcance**

- Listado con marca, contacto, próxima fecha y pedidos abiertos.
- Alta que crea cliente y usuario en una transacción.
- Archivado y desarchivado.
- Restablecimiento de clave desde la ficha.
- Ficha con las cuatro solapas, todavía vacías.
- Portal del cliente con su marca y menú.

**Criterios de aceptación**

- El alta de un cliente con correo repetido falla con mensaje claro y no deja registros a medias.
- Un cliente archivado no puede iniciar sesión.
- Las pruebas de aislamiento cubren clientes y usuarios.

**Estimación.** 3 a 4 días.

---

## Fase 5 — Agenda y calendario

**Objetivo.** Que el cliente vea sus fechas.

**Alcance**

- Tabla `agenda_eventos`.
- Vista mensual con navegación, en panel y en portal.
- Alta desde el día del calendario y desde botón.
- Edición y baja, solo administradora.
- Lista de próximas fechas.
- Colores por tipo, consistentes.
- Comportamiento adaptable en pantallas angostas.

**Criterios de aceptación**

- Una fecha cargada aparece en el portal del cliente sin recargar sesión.
- El cliente no tiene ningún camino para crear, editar o borrar.
- El calendario respeta la zona horaria `America/Argentina/Buenos_Aires`.

**Riesgo.** Zonas horarias. Guardar las fechas como `date`, sin hora, evita la clase de error donde un evento del día 1 aparece el día 31 del mes anterior.

**Estimación.** 5 días.

---

## Fase 6 — Publicaciones

**Objetivo.** El registro de piezas, con sus 25 columnas.

**Alcance**

- Tablas `publicaciones` y `publicacion_metricas`.
- Tabla ancha con desplazamiento horizontal y primera columna fija.
- Ficha de detalle.
- Formulario de alta y edición de los campos propios.
- Estados y su transición.
- Aparición automática en el calendario.
- Vista reducida para el cliente.

**Criterios de aceptación**

- Una publicación planificada aparece en el calendario en su fecha.
- El cliente no ve las columnas internas.
- La tabla se usa sin trabas con 200 publicaciones cargadas.
- La tasa de interacción se calcula sola cuando falta.

**Estimación.** 5 a 6 días.

---

## Fase 7 — Importación y conciliación

**Objetivo.** El corazón del sistema. Convertir el JSON en datos conciliados.

**Alcance**

- Servicios `ImportadorMetricas` y `ConciliadorPublicaciones`.
- Validación completa con mensajes en español y accionables.
- Verificación de identidad de cliente.
- Normalización de permalink.
- Cascada de conciliación de tres niveles.
- Transacción e idempotencia.
- Tabla `importaciones` con el archivo original.
- Interfaz de importación con archivo o texto pegado, informe de resultado y visor del formato esperado.

**Criterios de aceptación**

- Un archivo con 20 publicaciones se importa entero y el informe es correcto.
- **Reimportar el mismo archivo no crea duplicados.**
- Un archivo con una publicación inválida no aplica ninguna: la transacción se revierte.
- Permalinks con `?igshid=` concilian con los guardados sin parámetros.
- Un archivo con el cliente equivocado se detiene con aviso.
- Cobertura del 100 % en ambos servicios.

**Riesgo.** Es la fase con más reglas y donde un error silencioso corrompe datos sin avisar. Escribir primero las pruebas, con archivos de ejemplo reales, y recién después el código.

**Estimación.** 6 a 7 días.

---

## Fase 8 — Métricas del período

**Objetivo.** Lo que el cliente entra a ver.

**Alcance**

- Tabla `metricas_periodo`.
- Cuatro indicadores con variación.
- Gráfico de alcance mensual.
- Tabla de piezas con mejor rendimiento.
- Período y fecha de actualización visibles.
- Estados vacíos explícitos.
- Selector de período cuando hay más de uno.

**Criterios de aceptación**

- Sin datos del período se ve un mensaje claro, nunca ceros.
- Los números usan formato argentino.
- Las columnas sin dato muestran un guion, no un cero.

**Estimación.** 4 días.

---

## Fase 9 — Pedidos y notificaciones

**Objetivo.** Cerrar el circuito de trabajo con el cliente.

**Alcance**

- Tabla `pedidos`.
- Alta desde el portal, con imagen opcional a R2 privado.
- Validación de tipo real de imagen y límite de 8 MB.
- Estados y respuesta desde el panel.
- Bandeja global con contadores.
- Enlaces firmados para las imágenes.
- Las cinco notificaciones por correo, en cola.
- Preferencias de notificación por usuario.

**Criterios de aceptación**

- Un pedido con imagen recorre los tres estados y ambas partes reciben su correo.
- Un archivo renombrado a `.jpg` que no es imagen se rechaza.
- El enlace firmado vence a los 15 minutos.
- Ningún correo se envía dentro del pedido web.

**Estimación.** 5 días.

---

## Fase 10 — Endurecimiento y producción

**Objetivo.** Salir a producción con respaldo y sin sorpresas.

**Alcance**

- Cabeceras de seguridad y política de contenido.
- Reglas WAF, límites de peticiones y Turnstile en producción.
- Revisión completa de las reglas de caché, **con verificación explícita de que `/panel` y `/portal` nunca se cachean**.
- Auditoría de accesibilidad con teclado y lector de pantalla.
- Revisión de rendimiento con datos de volumen real.
- Respaldos configurados y **una restauración de prueba ejecutada**.
- Sentry y avisos de error.
- Manual breve de uso para Catalina, con capturas.
- Carga de datos reales: videos, clientes y primer período de métricas.
- Publicación del dominio definitivo.

**Criterios de aceptación**

- La batería de aislamiento pasa entera contra producción.
- Una restauración de respaldo en staging se completó y se verificó.
- El manual permite operar el sistema sin asistencia.
- Cabeceras de seguridad verificadas con herramienta externa.

**Estimación.** 5 a 6 días.

---

## Resumen

| Fase | Contenido | Estimación |
|---|---|---|
| 0 | Fundaciones | 3-4 días |
| 1 | Autenticación y roles | 4-5 días |
| 2 | Portfolio público | 5-6 días |
| 3 | Gestión de videos | 4 días |
| 4 | Clientes y accesos | 3-4 días |
| 5 | Agenda y calendario | 5 días |
| 6 | Publicaciones | 5-6 días |
| 7 | Importación y conciliación | 6-7 días |
| 8 | Métricas del período | 4 días |
| 9 | Pedidos y notificaciones | 5 días |
| 10 | Endurecimiento y producción | 5-6 días |
| | **Total** | **49 a 56 días de trabajo** |

**Puntos de entrega parcial.** Al cerrar la fase 3 el portfolio ya sirve para conseguir clientes y puede publicarse. Al cerrar la fase 5 el portal ya aporta valor a las marcas activas. No hace falta esperar la fase 10 para empezar a usar el sistema.

---

## Después de producción

- Revisión mensual de registros de error y trabajos fallidos.
- Actualización de dependencias cada mes, con pruebas en staging.
- Restauración de respaldo de prueba cada trimestre.
- Revisión trimestral de `AVANCE.md` para pasar a los documentos lo que se haya vuelto permanente.

**Candidatos a la versión 2:** conexión directa a Meta Graph API, estado de aprobación en pedidos, propuestas de cambio de fecha por parte del cliente, exportación de informes en PDF y panel comparativo entre marcas.
