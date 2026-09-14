# ADR-001: Arquitectura entregada del modulo 29

**Fecha:** 2026-08-21  
**Estado:** Aceptado y alineado con el MVP entregado  
**Decision:** `ManageQaRegression` orquesta el vertical mediante el puerto `QaRegressionRepository`, con adaptadores `InMemoryQaRegressionRepository` y `EloquentQaRegressionRepository`; PostgreSQL HOSPITAL es el limite de persistencia.

## Contexto

El modulo necesita registrar casos manuales vinculados con requisito y criterio de aceptacion, agruparlos en una campana, conservar una ejecucion con evidencia y gestionar el defecto que nace de un fallo. La re-prueba debe registrar la version corregida y una ejecucion nueva.

El alcance entregado es deliberadamente pequeno: cinco rutas, seis tablas QA y un caso de uso de aplicacion. No hay UI, automatizacion E2E, versionado de casos, carga fisica de evidencia ni sincronizacion federada.

## Decision

### 1. Capas implementadas

```text
HTTP
  QaRegressionController + cuatro FormRequest + rutas /api/v1/qa
                         |
                         v
Application
  ManageQaRegression + comandos + QaAuthorizer
                         |
                         v
Domain
  TestCase, RegressionCampaign, TestExecution, Evidence, Defect
  QaRegressionRepository
                         ^
                         |
Infrastructure
  InMemoryQaRegressionRepository
  EloquentQaRegressionRepository -> modelos Eloquent -> PostgreSQL local
  SpatieQaAuthorizer
```

- Las rutas aplican `tenant`, `jwt.auth` y `permission:qa.regression.manage`.
- `QaRegressionController` transforma requests en comandos y traduce excepciones conocidas.
- `ManageQaRegression` autoriza, crea entidades y coordina el flujo.
- Las entidades validan invariantes del dominio.
- `QaRegressionRepository` define las operaciones necesarias, no un CRUD generico.
- `EloquentQaRegressionRepository` filtra lecturas por `hospital_id` y define transacciones locales.
- `InMemoryQaRegressionRepository` permite probar el caso de uso sin PostgreSQL.

### 2. Contrato Repository real

El puerto entrega estas operaciones:

```text
saveTestCase
findTestCase
saveCampaign
findCampaign
recordExecution
findExecution
findDefect
recordRetest
campaignMatrix
```

`AppServiceProvider` enlaza `QaRegressionRepository` con `EloquentQaRegressionRepository` para ejecucion HTTP. El adaptador en memoria no participa en produccion.

### 3. Separacion de conceptos

| Concepto | Papel real |
|---|---|
| Repositorio Git | Conserva codigo y commits. El modulo solo recibe cadenas de version; no inspecciona Git. |
| Patron Repository | `QaRegressionRepository` desacopla `ManageQaRegression` de Eloquent. |
| Arquitectura federada | Distingue CENTRAL/HOSPITAL y la propiedad de datos; no es un Repository de software. |

### 4. Dominio minimo

- `TestCase` contiene hospital, `target_scope`, referencias logicas, titulo, precondiciones, lista de pasos, resultado esperado y creador.
- `RegressionCampaign` contiene hospital, nombre, `target_version`, IDs de casos y creador.
- `TestExecution` contiene estado, `actual_result`, ejecutor, una `Evidence` y enlaces opcionales de defecto/re-prueba.
- `Evidence` contiene ruta privada declarada, checksum SHA-256 declarado y metadatos saneados.
- `Defect` conserva ejecucion inicial, reproduccion, evidencia inicial, severidad, estado, version detectada, version corregida y cierre.
- Los estados de ejecucion son `PASSED`, `FAILED` y `BLOCKED`.
- Los estados de defecto son `OPEN`, `REOPENED` y `CLOSED`.

No se incorporan propiedades que el codigo no entrega: version/prioridad del caso, estado/ambiente de campana, instantanea de caso o version ejecutada.

### 5. Propiedad federada

- Los datos QA se escriben en la base HOSPITAL y llevan `hospital_id`.
- `target_scope=CENTRAL` expresa que el caso evalua una capacidad CENTRAL; no cambia la ubicacion del dato QA.
- `requirement_ref`, `acceptance_criterion_ref`, `target_version`, `detected_version` y `fixed_version` son cadenas logicas.
- No hay FKs, JOINs ni escrituras remotas hacia CENTRAL.
- Las FKs a `users` y `tenants` son locales y compartidas dentro de la misma base.
- La persistencia QA local continua aunque CENTRAL no este disponible.

### 6. Evidencia

El request y dominio validan una referencia declarativa:

- ruta que empieza con `private/qa/` y no termina en el propio prefijo;
- checksum con sintaxis SHA-256;
- `metadata.redacted=true`;
- `metadata.mime` y `metadata.size`;
- ausencia recursiva de las claves `password`, `token`, `authorization`, `cookie` y `secret`.

No se implementa un componente de almacenamiento. El modulo no carga el artefacto, no abre la ruta, no comprueba que exista y no recalcula el hash. Esta limitacion evita representar como verificada una evidencia que el MVP solo referencia.

### 7. Consistencia y transacciones

- Crear caso es un solo `INSERT` sin transaccion explicita del repositorio.
- Crear campana y asociaciones usa una transaccion.
- Registrar una ejecucion siempre inserta ejecucion y evidencia dentro de una transaccion.
- Una ejecucion inicial `FAILED` inserta tambien el defecto `OPEN` en esa transaccion.
- La validacion completa de una nueva ejecucion de re-prueba ocurre antes de mutar el estado del defecto.
- Una re-prueba bloquea el defecto con `lockForUpdate`, inserta ejecucion/evidencia y actualiza `fixed_version`, estado y `closed_at` en una transaccion.
- `PASSED` cierra; `FAILED` o `BLOCKED` reabre.
- La matriz ejecuta lecturas filtradas, sin prometer una instantanea transaccional global.

PostgreSQL es el unico participante transaccional. CENTRAL y un eventual almacenamiento fisico de archivos no forman parte de la transaccion.

### 8. Persistencia real

`EloquentQaRegressionRepository` usa:

- `qa_test_cases`;
- `qa_regression_campaigns`;
- `qa_campaign_cases`;
- `qa_test_executions`;
- `qa_evidences`;
- `qa_defects`.

La migracion usa UUID para entidades QA, JSON para pasos/metadatos, FKs locales y los indices/unicos descritos en `ESPECIFICACION.md`. La restriccion unica `(hospital_id, execution_id)` de `qa_evidences` refuerza el contrato de una evidencia por ejecucion. No entrega JSONB ni `CHECK` de enums o estructura de evidencia. Para evitar un ciclo de insercion, `qa_defects.initial_execution_id` es una referencia logica unica y `qa_test_executions (hospital_id, defect_id)` conserva la FK compuesta hacia el defecto.

### 9. Integracion futura

Outbox/inbox no esta implementado y no aparece como componente operativo. Si en el futuro se publican indicadores QA agregados, esa capacidad requerira su propio contrato, privacidad, reintentos e idempotencia. La operacion actual no depende de ella.

## Alternativas descartadas

| Alternativa | Motivo |
|---|---|
| Reglas y SQL en el controller | Rompe la separacion ya implementada y dificulta usar el adaptador en memoria. |
| CRUD generico | No expresa `recordExecution`, `recordRetest` ni `campaignMatrix`. |
| Persistir QA en CENTRAL | Haría depender el registro operativo de la red y del nodo CENTRAL. |
| FK remota a requisitos | Las referencias son logicas y pueden pertenecer a modulos federados. |
| Sobrescribir la ejecucion inicial | Perderia la relacion entre fallo y re-prueba. |
| Endpoint separado de version corregida | No fue entregado; `fixed_version` pertenece al payload de re-prueba. |
| Servicio de carga de evidencia | No fue entregado; solo se valida y persiste la referencia. |
| Outbox/inbox dentro del MVP | No existe implementacion ni necesidad para confirmar la transaccion local. |

## Consecuencias

### Positivas

- El flujo principal queda probado con un repositorio en memoria y con PostgreSQL.
- Fallo, evidencia y defecto no quedan parcialmente escritos.
- La re-prueba preserva la ejecucion original y serializa cambios concurrentes del defecto.
- El aislamiento por hospital se aplica en caso de uso y consultas del repositorio.
- El MVP funciona sin CENTRAL.

### Limitaciones aceptadas

- No existe versionado ni instantanea de casos.
- La evidencia se confia como referencia declarada, no como archivo verificado.
- La base no impone `CHECK` de estados, alcance o metadatos.
- `initial_execution_id` en defectos es una referencia logica local sin FK; su unicidad por hospital y la transaccion preservan la relacion.
- La matriz se construye mediante varias consultas y no es una instantanea aislada.
- No hay idempotencia ni integracion asincrona.

## Verificacion

La decision se verifica con pruebas de dominio, pruebas de `ManageQaRegression`, pruebas HTTP de las cinco rutas y pruebas de integracion de `EloquentQaRegressionRepository` sobre PostgreSQL. Este ADR describe esos componentes sin ampliar su contrato.
