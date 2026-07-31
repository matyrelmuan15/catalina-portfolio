# Formato del archivo de métricas

**Versión del formato:** 2
**Última actualización:** 31 de julio de 2026

Define el contrato entre lo que devuelve Claude y lo que el sistema sabe leer. Tiene dos partes: el **resumen del período** y el **detalle por publicación**. Se pueden mandar juntas o por separado.

---

## 1. De dónde sale cada columna

No todas las columnas vienen de Meta. Hay tres grupos, y la diferencia importa porque define qué se completa solo y qué hay que cargar a mano.

### Grupo A — Vienen directo de Meta Graph API

| Columna | Campo de la API |
|---|---|
| ID Media (Meta) | `id` |
| Permalink | `permalink` |
| Fecha | `timestamp` |
| Formato | `media_type` + `media_product_type` |
| Copy / Caption | `caption` |
| Me gusta | `like_count` |
| Comentarios | `comments_count` |
| Alcance | insight `reach` |
| Vistas | insight `views` |
| Compartidos | insight `shares` |
| Guardados | insight `saved` |
| Interacciones | insight `total_interactions` |

### Grupo B — Se calculan o se derivan

| Columna | Cómo se obtiene |
|---|---|
| Hashtags | Se extraen del `caption` |
| Tasa interacción % | Interacciones ÷ alcance × 100 |
| Plataforma | Según la cuenta consultada |
| Medido el | Momento en que se corrió la consulta |

### Grupo C — No las da Meta: se cargan en el sistema

| Columna | Motivo |
|---|---|
| Publicación (título interno) | Es tu nomenclatura, no la de Meta |
| Estado | Planificada, En producción, Aprobada, Publicada, Medida |
| Pilar | Clasificación editorial propia |
| Cliente y Cliente (texto) | Relación interna |
| Archivo final | Enlace a Drive |
| Creativo (Figma) | Enlace al diseño |

### Dos advertencias

**Clics al enlace.** Para publicaciones orgánicas de feed en Instagram, Meta no expone clics por pieza. Solo hay dato en historias con sticker de enlace y en publicaciones de Facebook (`post_clicks_by_type`). En la mayoría de los casos esta columna va a quedar vacía, y está bien que así sea: es preferible el campo vacío antes que un cero que parezca un resultado.

**Seguidores al publicar.** Meta tampoco lo entrega por publicación; solo da el total actual de la cuenta. Hay dos formas de tenerlo: guardar el total de seguidores cada vez que se mide (aproximación razonable), o llevar un registro diario de seguidores y cruzarlo por fecha (exacto, pero requiere una consulta programada). Para arrancar alcanza con la primera.

---

## 2. Estructura del archivo

```json
{
  "version": 2,
  "cliente": "bloom",
  "periodo": "2026-07",
  "actualizado": "2026-07-31 08:15",
  "origen": "Meta Graph API",

  "resumen": {
    "alcance": 184200,
    "interacciones": 12400,
    "seguidores_nuevos": 1240,
    "piezas_publicadas": 14
  },
  "variacion": {
    "alcance": 18,
    "interacciones": 9
  },
  "serie_mensual": [
    { "mes": "2026-06", "alcance": 96000 },
    { "mes": "2026-07", "alcance": 112000 }
  ],

  "publicaciones": [
    {
      "id_media": "17912345678901234",
      "permalink": "https://www.instagram.com/p/C9aBcDeFgH1/",
      "titulo": "Rutina de mañana",
      "fecha": "2026-07-09",
      "plataforma": "Instagram",
      "formato": "Reel",
      "copy": "Tres pasos y listo. El sérum de noche se absorbe en segundos",
      "hashtags": "#skincare #rutinadenoche #serum",
      "metricas": {
        "alcance": 42000,
        "vistas": 51300,
        "interacciones": 3100,
        "me_gusta": 2410,
        "comentarios": 118,
        "compartidos": 342,
        "guardados": 230,
        "clics_enlace": null,
        "seguidores_al_publicar": 18420,
        "medido_el": "2026-07-31"
      }
    }
  ]
}
```

### Reglas

- Números enteros, sin separadores de miles ni sufijos: `184200`, nunca `"184.2K"`.
- Porcentajes como número, sin símbolo: `18`, no `"18%"`.
- Fechas en formato `AAAA-MM-DD`; períodos en `AAAA-MM`.
- Si un dato no está disponible, es preferible `null` u omitir el campo antes que mandar `0`.
- `tasa_interaccion` es opcional: si no viene, el sistema la calcula.
- Se puede mandar solo `publicaciones`, sin `resumen`, para actualizar el detalle sin tocar el resumen del mes.

---

## 3. Cómo concilia el sistema

Al importar, cada publicación del archivo se busca contra las que ya existen, en este orden:

1. **Por ID Media.** Es el identificador de Meta y no cambia nunca. Es la coincidencia más confiable.
2. **Por permalink.** Si no hubo ID, se compara la URL exacta.
3. **Por fecha y plataforma.** Solo se aplica a publicaciones que todavía no tienen permalink, es decir, las que vos cargaste como planificadas en el calendario. Si hay exactamente una candidata en esa fecha, se vincula; si hay más de una, no adivina y crea una nueva.

Si no encuentra coincidencia, **crea la publicación** y esta aparece automáticamente en el calendario del cliente, en su fecha.

Cuando la coincidencia se encuentra, el sistema actualiza los datos que vienen de Meta y **conserva los del grupo C**: título interno, pilar, archivo final y creativo en Figma no se pisan nunca.

El resultado de cada importación se informa en pantalla: cuántas se crearon, cuántas se actualizaron y cuántas se vincularon con una publicación planificada.

---

## 4. Prompt sugerido para pedirle el archivo a Claude

> Traeme por Meta Graph API las publicaciones de Instagram de **[MARCA]** del período **[MES AAAA]**.
>
> Por cada publicación necesito: `id`, `permalink`, `timestamp`, `media_type` y `media_product_type`, `caption`, `like_count`, `comments_count`, y los insights `reach`, `views`, `shares`, `saved` y `total_interactions`. Sumá también el total de seguidores de la cuenta al momento de la consulta.
>
> Después armá un resumen del período con alcance total, interacciones totales, seguidores nuevos y cantidad de piezas publicadas, más la variación porcentual contra el mes anterior y la serie de alcance de los últimos seis meses.
>
> Devolvémelo como un único archivo JSON con esta estructura exacta, sin texto alrededor:
>
> [pegar acá el bloque de la sección 2]
>
> Reglas: el campo `cliente` tiene que decir `[identificador]`. Todos los números enteros y sin separadores de miles. Los hashtags extraelos del caption. El `formato` traducilo a Reel, Carrusel, Imagen, Historia o Video. Si un dato no está disponible, poné `null`.

---

## 5. Errores frecuentes

| Mensaje | Causa | Solución |
|---|---|---|
| El archivo no es JSON válido | Quedó texto explicativo fuera de las llaves | Dejar solo el JSON |
| Faltan campos o no son números | Vinieron como `"42K"` o como texto | Pedir enteros |
| El archivo dice cliente X y estás importando en Y | Ficha equivocada o identificador mal escrito | Verificar antes de forzar |
| Se duplicaron publicaciones | Faltaba `id_media` y el permalink venía con parámetros de seguimiento | Pedir el permalink limpio, sin `?igshid=` ni similares |
