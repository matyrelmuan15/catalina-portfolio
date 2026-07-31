# Especificación funcional

**Proyecto:** Portfolio y portal de clientes de Catalina Avendaño
**Versión:** 1.0
**Fecha:** 31 de julio de 2026
**Documento hermano:** `02-arquitectura-y-datos.md`

---

## 1. Contexto y objetivo

Catalina Avendaño produce contenido audiovisual para marcas desde Viedma, Río Negro: videos UGC, piezas de marketing, fotografía, modelaje y gestión de redes y pauta.

Hoy su trabajo se muestra y se coordina por WhatsApp e Instagram. Eso produce tres problemas concretos:

1. **No hay una vitrina propia.** Cada prospecto nuevo requiere armar a mano una selección de trabajos.
2. **Las fechas se pierden.** Lo comprometido con cada marca vive en conversaciones sueltas.
3. **Los resultados no se muestran.** El rendimiento del contenido queda en capturas de pantalla, sin continuidad ni comparación.

El sistema resuelve las tres cosas con tres capas:

| Capa | Usuario | Propósito |
|---|---|---|
| Portfolio público | Prospectos | Conseguir clientes nuevos |
| Panel de administración | Catalina | Operar todo el negocio |
| Portal de clientes | Marcas activas | Ver fechas, pedir trabajo y ver resultados |

**Lo que el sistema no es.** No reemplaza WhatsApp como canal de conversación, no factura, no firma contratos y no publica en redes. Concentra lo que hoy se dispersa: obra, fechas, pedidos y resultados.

---

## 2. Usuarios y roles

### 2.1 Visitante

Sin credenciales. Accede solo al portfolio público.

### 2.2 Administradora

Usuaria única en la práctica, aunque el sistema soporta varias. Accede a todo: videos, clientes, agenda, publicaciones, pedidos, métricas y configuración.

### 2.3 Cliente

Una cuenta por marca. La crea la administradora; **no existe registro abierto**. Accede únicamente a los datos de su propia marca.

### 2.4 Regla de aislamiento

> Un usuario con rol cliente no puede acceder, por ningún camino, a datos de otra marca.

Es el requisito de seguridad más importante del sistema. Se implementa en tres niveles —consulta, autorización e interfaz— y se verifica con pruebas automatizadas específicas. Ver `02-arquitectura-y-datos.md`, sección 6.

---

## 3. Portfolio público

### 3.1 Estructura

| Sección | Contenido |
|---|---|
| Cabecera | Nombre a la izquierda, navegación al centro, iniciar sesión y contacto a la derecha |
| Portada | Título, bajada, dos llamados a la acción y tira animada de piezas verticales |
| Trabajos | Grilla filtrable de piezas publicadas |
| Servicios | Once servicios agrupados en tres familias |
| Sobre mí | Retrato, texto y tres cifras |
| Contacto | WhatsApp, correo, Instagram y ubicación |
| Pie | Datos de contacto y año |

### 3.2 Requisitos funcionales

- **RF-01.** La grilla muestra solo videos con `publicado = verdadero`.
- **RF-02.** Orden: primero los destacados, luego por fecha descendente.
- **RF-03.** Filtro por categoría: UGC, Marketing, Lifestyle, Fotografía, Modelaje y Redes. El filtro no recarga la página.
- **RF-04.** Al abrir una pieza se muestra el video reproducible con cliente, fecha y descripción.
- **RF-05.** El botón de contacto abre WhatsApp con el número +54 9 2931 40-3502.
- **RF-06.** El sitio es indexable por buscadores, con etiquetas Open Graph para que el enlace se vea bien al compartirlo.
- **RF-07.** Diseño adaptable desde 360 píxeles de ancho.
- **RF-08.** El botón de iniciar sesión está visible en la cabecera.

### 3.3 Servicios

| Familia | Servicios |
|---|---|
| Frente a cámara | Videos UGC, videos lifestyle, modelaje |
| Producción | Videos de marketing, grabación y edición, fotografía, diseño gráfico |
| Crecimiento | Manejo de redes, Meta Ads, Google Ads, sitios web a medida |

### 3.4 Rendimiento

- Mayor Contentful Paint por debajo de 2,5 segundos en 4G.
- Los videos no se cargan hasta que el visitante abre la pieza.
- Las miniaturas se sirven en WebP con carga diferida.

---

## 4. Gestión de videos del portfolio

Módulo del panel. Controla qué se ve en el sitio público.

### 4.1 Campos

| Campo | Tipo | Obligatorio | Observaciones |
|---|---|---|---|
| Título | Texto | Sí | Hasta 120 caracteres |
| Cliente | Texto libre | Sí | No es una relación: aparecen marcas que no son clientes del portal |
| Categoría | Lista | Sí | Las seis del filtro |
| Fecha | Fecha | Sí | Del trabajo, no de la carga |
| Enlace del video | URL | Sí | YouTube, Vimeo o archivo |
| Descripción | Texto | No | Hasta 500 caracteres |
| Miniatura | Imagen | No | Si no hay, se usa la del proveedor |
| Publicado | Sí/No | Sí | Por defecto sí |
| Destacado | Sí/No | Sí | Por defecto no |

### 4.2 Requisitos funcionales

- **RF-10.** Alta, edición, baja y listado con buscador por título y cliente, y filtro por categoría.
- **RF-11.** Publicar y destacar se alternan desde el listado, sin abrir el formulario.
- **RF-12.** La baja pide confirmación y es definitiva.
- **RF-13.** El listado muestra cuatro contadores: cargados, publicados, ocultos y destacados.
- **RF-14.** Todo cambio se refleja en el sitio público de inmediato.

---

## 5. Clientes

### 5.1 Campos

Marca, persona de contacto, correo de contacto, teléfono, color de ficha, estado (activo o archivado).

Cada cliente tiene **un usuario asociado** con correo y clave para entrar al portal.

### 5.2 Requisitos funcionales

- **RF-20.** El listado muestra marca, contacto, próxima fecha comprometida y cantidad de pedidos abiertos.
- **RF-21.** El alta crea el cliente y su usuario en una sola operación, con clave inicial que la administradora comunica por fuera del sistema.
- **RF-22.** Los clientes se **archivan**, no se eliminan. Un cliente archivado conserva su historial, no aparece en los listados activos y su usuario no puede iniciar sesión.
- **RF-23.** La administradora puede restablecer la clave de un cliente desde la ficha.
- **RF-24.** La ficha tiene cuatro solapas: calendario, publicaciones, pedidos y métricas.

---

## 6. Agenda y calendario

Es el punto de contacto principal con el cliente.

### 6.1 Tipos de evento

| Tipo | Significado |
|---|---|
| Grabación | Día de rodaje o de sesión |
| Entrega | Fecha en que el material queda disponible |
| Reunión | Videollamada o encuentro |

Las **publicaciones** también aparecen en el calendario, pero no son eventos de agenda: son una entidad propia (sección 7) que se muestra en su fecha. Por eso "Publicación" no figura como tipo de evento.

### 6.2 Requisitos funcionales

- **RF-30.** Vista mensual con navegación entre meses y lista de próximas fechas debajo.
- **RF-31.** Cada evento tiene fecha, tipo, título y una nota visible para el cliente.
- **RF-32.** Solo la administradora crea, edita y elimina. El cliente únicamente consulta.
- **RF-33.** El evento aparece en el portal del cliente apenas se guarda.
- **RF-34.** Cada tipo tiene un color propio y consistente en todo el sistema.
- **RF-35.** En pantallas angostas el calendario se desplaza en horizontal; la lista de próximas fechas siempre es legible.

### 6.3 Decisión tomada

El cliente **no** puede proponer ni pedir cambios de fecha desde el sistema. Si necesita mover algo, lo hace por WhatsApp o crea un pedido. Incorporar negociación de fechas exige estados, contrapropuestas y notificaciones cruzadas; no se justifica para el volumen actual. Queda anotado como candidato a versión 2.

---

## 7. Publicaciones

Es la entidad central del módulo de resultados. Una publicación es una pieza que se publica —o se va a publicar— en una red, con su rendimiento asociado.

### 7.1 Origen de los datos

Los campos se dividen en tres grupos, y la diferencia define el comportamiento del sistema:

| Grupo | Campos | Se completa |
|---|---|---|
| A — Meta | ID Media, permalink, fecha, formato, copy, me gusta, comentarios, alcance, vistas, compartidos, guardados, interacciones | Al importar el JSON |
| B — Derivado | Hashtags, tasa de interacción, plataforma, medido el | Se calcula |
| C — Propio | Título interno, estado, pilar, cliente, archivo final, creativo en Figma | A mano, en el panel |

> **Regla:** la importación nunca sobrescribe los campos del grupo C.

### 7.2 Columnas de la tabla

Publicación, Estado, Fecha, Plataforma, Formato, Pilar, Cliente, Cliente (texto), Alcance, Vistas, Interacciones, Me gusta, Comentarios, Compartidos, Guardados, Clics al enlace, Tasa de interacción, Seguidores al publicar, Medido el, Copy, Hashtags, Permalink, ID Media, Archivo final y Creativo en Figma.

### 7.3 Estados

`Planificada` → `En producción` → `Aprobada` → `Publicada` → `Medida`

El paso a `Medida` es automático cuando la importación trae métricas para esa pieza. El resto lo maneja la administradora.

### 7.4 Limitaciones conocidas de la fuente

Deben estar documentadas en la interfaz, no solo en el código:

- **Clics al enlace.** Meta no expone clics por pieza en publicaciones orgánicas de feed de Instagram. Solo hay dato en historias con sticker de enlace y en publicaciones de Facebook. La columna queda vacía; **está prohibido rellenarla con cero**, porque un cero se lee como un resultado y la ausencia de dato no lo es.
- **Seguidores al publicar.** Meta entrega el total actual de la cuenta, no una foto por publicación. La versión 1 guarda el total al momento de medir y lo declara como aproximación.

### 7.5 Requisitos funcionales

- **RF-40.** Tabla con las 25 columnas, desplazamiento horizontal y primera columna fija.
- **RF-41.** Cada fila abre una ficha con el detalle completo.
- **RF-42.** Formulario de alta y edición para los campos del grupo C.
- **RF-43.** Las publicaciones aparecen automáticamente en el calendario del cliente, en su fecha.
- **RF-44.** El cliente ve un subconjunto de columnas: sin estado, ID Media, creativo, copy, hashtags, clics, seguidores ni datos internos.
- **RF-45.** La tasa de interacción se calcula como interacciones ÷ alcance × 100 cuando no viene en el archivo.

---

## 8. Importación de métricas

### 8.1 Flujo

```
Meta Graph API → Claude (pedido en lenguaje natural) → archivo JSON → importación en el panel
```

La versión 1 **no** se conecta a Meta desde el sistema. Trabaja por archivo. El contrato del archivo está en `03-formato-metricas.md`.

### 8.2 Validaciones previas

La importación se rechaza entera si falla alguna. No se aplica nada a medias.

1. El contenido es JSON válido.
2. Está `periodo`, y está `resumen` o `publicaciones`.
3. Si viene `resumen`, sus cuatro valores son números enteros.
4. Si viene `cliente`, coincide con la marca en la que se está importando. Si no coincide, se avisa y se detiene: cargar los números de una marca en la ficha de otra es el error más caro de este flujo.

### 8.3 Conciliación

Por cada publicación del archivo, en este orden:

1. **Por ID Media.** Identificador de Meta, inmutable. Es la coincidencia confiable.
2. **Por permalink**, si no hubo ID. Se normaliza la URL quitando parámetros de seguimiento (`igshid`, `utm_*`) antes de comparar.
3. **Por fecha y plataforma**, solo contra publicaciones sin permalink, es decir las planificadas. Si hay **exactamente una** candidata, se vincula. Si hay dos o más, no se adivina: se crea una nueva.

Si no hay coincidencia, se **crea** la publicación y aparece en el calendario en su fecha.

### 8.4 Requisitos funcionales

- **RF-50.** La importación es una transacción: se aplica completa o no se aplica.
- **RF-51.** Es idempotente: importar dos veces el mismo archivo no duplica nada.
- **RF-52.** Se informa el resultado: creadas, actualizadas y vinculadas con planificadas.
- **RF-53.** Cada importación queda registrada con fecha, usuario, nombre del archivo, contenido original y resultado.
- **RF-54.** Se puede subir un archivo o pegar el contenido.
- **RF-55.** El panel muestra el formato esperado sin salir de la pantalla.
- **RF-56.** El histórico de métricas por pieza se conserva: una misma publicación puede tener varias mediciones en fechas distintas.

---

## 9. Métricas del período

Lo que ve el cliente al entrar a su portal.

- **RF-60.** Cuatro indicadores: alcance, interacciones, seguidores nuevos y piezas publicadas.
- **RF-61.** Variación porcentual contra el mes anterior, con signo.
- **RF-62.** Gráfico de alcance de los últimos meses disponibles.
- **RF-63.** Tabla de piezas con mejor rendimiento.
- **RF-64.** Período y fecha de última actualización siempre visibles. El cliente tiene que saber a qué momento corresponden los números sin preguntar.
- **RF-65.** Si no hay datos del período, se muestra un estado vacío explícito, nunca ceros.

---

## 10. Pedidos

### 10.1 Flujo

El cliente escribe qué necesita y adjunta una imagen de referencia opcional. La administradora responde por escrito y mueve el estado.

`Nuevo` → `En curso` → `Entregado`

- **RF-70.** El cliente crea pedidos con texto obligatorio e imagen opcional (JPG, PNG o WebP, hasta 8 MB).
- **RF-71.** La respuesta de la administradora queda visible para el cliente.
- **RF-72.** Responder un pedido en estado Nuevo lo pasa automáticamente a En curso.
- **RF-73.** El cliente ve su historial completo con los estados.
- **RF-74.** El panel tiene una bandeja global con los pedidos de todas las marcas y contadores por estado.
- **RF-75.** El cliente no puede editar ni borrar un pedido enviado. Si se equivocó, manda otro.

### 10.2 Decisión tomada

No hay estado de aprobación intermedio en la versión 1. Sumar una instancia de revisión formal exige definir qué pasa si el cliente no responde, plazos de aprobación tácita y notificaciones de recordatorio. Se evalúa para la versión 2 con datos de uso real.

---

## 11. Notificaciones

| Evento | Destinatario | Canal |
|---|---|---|
| Pedido nuevo | Administradora | Correo |
| Respuesta a un pedido | Cliente | Correo |
| Nueva fecha en el calendario | Cliente | Correo |
| Métricas del mes importadas | Cliente | Correo |
| Restablecimiento de clave | Quien corresponda | Correo |

- **RF-80.** Todos los correos salen en cola, nunca dentro del pedido web.
- **RF-81.** Cada usuario puede desactivar las notificaciones que no sean de seguridad.
- **RF-82.** Las plantillas usan la identidad visual del sistema y llevan enlace directo a la pantalla correspondiente.

---

## 12. Autenticación y seguridad funcional

- **RF-90.** Ingreso con correo y clave.
- **RF-91.** Límite de cinco intentos por minuto y por dirección IP; luego bloqueo temporal.
- **RF-92.** Recuperación de clave por enlace con vencimiento de 60 minutos.
- **RF-93.** Segundo factor obligatorio para el rol administradora, opcional para clientes.
- **RF-94.** Cierre de sesión por inactividad a las dos horas.
- **RF-95.** Clave mínima de 10 caracteres, verificada contra listas de claves filtradas.
- **RF-96.** Los archivos privados —imágenes de pedidos y archivos finales— se sirven con enlaces firmados de vigencia limitada, nunca por URL pública adivinable.

---

## 13. Idioma, formato y accesibilidad

- Todo el sistema en español rioplatense, con voseo, en registro profesional.
- Fechas en formato `d de mmm de aaaa`; en tablas, `d mmm aaaa`.
- Números con separador de miles con punto y decimales con coma.
- Porcentajes con un decimal.
- Contraste mínimo 4,5:1 en texto.
- Navegación completa por teclado con foco visible.
- Se respeta la preferencia de movimiento reducido del sistema operativo.

---

## 14. Criterios de aceptación del producto

El sistema se considera terminado cuando:

1. Un visitante recorre el portfolio en escritorio y en teléfono y abre cualquier pieza.
2. La administradora carga un video y aparece en el sitio sin intervención técnica.
3. Se crea un cliente, se le entrega el acceso y entra a su portal.
4. Una fecha cargada en el panel aparece en el calendario del cliente.
5. Un archivo JSON con 20 publicaciones se importa y concilia correctamente: crea las nuevas, actualiza las existentes y vincula las planificadas.
6. Reimportar el mismo archivo no genera duplicados.
7. Un cliente autenticado no accede a datos de otro cliente por ninguna vía, verificado con pruebas automatizadas.
8. Un pedido con imagen recorre los tres estados y ambas partes reciben su correo.
9. Todas las pruebas pasan en integración continua.
10. El sistema está en producción, con dominio propio, certificado válido y respaldos verificados.

---

## 15. Fuera de alcance en la versión 1

Facturación y cobros. Firma de presupuestos. Chat interno. Aplicación móvil nativa. Publicación automática en redes. Conexión directa a Meta Graph API desde el sistema. Múltiples idiomas. Portal para proveedores o colaboradores.
