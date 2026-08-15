# ASII-28 - Criterios de aceptación y trazabilidad

## 1. Criterios de aceptación

| ID | Requisito | Dado | Cuando | Entonces |
|---|---|---|---|---|
| CA-01 | RF-01 | Un requisito o riesgo aprobado y datos ficticios disponibles | El Analista QA diseña un caso manual | El caso conserva origen, precondiciones, rol, tenant, datos, pasos y resultado esperado. |
| CA-02 | RF-01 | Un borrador sin requisito, pasos o resultado esperado | El Analista QA intenta validarlo | El borrador se rechaza y no se incorpora como caso ejecutable. |
| CA-03 | RF-02 | Una versión identificada y casos revisados | El Analista QA planifica la campaña | La campaña registra alcance, ambiente, prioridad, versión y casos aplicables. |
| CA-04 | RF-03 | Una campaña con ambiente, versión, tenant, rol y datos disponibles | Se validan sus precondiciones | La campaña queda habilitada para ejecutar casos aplicables. |
| CA-05 | RF-03 | Una campaña con una precondición ausente | Se intenta iniciar la ejecución | Se registra estado `Bloqueado`, la causa y la condición faltante, sin informar éxito. |
| CA-06 | RF-04 | Un caso ejecutable y un contexto válido | `GestorCampana` solicita su ejecución | El caso devuelve un resultado controlado sin requerir comprobaciones de su clase concreta. |
| CA-07 | RF-05 | Un caso ejecutado | El Analista QA registra el resultado | La ejecución conserva estado, versión, ambiente, ejecutor, resultado observado y evidencia identificable. |
| CA-08 | RF-06 | Un resultado diferente con evidencia suficiente | El Analista QA repite las condiciones relevantes y reproduce el fallo | Se crea un defecto vinculado con caso, requisito, versión, evidencia y módulo responsable. |
| CA-09 | RF-06 | Un resultado diferente que no puede reproducirse | El Analista QA analiza el resultado | No se confirma un defecto y se documenta la condición inconclusa para seguimiento. |
| CA-10 | RF-07 | Un defecto y una versión corregida notificada | El Analista QA re-prueba el caso en condiciones equivalentes | Se conserva una nueva evidencia y el defecto se cierra si la corrección funciona. |
| CA-11 | RF-07 | Una re-prueba donde el fallo persiste | Se registra el resultado | El defecto se reabre con la nueva versión y evidencia. |
| CA-12 | RF-08 | Una campaña sin casos aplicables pendientes | El Analista QA solicita el cierre | El resumen presenta cobertura, aprobados, fallidos, bloqueados, no ejecutados, defectos y riesgos. |
| CA-13 | RNF-01, RNF-05 | Un artefacto preparado para revisión | Se inspeccionan datos y evidencias | No existen datos clínicos reales, contraseñas, tokens ni identificadores sensibles. |
| CA-14 | RNF-06 | Un `CasoManual` o `CasoReprueba` válido | Sustituye a otro `CasoRegresionEjecutable` en la misma campaña | Acepta el mismo contexto, devuelve `ResultadoEjecucion` y no altera el algoritmo del gestor. |
| CA-15 | RNF-06 | Un nuevo tipo que cumple `CasoRegresionEjecutable` | Se incorpora a la lista de la campaña | `GestorCampana` lo ejecuta sin modificarse ni agregar condiciones por tipo. |
| CA-16 | RNF-06 | Un `CasoBorrador` incompleto | Se intenta agregar a una campaña | El diseño impide tratarlo como ejecutable porque no implementa el contrato. |

## 2. Matriz de trazabilidad

| Requisito | Caso de uso | Proceso | Criterios | Evidencia esperada |
|---|---|---|---|---|
| RF-01 | CU-01 | P-02 | CA-01, CA-02 | Caso completo o rechazo con campos faltantes. |
| RF-02 | CU-02 | P-01 | CA-03 | Campaña identificada con alcance y versión. |
| RF-03 | CU-03 | P-03 | CA-04, CA-05 | Validación de precondiciones o bloqueo documentado. |
| RF-04 | CU-04 | P-04 | CA-06 | Resultado de ejecución asociado con el caso. |
| RF-05 | CU-05 | P-04 | CA-07 | Registro de ejecución y evidencia identificable. |
| RF-06 | CU-06 | P-05 | CA-08, CA-09 | Defecto trazable o análisis sin confirmación indebida. |
| RF-07 | CU-07 | P-06 | CA-10, CA-11 | Nueva evidencia y defecto cerrado o reabierto. |
| RF-08 | CU-08 | P-07 | CA-12 | Resumen de cobertura, resultados y riesgos. |
| RNF-01, RNF-05 | CU-01, CU-04, CU-05 | P-02, P-03, P-04 | CA-13 | Artefactos con datos ficticios y sin secretos. |
| RNF-02, RNF-03, RNF-04, RNF-08 | CU-04 a CU-08 | P-04 a P-07 | CA-06 a CA-12 | Relación verificable entre requisito, caso, ejecución y evidencia. |
| RNF-06 | CU-04 | P-04 | CA-14, CA-15, CA-16 | Escenarios de sustitución y fuentes del diseño LSP. |
| RNF-07 | CU-04, CU-08 | P-04, P-07 | CA-05, CA-07, CA-12 | Estados consistentes con su significado documentado. |

## 3. Criterio de cierre de la semana 2

La entrega se considera completa cuando los RF/RNF pueden rastrearse a semana 1,
los criterios cubren rutas exitosas y alternas, y los diagramas demuestran que solo
los casos que cumplen el contrato ejecutable participan en una campaña.
