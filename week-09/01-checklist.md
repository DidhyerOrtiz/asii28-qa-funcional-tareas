# Checklist de usabilidad y accesibilidad

## Usabilidad

| Criterio | Estado | Evidencia o accion |
|---|---|---|
| Contexto de hospital visible | Cumple | Encabezado persistente. |
| Progreso de campana visible | Cumple | Porcentaje y caso actual. |
| Accion primaria clara | Cumple | `Continuar` / `Guardar resultado`. |
| Estados de carga/vacio/error | Cumple | Definidos en semana 8. |
| Prevencion de errores | Parcial | Falta confirmacion visual final para `FAILED`. |
| Recuperacion tras error | Cumple | Datos del formulario se conservan. |
| Consistencia de etiquetas | Cumple | Estados del dominio sin sinonimos. |
| Ayuda contextual | Parcial | Falta explicar checksum y redaccion. |

## Accesibilidad

| Criterio | Estado | Evidencia o accion |
|---|---|---|
| Navegacion por teclado | Cumple en prototipo | Botones y enlaces nativos. |
| Foco visible | Cumple | Contorno de alto contraste. |
| Orden de foco logico | Cumple | DOM sigue lectura visual. |
| Etiquetas asociadas | Cumple | `label` y `fieldset`. |
| No depender solo del color | Cumple | Texto e icono/estado. |
| Contraste AA | A verificar | Medir paleta final con herramienta. |
| Texto adaptable a 200% | Cumple por diseno | Unidades relativas y reflow. |
| Objetivos tactiles | Cumple | Minimo 44 px. |
| Mensajes anunciables | Cumple en prototipo | Region `aria-live`. |
| Reduccion de movimiento | Cumple | `prefers-reduced-motion`. |

## Privacidad

- Los mockups contienen datos ficticios.
- No se muestran tokens ni cabeceras.
- La evidencia se representa como referencia privada.
- No se usan nombres o identificadores de pacientes.
