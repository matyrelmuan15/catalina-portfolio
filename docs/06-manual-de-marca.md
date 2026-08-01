# Manual de marca

**Proyecto:** Portfolio y portal de clientes de Catalina Avendaño
**Versión:** 1.0
**Fecha:** 31 de julio de 2026
**Fuente:** valores extraídos de `mockup/portfolio-mockup.html`

Este documento es la referencia obligatoria para toda decisión visual del sistema. Ante una duda que no esté acá, se resuelve mirando el mockup.

---

## 1. Idea rectora

Una editorial de moda, no un panel de administración.

El sistema muestra trabajo audiovisual para marcas de indumentaria, belleza y gastronomía. Su credibilidad depende de verse cuidado. Tres ideas sostienen todas las decisiones:

- **Aire antes que densidad.** El espacio vacío es parte del diseño, no un desperdicio.
- **El fucsia se gana, no se reparte.** Es el acento; si aparece en todos lados, deja de significar.
- **Sin ornamento decorativo.** Nada de sombras difusas, degradados de fondo, bordes redondeados ni emoticones. La elegancia viene de la tipografía y del espacio.

Esto vale también para el panel y el portal. Son herramientas de trabajo, pero el cliente que entra a ver sus métricas tiene que reconocer la misma marca que vio en el portfolio.

---

## 2. Paleta

| Nombre | Valor | Uso |
|---|---|---|
| Porcelana | `#FBF6F7` | Fondo general. Un blanco apenas rosado |
| Blanco | `#FFFFFF` | Tarjetas, tablas, superficies elevadas |
| Fucsia | `#E4006E` | Acento principal: botones, enlaces, rótulos, datos destacados |
| Fucsia hondo | `#A80352` | Estado hover del fucsia y textos sobre rosa claro |
| Tinta | `#2B0A1B` | Texto principal y fondo de barras laterales. Ciruela muy oscuro, nunca negro puro |
| Malva | `#866A78` | Texto secundario, rótulos apagados, datos de apoyo |
| Rosa humo | `#F6DCE6` | Fondos suaves, insignias, resaltados |
| Línea | `#EADCE3` | Bordes y separadores |

> **Nota sobre malva.** El valor original extraído del mockup era `#9A7F8C`, que da 3,39:1 sobre porcelana y no cumple el mínimo de 4,5:1 que fija este mismo documento más abajo. Se oscureció conservando matiz y saturación hasta `#866A78` (4,52:1 sobre porcelana, 4,83:1 sobre blanco). El cálculo completo y el estado de **aprobación visual pendiente** están en la entrada del 1 de agosto de 2026 de `AVANCE.md`, "Contraste de malva, CI en verde y decisión sobre PHP 8.4".

### Reglas de uso

- **El fondo por defecto es porcelana, no blanco.** El blanco puro se reserva para superficies que se apoyan encima.
- **Nunca negro puro.** El texto es tinta. Un negro absoluto rompe la calidez del conjunto.
- El fucsia como fondo se usa en una sola sección por pantalla, como máximo. En el portfolio, esa sección es contacto.
- Sobre fucsia, el texto va en blanco. Sobre rosa humo, en fucsia hondo.
- Los bordes son de un píxel, color línea. No hay bordes gruesos.
- Contraste mínimo 4,5:1 en todo texto.

### No hay colores semánticos de sistema

No se usan verde para éxito ni rojo para error. Los estados se distinguen por intensidad dentro de la misma familia:

| Estado | Tratamiento |
|---|---|
| Neutro, inactivo | Borde línea, texto malva |
| En curso, atención | Borde fucsia, texto fucsia |
| Completado, activo | Fondo rosa humo, texto fucsia hondo |
| Destacado, nuevo | Fondo fucsia, texto blanco |

---

## 3. Tipografía

Dos familias, sin excepciones.

### Bodoni Moda — títulos

Serif didone de alto contraste. Es la voz de revista de moda del sistema.

- Pesos: 400 y 500. **Nunca negrita.**
- Interlineado ajustado: entre 0,86 y 1,25 según el tamaño.
- Espaciado entre letras negativo en tamaños grandes: `-0.02em` a `-0.03em`.
- La cursiva se usa como acento expresivo en una o dos palabras de un título, generalmente en fucsia.

**Dónde va:** títulos de sección, títulos de pantalla, cifras grandes de métricas, nombre de la marca.

### Jost — texto e interfaz

Sans geométrica. Sostiene todo lo demás.

- Pesos: 300 para texto corrido, 400 para datos, 500 para rótulos y botones, 600 solo si hace falta.
- El texto corrido va en 300. Es la elección que más define el carácter del conjunto.

**Dónde va:** párrafos, formularios, tablas, botones, navegación, rótulos.

### Escala

| Elemento | Familia | Tamaño | Detalle |
|---|---|---|---|
| Título de portada | Bodoni | `clamp(3.4rem, 8vw, 6.6rem)` | Interlineado 0.86 |
| Título de sección | Bodoni | `clamp(2.2rem, 4.4vw, 3.4rem)` | Interlineado 1 |
| Título de pantalla | Bodoni | 36 px | Panel y portal |
| Título de ventana | Bodoni | 30 px | |
| Cifra de métrica | Bodoni | 34 a 44 px | |
| Texto corrido | Jost 300 | 15 a 17 px | Interlineado 1.6 |
| Texto de tabla | Jost 300 | 13 a 14 px | |
| Rótulo | Jost 500 | 10 px | Versalitas, `letter-spacing: .24em` a `.3em` |
| Botón | Jost 500 | 11 px | Versalitas, `letter-spacing: .22em` |

### El rótulo en versalitas espaciadas

Es el recurso tipográfico distintivo del sistema. Texto muy chico, en mayúsculas, con espaciado amplio entre letras. Aparece sobre cada título de sección, en los encabezados de tabla, en las etiquetas de formulario y en los botones.

```css
font-size: 10px;
letter-spacing: .3em;
text-transform: uppercase;
font-weight: 500;
color: var(--fucsia);   /* o var(--malva) cuando es secundario */
```

Usarlo mal —en tamaños grandes o sin espaciado— desarma el carácter de la marca.

---

## 4. Formas y espacio

### Sin esquinas redondeadas

`border-radius: 0` en todo el sistema: botones, tarjetas, campos, tablas e insignias.

Las dos únicas excepciones: el círculo del día actual en el calendario y el botón circular de reproducción sobre las piezas.

### Sin sombras difusas

No se usan sombras para separar elementos. La jerarquía se construye con bordes de un píxel, con contraste de fondo entre porcelana y blanco, y con espacio.

Única excepción: la barra flotante de prueba del mockup, que no forma parte del producto.

### Ritmo de espaciado

Múltiplos de 4 píxeles, con estos valores de referencia:

| Contexto | Valor |
|---|---|
| Interior de campos y botones | 13 a 14 px vertical, 14 a 26 px horizontal |
| Entre campos de formulario | 18 px |
| Celdas de tabla | 14 px |
| Interior de tarjetas y ventanas | 38 a 40 px |
| Secciones del portfolio | 110 px vertical, 32 px horizontal |
| Ancho máximo de contenido | 1180 px |

**El espacio generoso no es negociable.** Comprimir para que entre más información en pantalla es la forma más rápida de que el sistema deje de parecerse a su marca.

---

## 5. Componentes

### Botones

| Variante | Fondo | Texto | Uso |
|---|---|---|---|
| Principal | Fucsia | Blanco | Acción primaria. Una por pantalla |
| Fantasma | Transparente, borde tinta | Tinta | Acciones secundarias. Invierte al pasar el cursor |
| Claro | Blanco | Fucsia | Sobre fondo fucsia |
| Chico | Igual, con 10 px de texto | | Dentro de tablas y barras |

Siempre en versalitas espaciadas. Nunca con ícono a la izquierda, salvo el `+` de las acciones de alta.

### Campos de formulario

Fondo porcelana, borde línea, sin redondeo. Al recibir el foco: fondo blanco y borde fucsia. La etiqueta va arriba, en rótulo malva.

### Tablas

Encabezados en rótulo malva sobre porcelana. Filas separadas por línea de un píxel. Al pasar el cursor, la fila se tiñe de porcelana. Los números van alineados a la derecha con cifras tabulares.

### Insignias

Rectángulo con borde de un píxel, texto de 9 px en versalitas espaciadas. La intensidad indica el estado, según la tabla de la sección 2.

### Íconos

Cuadrados de 32 píxeles con borde línea, con un carácter tipográfico adentro: `✎` editar, `✕` eliminar, `★` destacar, `↗` abrir. Al pasar el cursor, borde y texto en fucsia.

**No se usan librerías de íconos ni emoticones**, en ninguna parte del sistema.

### Miniaturas y piezas

Relación 9:16 siempre. Es el formato del contenido que produce Catalina y es un rasgo de identidad, no una decisión técnica. Sobre la imagen va un velo degradado de tinta desde abajo, para que el texto se lea.

---

## 6. Movimiento

Discreto y con propósito.

| Transición | Duración |
|---|---|
| Color de fondo y borde | 200 a 250 ms |
| Transformaciones y escala | 350 a 600 ms |
| Aparición de ventanas | 250 ms |

Curva de referencia: `cubic-bezier(.2, .7, .2, 1)`.

La animación de la tira de piezas de la portada corre en 34 segundos por ciclo. Tiene que leerse como deriva, no como carrusel.

**Toda animación respeta `prefers-reduced-motion`.** Si el sistema operativo pide movimiento reducido, se desactiva.

---

## 7. Voz y redacción

- **Español rioplatense con voseo**, registro profesional. "Contame qué necesitás", no "Cuéntame lo que necesita".
- **Frases cortas y directas.** Sin relleno ni entusiasmo impostado.
- **Sin signos de exclamación** en la interfaz.
- **Sin emoticones**, en ningún lugar del sistema ni en los correos.
- Los mensajes de error dicen qué pasó y qué hacer: "La clave no coincide. Volvé a intentarlo", no "Error de autenticación".
- Los estados vacíos explican, no se disculpan: "Todavía no hay publicaciones cargadas".
- Los rótulos de interfaz van en singular y sin artículo: "Alcance", "Fecha", "Cliente".

### Formatos

| Dato | Formato |
|---|---|
| Fecha larga | 31 de julio de 2026 |
| Fecha en tabla | 31 jul 2026 |
| Número | 184.200 (miles con punto) |
| Decimal | 12,4 (coma) |
| Porcentaje | 18,5 % (con espacio antes del signo) |
| Dato ausente | — (guion largo) |

**Un dato que falta se muestra con guion, nunca con cero.** El cero es un resultado; la ausencia de dato no lo es.

---

## 8. Tokens para código

```css
:root{
  /* color */
  --porcelana:   #FBF6F7;
  --blanco:      #FFFFFF;
  --fucsia:      #E4006E;
  --fucsia-hondo:#A80352;
  --tinta:       #2B0A1B;
  --malva:       #866A78;
  --rosa-humo:   #F6DCE6;
  --linea:       #EADCE3;

  /* tipografía */
  --display: 'Bodoni Moda', 'Didot', Georgia, serif;
  --texto:   'Jost', 'Century Gothic', system-ui, -apple-system, sans-serif;

  /* forma */
  --radio: 0;
  --borde: 1px solid var(--linea);

  /* movimiento */
  --curva: cubic-bezier(.2,.7,.2,1);
  --rapido: .2s;
  --medio: .35s;
  --lento: .6s;
}
```

Tipografías desde Google Fonts:

```html
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
```

En Tailwind 4, estos tokens se declaran en `@theme` y se usan por nombre. No se escriben valores hexadecimales sueltos en las plantillas.

---

## 9. Lista de verificación

Antes de dar por buena una pantalla:

- [ ] El fondo es porcelana, no blanco puro
- [ ] Ningún texto usa negro puro
- [ ] El fucsia aparece como acento, no como fondo generalizado
- [ ] Los títulos están en Bodoni Moda, sin negrita
- [ ] El texto corrido está en Jost 300
- [ ] Los rótulos van en versalitas con espaciado amplio
- [ ] No hay esquinas redondeadas
- [ ] No hay sombras difusas
- [ ] No hay emoticones ni íconos de librería
- [ ] Los datos ausentes muestran guion, no cero
- [ ] Los números usan formato argentino
- [ ] Los textos están en voseo, sin exclamaciones
- [ ] Las animaciones respetan `prefers-reduced-motion`
- [ ] Funciona desde 360 píxeles de ancho
