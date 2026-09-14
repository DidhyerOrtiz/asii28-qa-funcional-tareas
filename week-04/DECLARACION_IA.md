# Declaración de uso de inteligencia artificial

## Identificación

| Dato | Valor |
|---|---|
| Estudiante responsable de la entrega | Didhyer Alexander Ortiz Guevara |
| GitHub | `DidhyerOrtiz` |
| Asignación | Variante `mod29`, antecedente histórico issue `#28` |
| Herramienta asistida por IA | OpenCode |
| Modelo | OpenAI GPT-5.6-sol |
| Rama revisada | `feat/mod29-qa-regresion` |
| Tip de código y pruebas validado | `80f8f37` |

## Alcance del uso

Se utilizó OpenCode con OpenAI GPT-5.6-sol como herramienta de apoyo para analizar el repositorio, proponer y revisar una solución por capas, diagnosticar fallos de compatibilidad con PostgreSQL, relacionar pruebas con criterios de aceptación y preparar documentación técnica. La herramienta tuvo acceso al worktree local y ejecutó comandos de Git, PHP, PHPUnit, Artisan, Pint y Docker bajo supervisión del proceso de trabajo.

Esta declaración no afirma que el estudiante haya escrito personalmente cada línea. La IA participó en producción y revisión de contenido; la autoría académica y la responsabilidad de aceptar, comprender, probar y presentar el resultado corresponden al estudiante.

## Propósitos concretos

- Análisis de la arquitectura existente y del límite federado CENTRAL/HOSPITAL.
- Diseño e implementación del vertical de QA con dominio, aplicación, puerto Repository, adaptadores, HTTP y persistencia.
- Revisión de código para detectar contratos aspiracionales o no respaldados por la implementación.
- Diagnóstico de la migración sobre PostgreSQL 17 y de la restricción compuesta autorreferente.
- Diseño y ejecución de pruebas unitarias, de aplicación, HTTP y de integración PostgreSQL.
- Auditoría final de atomicidad InMemory, cardinalidad de evidencia, índices y coherencia de los seis Mermaid.
- Elaboración de la matriz de regresión, evidencia reproducible, informe formal y esta declaración.
- Comprobación de coherencia entre código, pruebas, migración, rutas, ADR, especificación y diagramas.

## Prompts representativos

Los textos siguientes resumen instrucciones representativas; no se presentan como una transcripción íntegra de todas las interacciones:

1. **Análisis arquitectónico:** “Analiza el repositorio y delimita una solución mínima para QA funcional y regresión, respetando la separación CENTRAL/HOSPITAL y sin inventar integración distribuida.”
2. **Implementación:** “Implementa casos, campañas, ejecuciones, evidencia, defectos y re-pruebas por capas, usando un puerto Repository y persistencia local por hospital.”
3. **Revisión de código:** “Contrasta especificación, ADR, diagramas y código; identifica afirmaciones aspiracionales que no estén realmente implementadas.”
4. **Diagnóstico PostgreSQL:** “Reproduce la migración en PostgreSQL real, explica `SQLSTATE 42830` y corrige la FK compuesta sin modificar otro módulo.”
5. **Pruebas:** “Cubre dominio, caso de uso, API, aislamiento entre tenants, metadatos sensibles y atomicidad en PostgreSQL.”
6. **Documentación:** “Genera una matriz ejecutada, evidencia reproducible y un informe formal basado solo en hechos actuales, manteniendo visible el defecto y su re-prueba.”

## Contenido aceptado y modificado

Se aceptaron, después de revisión y ejecución, aportes relativos a:

- separación entre entidades de dominio, `ManageQaRegression` y `QaRegressionRepository`;
- adaptadores `InMemoryQaRegressionRepository` y `EloquentQaRegressionRepository`;
- reglas de trazabilidad, estados de ejecución/defecto y evidencia saneada;
- cinco rutas protegidas bajo `/api/v1/qa`;
- migración con seis tablas `qa_*` y pruebas de integración PostgreSQL;
- validación previa a la mutación del defecto y unicidad de una evidencia por ejecución;
- documentación de arquitectura, matriz, defecto, re-prueba y limitaciones.

El contenido sugerido fue modificado cuando excedía el alcance o no coincidía con el código. Se retiraron o evitaron afirmaciones sobre versionado o snapshots de casos, outbox, idempotencia, carga o verificación física de evidencia y automatización E2E. También se ajustaron descripciones de claves, transacciones y rutas después de inspeccionar los archivos vigentes.

## Errores y problemas detectados durante el uso

### 1. Desajuste de documentación aspiracional

En una etapa de análisis aparecieron descripciones más amplias que la implementación real. Algunas sugerencias trataban como entregadas capacidades futuras, por ejemplo snapshots/versionado, outbox, idempotencia o verificación del archivo de evidencia. La corrección consistió en volver a contrastar cada afirmación con entidades, migración, repositorios, rutas y pruebas. Los documentos finales declaran esas capacidades como limitaciones, no como funcionalidades.

También se observó que una versión inicial de la especificación y el ADR describía al revés dos relaciones respecto de la migración. La revisión final corrigió los documentos para reflejar el código: `qa_defects.initial_execution_id` queda como enlace lógico único y `qa_test_executions.defect_id` participa en una FK compuesta por hospital.

### 2. Orden de FK compuesta en PostgreSQL

La primera validación de persistencia real falló con `SQLSTATE 42830`. La FK compuesta autorreferente de ejecuciones no coincidía en orden con la clave única disponible. SQLite no había hecho visible de la misma manera esta incompatibilidad. El defecto se registró como `QA-DEF-001`, severidad `HIGH`, versión `mod29-0.1.0-pre`; se corrigió en `0e5aa7c` sin cambiar otro módulo y se re-probó en `80f8f37` sobre PostgreSQL 17.

### 3. Conexión `central` de Stancl en pruebas SQLite

Durante la preparación de pruebas se identificó que el modelo `Tenant` de Stancl espera una conexión llamada `central`. En el entorno SQLite, una configuración incompleta puede fallar antes de probar la lógica del módulo. Esto se diagnosticó como un asunto de configuración del entorno de pruebas, no como un defecto funcional de Modulo29. La validación se realizó alineando `central` con la conexión de prueba correspondiente; para PostgreSQL se apuntaron tanto `DB_*` como `CENTRAL_DB_*` a `shi_mod29_test`.

## Validación realizada por la herramienta

- Inspección del historial desde `0050f4f` hasta `80f8f37`.
- Ejecución Modulo29: 18 pruebas, 52 aserciones, PASS.
- Revisión final Modulo29: 21 pruebas, 63 aserciones, PASS.
- Ejecución Modulo20: 13 pruebas, 30 aserciones, PASS.
- `migrate:fresh --seed --force` en `shi_mod29_test`: 12 migraciones y todos los seeders, PASS.
- Prueba PostgreSQL focal final: 2 pruebas, 13 aserciones, PASS en dos repeticiones consecutivas.
- `git diff --check`: PASS.
- Pint focal de Modulo29: PASS.
- Revisión manual de rutas, autorización, aislamiento por hospital, privacidad, transacciones y limitaciones.

## Validación humana esperada

Antes de presentar o integrar la entrega, se espera que Didhyer Alexander Ortiz Guevara:

- lea y comprenda el código y los documentos;
- confirme que la matriz representa las ejecuciones realizadas;
- revise que el marcador de commit final se reemplace al confirmar la entrega;
- compruebe visualmente los seis diagramas cuando se rendericen fuera de esta entrega;
- pueda explicar el patrón Repository, la federación y el defecto `QA-DEF-001`;
- decida finalmente qué contenido acepta como parte de su entrega académica.

## Datos y credenciales

No se proporcionaron a la IA credenciales reales, secretos institucionales ni datos reales de pacientes. Las pruebas usaron entidades ficticias y credenciales efímeras de un contenedor Docker aislado. Los documentos usan marcadores como `<CONTRASENA_EFIMERA_NO_REAL>` y no contienen valores secretos.

## Declaración final

OpenCode con OpenAI GPT-5.6-sol fue una herramienta sustantiva de asistencia, no una fuente infalible ni un reemplazo de validación. Sus propuestas fueron contrastadas con el repositorio y con ejecuciones reproducibles. Los errores encontrados se conservaron como parte de la trazabilidad del proceso en vez de ocultarse, y la aprobación académica final permanece bajo responsabilidad humana.
