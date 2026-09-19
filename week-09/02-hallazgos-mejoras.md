# Hallazgos y mejoras priorizadas

| ID | Hallazgo | Impacto | Prioridad | Mejora |
|---|---|---|---|---|
| UX-01 | `FAILED` crea defecto y puede sorprender. | Alto | P0 | Confirmacion que enumere los campos que se guardaran. |
| UX-02 | Checksum no es familiar para todos. | Medio | P1 | Ayuda corta y ejemplo, sin explicar secretos. |
| UX-03 | Tres estados dependen inicialmente del color. | Alto | P0 | Etiqueta textual, icono y patron de borde. |
| UX-04 | Tabla de matriz puede desbordar en movil. | Alto | P0 | Convertir cada fila en tarjeta resumida. |
| UX-05 | Sesion vencida puede perder observaciones. | Alto | P1 | Borrador temporal sin evidencia sensible y reautenticacion. |
| UX-06 | Navegacion lateral ocupa espacio en movil. | Medio | P1 | Barra inferior con cuatro destinos prioritarios. |

## Criterio de prioridad

- P0: bloquea tarea, accesibilidad o comprension del efecto.
- P1: afecta eficiencia o recuperacion, pero existe alternativa.
- P2: mejora deseable sin riesgo inmediato.

## Cambios incorporados al prototipo

- Estados siempre incluyen texto.
- Matriz se representa con tarjetas en pantallas estrechas.
- Mensajes usan `aria-live`.
- Navegacion cambia de lateral a inferior.
- Acciones tienen altura tactil minima.
- Ayuda de evidencia aparece junto al campo.
