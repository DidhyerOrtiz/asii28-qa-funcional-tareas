# Matriz de regresión ejecutada - Módulo 29

## Identificación

| Dato | Valor |
|---|---|
| Estudiante | Didhyer Alexander Ortiz Guevara |
| GitHub | `DidhyerOrtiz` |
| Variante | `mod29` |
| Antecedente histórico | Issue `#28` |
| Rama | `feat/mod29-qa-regresion` |
| Base | `origin/develop` en `0050f4f` |
| Tip de código y pruebas verificado | `80f8f37` |
| Corte reforzado | Entrega final preparada a partir de `f52d969` |
| Fecha de ejecución final | 2026-09-13 |
| Entorno | PHP 8.3.31, Laravel 12.58.0, PHPUnit 11.5.55 y PostgreSQL 17 aislado en Docker |

## Convenciones

- **PASS:** el resultado observado cumple el requisito y el criterio de aceptación.
- **FAILED:** se reprodujo una desviación respecto del resultado esperado.
- **FIXED:** se aplicó una corrección identificable.
- Las referencias `RN-*` y `CA-*` corresponden a `ESPECIFICACION.md`.
- Los identificadores de esta matriz son registros documentales de QA; no sustituyen los UUID generados por la aplicación.

## Matriz ejecutada

| ID | Caso y datos relevantes | Requisito / aceptación | Resultado esperado | Resultado observado | Estado | Evidencia |
|---|---|---|---|---|---|---|
| QA-REG-001 | Crear caso `REQ-29` / `AC-29-1`, alcance `CENTRAL`, y campaña con ese caso. | RN-01, RN-03, RN-04, RN-05; CA-03, CA-04 | El caso conserva ambas referencias y la campaña contiene el caso del hospital autenticado. | El caso fue creado y recuperado con `requirement_ref=REQ-29`; la campaña quedó asociada al caso y la API devolvió 201 en el flujo completo. | **PASS** | `ManageQaRegressionTest::test_creates_traceable_case_and_campaign_with_attached_case`; `QaRegressionHttpTest::test_complete_failed_execution_retest_and_matrix_flow`; corrida Mod29: 21 pruebas, 63 aserciones. |
| QA-REG-002 | Registrar una ejecución inicial `FAILED` con evidencia privada, pasos de reproducción, severidad `HIGH` y versión detectada. | RN-07, RN-08, RN-09; CA-07, CA-08 | Ejecución, evidencia y defecto `OPEN` se crean como una unidad; no queda persistencia parcial. | El caso de uso creó el defecto enlazado y preservó la evidencia. PostgreSQL confirmó las tres filas y rechazó atómicamente una ejecución cuya membresía campaña-caso era inválida. | **PASS** | `ManageQaRegressionTest::test_failed_execution_atomically_creates_preserved_defect`; `PostgreSqlQaRepositoryIntegrationTest::test_failed_execution_and_defect_are_persisted_atomically`; defecto HTTP `OPEN`. |
| QA-REG-003 | Intentar asociar un caso de `hospital-1` desde `hospital-2` y usar un JWT con el contexto de otro tenant. | RN-05, RN-15, RN-16; CA-04, CA-15 | No se consulta, asocia ni modifica información de otro hospital. | Aplicación devolvió `QaNotFoundException` para el caso ajeno; HTTP rechazó con 403 el contexto tenant que no corresponde al JWT. | **PASS** | `ManageQaRegressionTest::test_cross_tenant_case_cannot_be_attached`; `QaRegressionHttpTest::test_token_cannot_use_another_tenant_context`. |
| QA-REG-004 | Enviar `metadata.request.token` dentro de la evidencia. | RN-17, RN-18; CA-09 | La validación recursiva rechaza claves sensibles sin persistir el payload. | Dominio lanzó `QaValidationException` y HTTP respondió 422 con error en `evidence.metadata`. | **PASS** | `QaDomainTest::test_evidence_rejects_sensitive_nested_metadata`; `QaRegressionHttpTest::test_sensitive_nested_evidence_metadata_returns_422`. |
| QA-REG-005 | Re-probar un defecto abierto con `fixed_version=29.0.1` y resultado `FAILED`. | RN-11, RN-12, RN-13; CA-10, CA-11 | Se crea una nueva ejecución; el defecto queda `REOPENED`, conserva `fixed_version` y no registra cierre. | Estado `REOPENED`, versión `29.0.1` y `closed_at=null`; la ejecución inicial no fue sobrescrita. | **PASS** | `QaDomainTest::test_failed_retest_reopens_defect_and_passed_retest_closes_it`; `ManageQaRegressionTest::test_failed_retest_reopens_defect_and_preserves_fixed_version`. |
| QA-REG-006 | Ejecutar la migración y probar persistencia atómica en PostgreSQL 17. Versión detectada: `mod29-0.1.0-pre`. | Persistencia PostgreSQL y límites transaccionales; RN-08; CA-08 | Las tablas y FKs se crean en PostgreSQL y el repositorio guarda ejecución, evidencia y defecto de forma atómica. | La primera ejecución falló con `SQLSTATE 42830` por incompatibilidad de orden entre la FK compuesta autorreferente y su clave única. Se corrigió en `0e5aa7c`; la re-prueba inicial pasó en `80f8f37` y la revisión final amplió la cobertura PostgreSQL a 2 pruebas y 13 aserciones. | **FAILED → FIXED → PASS** | Registro `QA-DEF-001`; `2026_08_21_000029_create_qa_regression_tables.php`; `PostgreSqlQaRepositoryIntegrationTest`; `migrate:fresh --seed --force` con 12 migraciones y todos los seeders en PASS. |
| QA-REG-007 | Intentar una re-prueba `PASSED` con ruta de evidencia pública. | RN-11, RN-13, RN-17; CA-09, CA-11 | La operación responde 422 y el defecto almacenado continúa `OPEN`, sin versión corregida ni fecha de cierre. | La nueva ejecución se validó antes de mutar el defecto; InMemory conservó `OPEN`, `fixed_version=null` y `closed_at=null`. | **PASS** | `ManageQaRegressionTest::test_invalid_retest_does_not_mutate_stored_defect`. |
| QA-REG-008 | Persistir re-prueba aprobada y luego intentar una segunda evidencia para la misma ejecución. | RN-11, RN-12, RN-13, RN-17; CA-10, CA-11, CA-13 | La re-prueba cierra el defecto y la base rechaza cardinalidad mayor que una evidencia por ejecución. | PostgreSQL guardó re-prueba y `fixed_version`, la matriz mostró `PASSED`, el filtro tenant ocultó el defecto a otro hospital y el único `(hospital_id, execution_id)` rechazó la evidencia duplicada. | **PASS** | `PostgreSqlQaRepositoryIntegrationTest::test_retest_and_single_evidence_constraint_are_persisted`. |

## Registro de defecto QA-DEF-001

| Campo | Registro |
|---|---|
| ID | `QA-DEF-001` |
| Título | PostgreSQL rechaza la FK compuesta autorreferente de ejecuciones QA |
| Tipo | Defecto de implementación detectado antes de publicación |
| Severidad | **HIGH** |
| Estado inicial | `OPEN` |
| Versión detectada | `mod29-0.1.0-pre` |
| Entorno | PostgreSQL 17 en contenedor Docker aislado; base dedicada `shi_mod29_test` |
| Resultado esperado | La migración crea las seis tablas `qa_*` y permite verificar persistencia atómica. |
| Resultado real | PostgreSQL detuvo la creación de la FK con `SQLSTATE 42830`: la clave única disponible no coincidía en orden con la referencia compuesta autorreferente. |
| Impacto | La migración no podía completarse en PostgreSQL; por tanto, la persistencia real del módulo no era publicable. No afectó datos reales ni otro módulo. |
| Corrección | Alinear el orden de la clave compuesta autorreferente con la restricción única de ejecuciones dentro de la migración del Módulo 29. |
| Versión corregida | Commit `0e5aa7c` (`feat(mod29): persiste regresion en PostgreSQL`) |
| Estado final | `CLOSED` después de re-prueba satisfactoria en `80f8f37` |

### Pasos de reproducción originales

1. Iniciar PostgreSQL 17 en un contenedor aislado.
2. Configurar la conexión hacia la base destructible `shi_mod29_test`.
3. Ejecutar `php -d extension=pdo_pgsql artisan migrate:fresh --force`.
4. Observar la creación de las tablas del Módulo 29.
5. Verificar que PostgreSQL termina la migración con `SQLSTATE 42830` al crear la referencia compuesta autorreferente de `qa_test_executions`.

### Evidencia del defecto

- Mensaje diagnóstico principal: `SQLSTATE 42830`.
- Motor que hizo visible el defecto: PostgreSQL 17.
- Área afectada: migración `2026_08_21_000029_create_qa_regression_tables.php`.
- Causa: orden no coincidente entre la referencia compuesta autorreferente y la clave única que debía respaldarla.
- Alcance de la corrección: Módulo 29 únicamente; no se modificó otro módulo para resolverlo.

## Registro de re-prueba QA-RETEST-001

| Campo | Registro |
|---|---|
| Defecto re-probado | `QA-DEF-001` |
| Tip probado | `80f8f37` |
| Condición de seguridad | `QA_PG_DATABASE=shi_mod29_test` y `QA_PG_ALLOW_MIGRATE_FRESH=1` |
| Comando focal | `php -d extension=pdo_pgsql vendor/bin/phpunit tests/Feature/Modulo29/PostgreSqlQaRepositoryIntegrationTest.php` |
| Resultado focal inicial | **PASS**, 1 prueba PostgreSQL, 6 aserciones en `80f8f37` |
| Resultado focal final | **PASS**, 2 pruebas PostgreSQL, 13 aserciones |
| Verificación complementaria | `php -d extension=pdo_pgsql artisan migrate:fresh --seed --force` |
| Resultado complementario | **PASS**, 12 migraciones, incluida `2026_08_21_000029`, y todos los seeders |
| Regresión completa Mod29 | **PASS**, 21 pruebas y 63 aserciones |
| Regresión Modulo20 | **PASS**, 13 pruebas y 30 aserciones |
| Conclusión | La migración y la persistencia atómica funcionan en PostgreSQL 17; el defecto permanece cerrado. |

## Resultado global

Los ocho casos terminaron conformes. `QA-REG-006` conserva tanto el fallo inicial como su corrección y re-prueba para no convertir una incidencia real de implementación en un PASS retrospectivo. La revisión final agregó `QA-REG-007` y `QA-REG-008` para demostrar atomicidad observable entre adaptadores y cardinalidad de evidencia en PostgreSQL. No se utilizaron datos de pacientes ni credenciales reales.
