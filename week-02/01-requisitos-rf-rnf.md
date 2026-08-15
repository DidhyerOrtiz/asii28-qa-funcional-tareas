# ASII-28 - Requisitos funcionales y no funcionales

## 1. Criterios de redacción

Los requisitos se derivan de los casos de uso `CU-01` a `CU-08` y de los procesos
`P-01` a `P-07` documentados en la semana 1. El sujeto funcional es el proceso de QA
manual del HIS; las clases usadas en el ejemplo LSP son una propuesta de diseño y no
una implementación existente.

## 2. Requisitos funcionales

| ID | Requisito | Origen | Verificación esperada |
|---|---|---|---|
| RF-01 | El módulo debe permitir diseñar un caso manual vinculado con un requisito, criterio de aceptación o riesgo. | `CU-01`, `P-02` | El caso conserva origen, precondiciones, datos ficticios, pasos y resultado esperado. |
| RF-02 | El módulo debe permitir planificar una campaña de regresión con alcance, versión, ambiente, prioridad y casos aplicables. | `CU-02`, `P-01` | La campaña puede identificarse y contiene las condiciones necesarias para su ejecución. |
| RF-03 | El módulo debe validar ambiente, versión, tenant, rol, dependencias y datos antes de ejecutar un caso. | `CU-03`, `P-03` | La ejecución inicia solo con precondiciones completas; de lo contrario registra el bloqueo. |
| RF-04 | El módulo debe ejecutar los casos aplicables y comparar el resultado observado con el esperado. | `CU-04`, `P-04` | Cada caso intentado obtiene un resultado controlado asociado con la campaña. |
| RF-05 | El módulo debe registrar estado, versión, ambiente, ejecutor, datos y evidencia de cada ejecución. | `CU-05`, `P-04` | El registro permite reproducir y auditar el resultado sin depender de información verbal. |
| RF-06 | El módulo debe registrar y comunicar únicamente defectos reproducibles vinculados con el caso, requisito, evidencia, versión y módulo responsable. | `CU-06`, `P-05` | Un resultado no reproducible queda documentado, pero no se confirma como defecto. |
| RF-07 | El módulo debe permitir re-probar una corrección en la versión notificada y cerrar o reabrir el defecto con nueva evidencia. | `CU-07`, `P-06` | La decisión de cierre queda respaldada por el resultado de la re-prueba. |
| RF-08 | El módulo debe consolidar cobertura, estados, defectos, bloqueos, riesgos y evidencias al cerrar la campaña. | `CU-08`, `P-07` | El resumen permite conocer el estado funcional de la versión evaluada. |

## 3. Requisitos no funcionales

| ID | Categoría | Requisito | Medida verificable |
|---|---|---|---|
| RNF-01 | Privacidad | Toda documentación y evidencia académica debe utilizar datos ficticios y no identificables. | Ningún artefacto contiene nombres, expedientes, credenciales o datos clínicos reales. |
| RNF-02 | Trazabilidad | Cada ejecución debe relacionar campaña, caso, requisito, versión, ambiente, tenant, rol y evidencia. | El registro contiene todos los identificadores aplicables y permite seguir la relación en ambos sentidos. |
| RNF-03 | Reproducibilidad | Los pasos, precondiciones, datos y resultado esperado deben permitir repetir el caso en condiciones equivalentes. | Otro analista puede repetir el caso sin solicitar instrucciones que no estén documentadas. |
| RNF-04 | Integridad | Ningún caso se considera aprobado, fallido o bloqueado sin un registro de ejecución y evidencia correspondiente. | El cierre rechaza conclusiones sin respaldo y separa los casos no ejecutados. |
| RNF-05 | Seguridad | La ejecución debe respetar el tenant y el rol definidos y evitar exponer secretos en la evidencia. | La evidencia demuestra el contexto utilizado y no incluye tokens, contraseñas ni datos sensibles. |
| RNF-06 | Mantenibilidad | Los tipos de caso ejecutables deben cumplir un contrato común y sustituirse sin cambiar el algoritmo de la campaña. | `GestorCampana` usa `CasoRegresionEjecutable` sin preguntar por clases concretas. |
| RNF-07 | Consistencia | Los estados permitidos son `Aprobado`, `Fallido`, `Bloqueado` y `No ejecutado`, con el significado establecido en semana 1. | No aparecen estados equivalentes con nombres incompatibles y cada estado respeta su regla. |
| RNF-08 | Auditabilidad | La evidencia debe identificar fecha, ejecutor, versión y resultado observado, y permanecer accesible durante la revisión de la campaña. | El revisor puede consultar la evidencia desde el registro sin perder su contexto. |

## 4. Reglas transversales

- `Bloqueado` indica que se intentó ejecutar, pero una dependencia, dato o problema de
  ambiente impidió completar la prueba.
- `No ejecutado` indica que el caso no se intentó por alcance, prioridad o calendario.
- Un resultado diferente solo se registra como `Fallido` cuando existe evidencia
  suficiente y el comportamiento puede reproducirse.
- Las pruebas automatizadas, CI y despliegue pertenecen a ASII-25 y no forman parte de
  estos requisitos.
