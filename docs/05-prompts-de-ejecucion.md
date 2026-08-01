# Prompts de ejecución rápida

Para construir el sistema con Claude Code en la menor cantidad de sesiones posible, agrupando fases en cuatro lotes.

**Versión:** 1.1 · 1 de agosto de 2026

**Antes de empezar:** el repositorio tiene que existir con la documentación adentro (`README.md`, `AVANCE.md` y los seis de `docs/`), más el mockup en `mockup/`. Los prompts se apoyan en esos archivos; sin ellos, el resultado va a ser genérico.

**Entorno de desarrollo:** Herd para PHP, PostgreSQL 16 nativo y Redis. No hay Docker ni WSL2, y el proyecto no lleva `Dockerfile` ni `docker-compose.yml`: Railway despliega con Nixpacks.

---

## Cómo agrupar

| Lote | Fases | Riesgo | Supervisión |
|---|---|---|---|
| A | 0, 1, 2, 3 | Bajo | Revisar al final |
| B | 4, 5, 6 | Medio | Revisar al final |
| C | 7 | **Alto** | Revisar durante |
| D | 8, 9, 10 | Medio | Revisar al final |

**El lote C va solo y a propósito.** La importación con conciliación es la parte con más reglas y donde un error silencioso corrompe datos sin avisar. Es la única que conviene no dejar correr sin mirar.

**Modelos.** Lotes A, B y D con Sonnet 5: es scaffolding, formularios y tablas, y rinde bien. Lote C con Opus 5 o Fable 5, que es lógica de conciliación con casos borde. Fable 5 acelera los lotes largos, pero no cambia el criterio: lo que hay que revisar sigue siendo lo mismo.

---

## Prompt del lote A — Fases 0 a 3

```
Vas a construir un sistema web completo siguiendo la documentación de este repositorio.

LEÉ PRIMERO, EN ESTE ORDEN:
1. README.md — cómo se trabaja en este proyecto
2. docs/01-especificacion-funcional.md
3. docs/02-arquitectura-y-datos.md
4. docs/04-plan-de-fases.md
5. mockup/portfolio-mockup.html — referencia visual y de comportamiento

TAREA: implementá las fases 0, 1, 2 y 3 completas, de corrido.

PUNTOS DE ATENCIÓN DE ESTE LOTE:
- El entorno local es Herd + PostgreSQL 16 nativo + Redis. NO hay Docker ni
  WSL2. No generes Dockerfile ni docker-compose.yml. Railway despliega con
  detección automática (Nixpacks).
- Fijá config.platform.php en composer.json con la versión de PHP de
  producción. Sin ese pin, el composer.lock se resuelve contra el PHP local
  y puede no instalarse en el servidor.
- Las pruebas corren contra PostgreSQL, NUNCA contra SQLite. Hay que revisar
  DOS lugares, no uno: que phpunit.xml no fije DB_CONNECTION=sqlite, y que el
  workflow de GitHub Actions no defina una variable de entorno a nivel job que
  pise a phpunit.xml. Corregir uno solo deja el problema intacto.
- Agregá una prueba que verifique que el driver activo es pgsql y falle si no.
- Todo buscador usa whereLike(..., caseSensitive: false), nunca
  where('columna', 'like', ...). PostgreSQL distingue mayúsculas en LIKE.
- Las columnas de texto que se ordenan llevan collation es-AR-x-icu declarada
  en la migración. Nada de collations no deterministas: rompen LIKE e ILIKE
  en PostgreSQL 16.
- Leé la sección 3.3 de docs/02 antes de escribir migraciones o buscadores.
  Las tres reglas de ahí no son preferencias de estilo.

REGLAS QUE NO SE NEGOCIAN:
- El stack es el del documento 02. No lo cambies ni propongas alternativas.
- El diseño sale del mockup: paleta, tipografías, espaciados y comportamiento
  de la cabecera, la grilla y los filtros. No inventes una estética nueva.
- Todo en español rioplatense con voseo, registro profesional. Esto incluye
  interfaz, mensajes de error, comentarios del código y nombres de rutas.
- El aislamiento entre clientes de la sección 6.1 del documento 02 se implementa
  en las tres capas desde el primer día, con su batería de pruebas. No lo dejes
  para después.
- Cada modelo nuevo con cliente_id suma su prueba de aislamiento en el mismo commit.
- Pruebas con Pest. Cobertura mínima 70%, y 100% en las pruebas de aislamiento.
- Migraciones que puedan aplicarse sobre una base en producción sin bloquearla.

CRITERIOS DE ACEPTACIÓN: los de cada fase en docs/04-plan-de-fases.md.
No des una fase por cerrada hasta cumplirlos todos.

ORDEN DE TRABAJO:
1. Fase 0 completa, con las pruebas corriendo contra PostgreSQL y la
   integración continua en verde.
2. Fase 1 completa, con la batería de aislamiento pasando.
3. Fase 2 completa.
4. Fase 3 completa.
5. Actualizá AVANCE.md con una entrada por fase, siguiendo la plantilla del final
   del archivo. Actualizá también la tabla de progreso y la de decisiones.

AL TERMINAR, INFORMAME:
- Qué quedó implementado y qué no.
- Qué decisiones tomaste que no estaban en la documentación, y por qué.
- Qué está roto, incompleto o pendiente. Sin maquillar.
- Los comandos exactos para levantarlo en local y probarlo.

Si algo de la documentación es ambiguo o contradictorio, no adivines:
frená y preguntame antes de seguir.
```

---

## Prompt del lote B — Fases 4 a 6

```
Continuás el sistema de este repositorio. Las fases 0 a 3 ya están implementadas.

LEÉ PRIMERO: AVANCE.md, para saber en qué estado quedó todo y qué decisiones
se tomaron. Después revisá docs/01, docs/02 y docs/04 en lo que corresponde
a las fases 4, 5 y 6, y el mockup para la parte visual.

TAREA: implementá las fases 4, 5 y 6 completas.

PUNTOS DE ATENCIÓN:
- Fase 4: el alta de cliente crea el cliente y su usuario en UNA transacción.
  Si falla el usuario, no queda el cliente huérfano.
- Fase 5: las fechas se guardan como date, sin hora, para evitar corrimientos
  por zona horaria. Zona del sistema: America/Argentina/Buenos_Aires.
- Fase 6: la tabla de publicaciones tiene 25 columnas con desplazamiento
  horizontal y primera columna fija, como en el mockup. Tiene que responder
  bien con 200 registros cargados.
- Fase 6: las publicaciones aparecen solas en el calendario, en su fecha.
  No son eventos de agenda.
- El cliente ve un subconjunto de columnas. Revisá RF-44 en docs/01.

REGLAS: las mismas del lote anterior. Español rioplatense, diseño del mockup,
aislamiento con pruebas en cada modelo nuevo, criterios de aceptación de docs/04.

AL TERMINAR: actualizá AVANCE.md e informame lo hecho, lo decidido y lo pendiente.
```

---

## Prompt del lote C — Fase 7

Este es el crítico. Va con instrucción explícita de escribir las pruebas primero.

```
Continuás el sistema de este repositorio. Las fases 0 a 6 están implementadas.

LEÉ PRIMERO: AVANCE.md, docs/03-formato-metricas.md completo, y las secciones
8 de docs/01 y 4 de docs/02.

TAREA: implementá la fase 7, importación de métricas con conciliación.

ESTA FASE SE HACE CON LAS PRUEBAS PRIMERO. Antes de escribir el servicio:

1. Armá archivos JSON de ejemplo en tests/fixtures/ que cubran estos casos:
   - Archivo válido con resumen y 20 publicaciones
   - Archivo con solo publicaciones, sin resumen
   - Archivo con solo resumen, sin publicaciones
   - JSON malformado
   - Falta el campo periodo
   - resumen.alcance viene como texto "42K" en vez de número
   - El campo cliente no coincide con la marca donde se importa
   - Publicaciones sin id_media pero con permalink
   - Permalinks con parámetros ?igshid= y ?utm_source=
   - Dos publicaciones planificadas en la misma fecha y plataforma
   - Publicación con clics_enlace en null
   - El mismo archivo importado dos veces seguidas

2. Escribí las pruebas de cada caso, con el resultado esperado.

3. Recién ahí implementá ImportadorMetricas y ConciliadorPublicaciones
   hasta que pasen todas.

REGLAS DE LA CONCILIACIÓN, en este orden exacto:
1. Por id_media. Es el identificador de Meta y no cambia nunca.
2. Por permalink NORMALIZADO: sin esquema, sin www, sin barra final y sin
   ningún parámetro de consulta. Sin este paso cada importación duplica todo.
3. Por fecha y plataforma, SOLO contra publicaciones sin permalink. Si hay
   exactamente una candidata, se vincula. Si hay dos o más, no adivines:
   creá una nueva.

REGLAS QUE NO SE NEGOCIAN:
- La importación es una transacción: se aplica entera o no se aplica nada.
- Es idempotente: reimportar el mismo archivo no duplica.
- NUNCA se sobrescriben los campos propios: titulo, estado, pilar,
  archivo_final_url y creativo_figma_url.
- Un campo sin dato queda en null. Está PROHIBIDO rellenar con cero:
  Meta no entrega clics por pieza en publicaciones orgánicas de feed,
  y un cero se lee como un resultado.
- El archivo original se guarda completo en la tabla importaciones.
- Los mensajes de error son en español, concretos y accionables: tienen que
  decir qué campo falla y qué se espera.
- Cobertura 100% en ambos servicios.

AL TERMINAR: actualizá AVANCE.md y mostrame la salida de las pruebas.
Detallá qué casos borde cubriste y cuáles decidiste no cubrir, con el motivo.
```

---

## Prompt del lote D — Fases 8 a 10

```
Continuás el sistema de este repositorio. Las fases 0 a 7 están implementadas.

LEÉ PRIMERO: AVANCE.md y las fases 8, 9 y 10 de docs/04-plan-de-fases.md.

TAREA: implementá las fases 8, 9 y 10 completas.

PUNTOS DE ATENCIÓN:
- Fase 8: si no hay datos del período, mostrá un estado vacío explícito.
  Nunca ceros. Las columnas sin dato llevan guion.
- Fase 8: formato argentino. Miles con punto, decimales con coma.
- Fase 9: validá el tipo REAL de la imagen, no la extensión del archivo.
- Fase 9: los correos van todos en cola, ninguno dentro del pedido web.
- Fase 9: las imágenes de pedidos se sirven con enlaces firmados de 15 minutos,
  nunca por URL pública adivinable.
- Fase 10: verificá explícitamente que las reglas de caché de Cloudflare
  excluyen /panel y /portal. Cachear una respuesta del portal sería servirle
  a un cliente los datos de otro.

Además, en la fase 10:
- Corré la batería de aislamiento completa y mostrame el resultado.
- Escribí el manual de uso para Catalina en docs/05-manual-de-uso.md, en
  lenguaje simple, sin vocabulario técnico, explicando cómo cargar un video,
  crear un cliente, agendar una fecha, importar el JSON de métricas y
  responder un pedido.

AL TERMINAR: actualizá AVANCE.md y dame la lista de lo que falta para
poder salir a producción.
```

---

## Prompt de recuperación

Si una sesión se corta o el contexto se llena, esto retoma sin repetir trabajo:

```
Retomás el desarrollo de este repositorio.

Leé AVANCE.md para saber en qué punto está el proyecto, qué se decidió y
qué quedó pendiente. Después revisá el estado real del código: qué migraciones
existen, qué pruebas pasan y qué fases están efectivamente terminadas.

Decime qué encontraste y cuál es el próximo paso concreto según
docs/04-plan-de-fases.md. No empieces a escribir código hasta que
confirme el diagnóstico.
```

---

## Qué revisar sí o sí

Aunque delegues el resto, mirá con tus propios ojos:

1. **La batería de aislamiento entre clientes.** Que exista, que corra y que efectivamente intente alcanzar datos de otra marca. Es la falla más cara del sistema.
2. **La normalización del permalink.** Probá importar dos veces el mismo archivo, con permalinks con y sin `?igshid=`. Si aparecen duplicados, la conciliación está mal.
3. **Que no haya ceros donde falta el dato.** Sobre todo en clics al enlace.
4. **Las reglas de caché de `/panel` y `/portal`.**
5. **Que `AVANCE.md` esté actualizado de verdad**, y no con una entrada genérica escrita al pasar.

---

## Una advertencia sobre el apuro

Los cuatro lotes comprimen bastante, pero no convierten cincuenta días en dos tardes. Lo que se acelera es la escritura de código; lo que no se acelera es la verificación de que el código hace lo correcto.

Si el tiempo aprieta de verdad, el recorte más sensato no es saltear revisiones sino **cortar alcance**: hacé los lotes A y B, publicá el portfolio y el calendario, y dejá la importación de métricas para más adelante. Un portfolio en línea con fechas que funcionan vale más que un sistema completo con números en los que no confiás.
