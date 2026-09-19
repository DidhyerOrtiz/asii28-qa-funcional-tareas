# Estrategia responsive y escenarios moviles

## 1. Breakpoints por contenido

| Rango | Adaptacion |
|---|---|
| `>= 1100px` | Sidebar, dos columnas y panel de actividad. |
| `700px - 1099px` | Sidebar compacta, tarjetas en una columna. |
| `< 700px` | Encabezado compacto, barra inferior y contenido apilado. |

Los breakpoints responden al espacio necesario para leer el caso y completar el
formulario, no a un modelo especifico de telefono.

## 2. Prioridades moviles

1. Hospital, campana y caso actual.
2. Pasos y resultado esperado.
3. Estado y resultado observado.
4. Evidencia y campos condicionales.
5. Accion guardar.
6. Actividad secundaria y metricas.

## 3. Escenarios

### Ejecutar caso desde tablet

El analista consulta pasos a la izquierda y registra resultado a la derecha. En
orientacion vertical los paneles se apilan sin cambiar el orden logico.

### Revisar bloqueo desde telefono

La tarjeta muestra `BLOCKED`, causa, version y accion para volver a la campana.
No se obliga a usar una tabla horizontal.

### Consultar defectos en movilidad

Cada defecto presenta severidad, estado, requisito y version. Los detalles se
expanden bajo demanda para reducir carga visual.

### Conexion inestable

El prototipo comunica el error y conserva texto no sensible. La implementacion
real no debe almacenar tokens ni archivos de evidencia en almacenamiento local.

## 4. Reglas tecnicas

- Unidades relativas para tipografia y espacios.
- Grid flexible con `minmax` y tarjetas.
- Controles de al menos 44 px.
- Sin desplazamiento horizontal en 320 CSS px.
- Zoom a 200% sin perdida de contenido.
- Navegacion usable por teclado y lector de pantalla.
- Movimiento reducido cuando el sistema lo solicita.
