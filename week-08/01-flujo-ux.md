# Flujo UX y reglas de interaccion

## 1. Flujo principal del Analista QA

1. Entra al resumen y confirma hospital y version objetivo.
2. Selecciona una campana `En curso`.
3. Revisa progreso, bloqueos y siguiente caso pendiente.
4. Abre el caso y consulta precondiciones, pasos y resultado esperado.
5. Ejecuta manualmente el flujo en el HIS bajo prueba.
6. Selecciona `PASSED`, `FAILED` o `BLOCKED`.
7. Describe el resultado observado y agrega referencia de evidencia saneada.
8. Si falla, completa reproduccion, severidad y version detectada.
9. Confirma el registro y avanza al siguiente caso.
10. Consulta la matriz y defectos resultantes.

## 2. Flujos por rol

| Rol | Entrada | Acciones | Salida |
|---|---|---|---|
| Analista QA | Campana asignada | Ejecutar, evidenciar, crear defecto y re-probar. | Matriz actualizada. |
| Revisor | Campana con resultados | Consultar cobertura, defectos y bloqueos. | Observaciones de revision. |
| Responsable | Defecto reproducible | Consultar contexto y comunicar version corregida. | Re-prueba habilitada. |

## 3. Estados visuales

| Estado | Tratamiento |
|---|---|
| Cargando | Esqueleto breve y texto `Cargando campana`. |
| Vacio | Explicar por que no hay casos y ofrecer regreso. |
| Sin permiso | Mensaje 403 sin revelar recursos. |
| No encontrado | Mensaje neutral 404. |
| Conflicto | Explicar que el defecto ya fue cerrado o el registro cambio. |
| Validacion | Error junto al campo y resumen al inicio. |
| Sin conexion | Conservar borrador local no sensible y permitir reintento. |
| Exito | Confirmacion con ID y siguiente accion. |

## 4. Reglas de interaccion

- No usar solo color para estados; combinar etiqueta e icono.
- Mantener visible hospital, campana y version durante la ejecucion.
- Deshabilitar envio mientras falten campos, pero mostrar la causa.
- Pedir confirmacion antes de registrar `FAILED` y crear defecto.
- No mostrar rutas internas completas como enlaces publicos.
- Conservar foco en el mensaje de resultado despues de enviar.
- Permitir teclado completo y foco visible.
- No pedir datos que el servidor deriva, como `hospital_id`.
